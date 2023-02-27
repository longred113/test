<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentProductResource;
use App\Models\StudentProducts;
use App\Models\Students;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StudentProductController extends Controller
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
        $studentProductsData = StudentProductResource::collection(StudentProducts::all());
        return $this->successStudentProductRequest($studentProductsData);
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
            'productId' => 'string|required',
            'studentId' => 'string|required',
        ]);
        if($validator->fails()){
            return $validator->errors();
        }

        $studentProductId = IdGenerator::generate(['table'=>'student_products', 'trow' => 'studentProductId', 'length' => 7, 'prefix' => 'SP']);
        $params = [
            'studentProductId' => $studentProductId,
            'productId' => $this->request['productId'],
            'studentId' => $this->request['studentId'],
        ];

        $newStudentProduct = new StudentProductResource(StudentProducts::create($params));
        return $this->successStudentProductRequest($newStudentProduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentProductId)
    {
        $studentProduct = StudentProducts::find($studentProductId);
        $studentProductData = new StudentProductResource($studentProduct);
        return $this->successStudentProductRequest($studentProductData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($studentProductId)
    {
        $studentProduct = StudentProducts::find($studentProductId);
        if(empty($this->request['studentId'])){
            $this->request['studentId'] = $studentProduct['studentId'];
        }
        if(empty($this->request['productId'])){
            $this->request['productId'] = $studentProduct['productId'];
        }
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'studentId' => 'string|required',
        ]);
        if($validator->fails()){
            return $validator->errors();
        }

        $params = [
            $studentProduct['studentId'] = $this->request['studentId'],
            $studentProduct['productId'] = $this->request['productId'],
        ];
        $newInfStudentProduct = $studentProduct->update($params);
        return $this->successStudentProductRequest($newInfStudentProduct);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentProductId)
    {
        $studentProduct = StudentProducts::find($studentProductId);
        $deleteStudentProduct = $studentProduct->delete();
        return $this->successStudentProductRequest($deleteStudentProduct);
    }
}
