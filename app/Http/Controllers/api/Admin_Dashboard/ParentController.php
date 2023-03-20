<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParentResource;
use App\Models\Parents;
use App\Models\Students;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\VarDumper\VarDumper;

class ParentController extends Controller
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
        $parentsData = ParentResource::collection(Parents::all());
        return $this->successParentRequest($parentsData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = Validator::make($this->request->all(), [
            'firstName' => 'string|required',
            'lastName' => 'string|required',
            'email' => 'string|required|unique:parents',
            'password' => 'string',
            'phone' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $parentId = IdGenerator::generate(['table' => 'parents', 'trow' => 'parentId', 'length' => 7, 'prefix' => 'PA']);
        $params = [
            'parentId' => $parentId,
            'firstName' => $this->request['firstName'],
            'lastName' => $this->request['lastName'],
            'email' => $this->request['email'],
            'phone' => $this->request['phone'],
        ];
        $userParams = [
            'parentId' => $parentId,
            'name' => $this->request['lastName'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
        ];
        $newParentData = new ParentResource(Parents::create($params));
        UserController::store($userParams);
        return $this->successParentRequest($newParentData);
    }

    public function addParentIntoStudent($parentId)
    {
        $validator = Validator::make($this->request->all(), [
            'studentIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $students = Students::find($this->request['studentIds']);
        foreach($students as $student) {
            Students::where('studentId', $student['studentId'])->update(['parentId' => $parentId]);
        }
        return $this->successStudentRequest();
    } 

    /**
     * Display the specified resource.
     *
     * @param  int  $parentId
     * @return \Illuminate\Http\Response
     */
    public function show($parentId)
    {
        $parent = Parents::find($parentId);
        $parentData = new ParentResource($parent);
        return $this->successParentRequest($parentData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $parentId
     * @return \Illuminate\Http\Response
     */
    public function update($parentId)
    {
        $parent = Parents::find($parentId);
        if (empty($this->request['firstName'])) {
            $this->request['firstName'] = $parent['firstName'];
        }
        if (empty($this->request['lastName'])) {
            $this->request['lastName'] = $parent['lastName'];
        }
        if (empty($this->request['email'])) {
            $this->request['email'] = $parent['email'];
        }
        if (empty($this->request['phone'])) {
            $this->request['phone'] = $parent['phone'];
        }
        $validator = Validator::make($this->request->all(), [
            'firstName' => 'string|required',
            'lastName' => 'string|required',
            'email' => 'string|required|unique:parents',
            'phone' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $params = [
            $parent['firstName'] = $this->request['firstName'],
            $parent['lastName'] = $this->request['lastName'],
            $parent['email'] = $this->request['email'],
            $parent['phone'] = $this->request['phone'],
        ];

        $newInfoParentData = $parent->update($params);
        return $this->successParentRequest($newInfoParentData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $parentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($parentId)
    {
        $parent = Parents::find($parentId);
        $deleteParent = $parent->delete();
        return $this->successParentRequest($deleteParent);
    }
}
