<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductEnrollmentResource;
use App\Models\ProductEnrollments;
use App\Models\StudentProducts;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductEnrollmentController extends Controller
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
        $productEnrollments = ProductEnrollmentResource::collection(ProductEnrollments::all());
        return $this->successProductEnrollmentRequest($productEnrollments);
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
            'enrollmentId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $productEnrollmentId = IdGenerator::generate(['table' => 'product_enrollments', 'trow' => 'productEnrollmentId', 'length' => 7, 'prefix' => 'PE']);
        $params = [
            'productEnrollmentId' => $productEnrollmentId,
            'productId' => $this->request['productId'],
            'enrollmentId' => $this->request['enrollmentId'],
            'date' => Carbon::now(),
        ];
        $newProductEnrollmentData = new ProductEnrollmentResource(ProductEnrollments::create($params));
        return $this->successProductEnrollmentRequest($newProductEnrollmentData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($productEnrollmentId)
    {
        $productEnrollment = ProductEnrollments::find($productEnrollmentId);
        $productEnrollmentData = new ProductEnrollmentResource($productEnrollment);
        return $this->successProductEnrollmentRequest($productEnrollmentData);
    }

    public function getProduct()
    {
        $validator = Validator::make($this->request->all(), [
            'enrollmentId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $productEnrollment = ProductEnrollments::where('enrollmentId', $this->request['enrollmentId'])->get();
        return $this->successProductEnrollmentRequest($productEnrollment);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($productEnrollmentId)
    {
        $productEnrollment = ProductEnrollments::find($productEnrollmentId);
        if(empty($this->request['productId'])){
            $this->request['productId'] = $productEnrollment['productId'];
        }
        if(empty($this->request['enrollmentId'])){
            $this->request['enrollmentId'] = $productEnrollment['enrollmentId'];
        }
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'enrollmentId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $productEnrollment['productId'] = $this->request['productId'],
            $productEnrollment['enrollmentId'] = $this->request['enrollmentId'],
            $productEnrollment['date'] = Carbon::now(),
        ];
        $newInfo = $productEnrollment->update($params);
        return $this->successProductEnrollmentRequest($newInfo);
    }

    public function updateProductOfEnrollment()
    {
        $validator = Validator::make($this->request->all(), [
            'enrollmentId' => 'string|required',
            'productIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try{
            $enrollmentId = $this->request['enrollmentId'];
            $productIds = $this->request['productIds'];
            $productEnrollment = ProductEnrollments::where('enrollmentId', $enrollmentId)->delete();
            foreach($productIds as $productId){
                $productEnrollmentId = IdGenerator::generate(['table' => 'product_enrollments', 'trow' => 'productEnrollmentId', 'length' => 7, 'prefix' => 'PE']);
                $productEnrollmentParams = [
                    'productEnrollmentId' => $productEnrollmentId,
                    'enrollmentId' => $enrollmentId,
                    'productId' => $productId,
                    'date' => Carbon::now(),
                ];
                ProductEnrollments::create($productEnrollmentParams);
            }
        }catch(Exception $e){
            return $e->getMessage();
        }
        return $this->successProductEnrollmentRequest();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productEnrollmentId)
    {
        $productEnrollment = ProductEnrollments::find($productEnrollmentId);
        $deleteProductEnrollment = $productEnrollment->delete();
        return $this->successProductEnrollmentRequest($deleteProductEnrollment);
    }
}
