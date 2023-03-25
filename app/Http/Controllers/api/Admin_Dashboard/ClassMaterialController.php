<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassMaterialResource;
use App\Models\ClassMaterials;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
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
        try{
            $classMaterialsData = ClassMaterials::join('classes','class_materials.class', '=', 'classes.classId')
            ->select(
                'class_materials.classMaterialId',
                'class_materials.writer',
                'class_materials.title',
                'class_materials.view',
                'class_materials.date',
                'classes.classId',
                'classes.name as className',
            )
            ->get();
            return $this->successClassMaterialRequest($classMaterialsData);
        }catch(Exception $e){
            return $e->getMessage();
        }
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
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classMaterialId = IdGenerator::generate(['table' => 'class_materials', 'trow' => 'classMaterialId', 'length' => 7, 'prefix' => 'CM']);
        $params = [
            'classMaterialId' => $classMaterialId,
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
        return $this->successClassMaterialRequest($classMaterialData);
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
        $classMaterial = ClassMaterials::find($classMaterialId);
        if (empty($this->request['writer'])) {
            $this->request['writer'] = $classMaterial['writer'];
        }
        if (empty($this->request['class'])) {
            $this->request['class'] = $classMaterial['class'];
        }
        if (empty($this->request['title'])) {
            $this->request['title'] = $classMaterial['title'];
        }
        if (empty($this->request['view'])) {
            $this->request['view'] = $classMaterial['view'];
        }
        if (empty($this->request['date'])) {
            $this->request['date'] = $classMaterial['date'];
        }
        $validator = Validator::make($this->request->all(), [
            'writer' => 'string|required',
            'class' => 'string|required',
            'title' => 'string|required',
            'view' => 'integer|required',
            'date' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $classMaterial['writer'] = $this->request['writer'],
            $classMaterial['class'] = $this->request['class'],
            $classMaterial['title'] = $this->request['title'],
            $classMaterial['view'] = $this->request['view'],
            $classMaterial['date'] = $this->request['date'],
        ];

        $newInfoClassMaterial = $classMaterial->update($params);
        return $this->successClassMaterialRequest($newInfoClassMaterial);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $classMaterialId
     * @return \Illuminate\Http\Response
     */
    public function destroy($classMaterialId)
    {
        $classMaterial = ClassMaterials::find($classMaterialId);
        $deleteClassMaterial = $classMaterial->delete();
        return $this->successClassMaterialRequest($deleteClassMaterial);
    }
}
