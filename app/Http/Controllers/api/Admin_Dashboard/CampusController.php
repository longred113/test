<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\CampusResource;
use App\Models\Campus;
use GrahamCampbell\ResultType\Success;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class CampusController extends Controller
{
    protected Request $request;

    public function __construct(
        Request $request
    ) {
        $this->request = $request;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $campusesData = CampusResource::collection(Campus::all());
        return $this->successCampusRequest($campusesData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = validator::make($this->request->all(), [
            'name' => 'required|string',
            'indicated' => 'required|string',
            // 'contact' => 'string',
            // 'signedDate' => 'date',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $campusId = IdGenerator::generate(['table' => 'campuses', 'trow' => 'campusId', 'length' => 7, 'prefix' => 'CP']);
        $email = $this->request['name'] . '@gmail.com';
        $userPassword = Str::random(8);
        $params = [
            'campusId' => $campusId,
            'name' => $this->request['name'],
            'indicated' => $this->request['indicated'],
            'contact' => $this->request['contact'],
            'signedDate' => $this->request['signedDate'],
            'activate' => $this->request['activate'],
        ];
        $userParams = [
            'campusId' => $campusId,
            'email' => $email,
            'password' => $userPassword,
        ];
        $newCampus = new CampusResource(Campus::create($params));
        // UserController::store($userParams);
        return $this->successCampusRequest($newCampus);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($campusId)
    {
        $campus = Campus::find($campusId);
        $campusData = new CampusResource($campus);
        return $this->successCampusRequest($campusData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($campusId)
    {
        $campus = Campus::find($campusId);
        if (empty($this->request['name'])) {
            $this->request['name'] = $campus['name'];
        }
        if (empty($this->request['indicated'])) {
            $this->request['indicated'] = $campus['indicated'];
        }
        if (empty($this->request['contact'])) {
            $this->request['contact'] = $campus['contact'];
        }
        if (empty($this->request['signedDate'])) {
            $this->request['signedDate'] = $campus['signedDate'];
        }
        if (empty($this->request['activate'])) {
            $this->request['activate'] = $campus['activate'];
        }

        $validator = validator::make($this->request->all(), [
            'name' => 'required|string',
            'indicated' => 'required|string',
            'contact' => 'string',
            'activate' => 'required'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $campus['name'] = $this->request['name'],
            $campus['indicated'] = $this->request['indicated'],
            $campus['contact'] = $this->request['contact'],
            $campus['activate'] = $this->request['activate'],
        ];
        $newInfoCampus = $campus->update($params);
        return $this->successCampusRequest($newInfoCampus);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($campusId)
    {
        $campus = Campus::find($campusId);
        $deleteCampus = $campus->delete();
        return $this->successCampusRequest($deleteCampus);
    }

    public function switchActivate()
    {
        $validator = validator::make($this->request->all(), [
            'campusId' => 'string|required_without:campusIds',
            'campusIds' => 'array|required_without:campusId'
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        if (!empty($this->request->get('campusId'))) {
            $ids[] = $this->request->get('campusId');
        } else {
            $ids = $this->request->get('campusIds');
        }
        $campuses = Campus::find(($ids));
        foreach ($campuses as $campus) {
            if ($campus['activate'] == 1) {
                Campus::where('campusId', $campus['campusId'])->update(['activate' => 0]);
            } else {
                Campus::where('campusId', $campus['campusId'])->update(['activate' => 0]);
            }
        }
        return $this->successCampusRequest();
    }
}
