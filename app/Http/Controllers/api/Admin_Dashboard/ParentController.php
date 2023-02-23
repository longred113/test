<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ParentResource;
use App\Models\Parents;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'name' => 'string|required',
            'email' => 'string|required|unique:parents',
            'phone' => 'string|required',
            'studentId' => 'string|required_without:studentIds',
            'studentIds' => 'array|required_without:studentId',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $parentId = IdGenerator::generate(['table'=>'parents', 'trow' => 'parentId', 'length' => 7, 'prefix' => 'PA']);
        if (!empty($this->request->get('studentId'))) {
            $studentIds[] = $this->request->get('studentId');
        } else {
            $studentIds = $this->request->get('studentIds');
        }
        $params = [
            'parentId' => $parentId,
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'phone' => $this->request['phone'],
        ];
        $newParentData = new ParentResource(Parents::create($params));
        foreach ($studentIds as $studentId) {
            
        }
        return $this->successParentRequest($newParentData);
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
        if(empty($this->request['name'])) {
            $this->request['name'] = $parent['name'];
        }
        if(empty($this->request['email'])) {
            $this->request['email'] = $parent['email'];
        }
        if(empty($this->request['phone'])) {
            $this->request['phone'] = $parent['phone'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'email' => 'string|required|unique:parents',
            'phone' => 'string|required',
            'studentId' => 'string|required_without:studentIds',
            'studentIds' => 'array|required_without:studentId',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        if (!empty($this->request->get('studentId'))) {
            $studentIds[] = $this->request->get('studentId');
        } else {
            $studentIds = $this->request->get('studentIds');
        }
        $params = [
            $parent['name'] = $this->request['name'],
            $parent['email'] = $this->request['email'],
            $parent['phone'] = $this->request['phone'],
            $parent['studentIds'] = $studentIds,
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
