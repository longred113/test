<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassMaterialResource;
use App\Models\ClassMaterials;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassMaterialController extends Controller
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
        $classMaterialsData = ClassMaterialResource::collection(ClassMaterials::all());
        return $this->successClassMaterialRequest($classMaterialsData);
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
            'writer' => 'string|required',
            'class' => 'string|required',
            'title' => 'string|required',
            'view' => 'integer|required',
            'date' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            'writer' => $this->request['writer'],
            'class' => $this->request['class'],
            'title' => $this->request['title'],
            'view' => $this->request['view'],
            'date' => $this->request['date'],
        ];

        $newClassMaterialData = new ClassMaterialResource(ClassMaterials::create($params));
        return $this->successClassMaterialRequest($newClassMaterialData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $classMaterialId
     * @return \Illuminate\Http\Response
     */
    public function show($classMaterialId)
    {
        $classMaterial = ClassMaterials::find($classMaterialId);
        $classMaterialData = new ClassMaterialResource($classMaterial);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classMaterialId
     * @return \Illuminate\Http\Response
     */
    public function update($classMaterialId)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $classMaterialId
     * @return \Illuminate\Http\Response
     */
    public function destroy($classMaterialId)
    {
        //
    }
}
