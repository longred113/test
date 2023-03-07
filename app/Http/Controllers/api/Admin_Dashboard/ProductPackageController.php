<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductPackageResource;
use App\Models\ProductPackages;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductPackageController extends Controller
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
        $productPackagesData = ProductPackageResource::collection(ProductPackages::all());
        return $this->successClassProductRequest($productPackagesData);
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
            'packageId' => 'integer|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $productPackageId = IdGenerator::generate(['table'=>'product_packages', 'trow' => 'productPackageId', 'length' => 8, 'prefix' => 'CPK']);
        $params = [
            'productPackageId' => $productPackageId,
            'productId' => $this->request['productId'],
            'packageId' => $this->request['packageId'],
            'status' => $this->request['status'],
        ];

        $newProductPackage = new ProductPackageResource(ProductPackages::create($params));
        return $this->successProductPackageRequest($newProductPackage);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($productPackageId)
    {
        $productPackage = ProductPackages::find($productPackageId);
        $productPackageData = new ProductPackageResource($productPackage);
        return $this->successProductPackageRequest($productPackageData);
    }

    public function displayByProduct($productId)
    {
        $productPackage = ProductPackages::where('productId', $productId)->get();
        return $this->successProductPackageRequest($productPackage);
    }

    public function displayByPackage($packageId)
    {
        $productPackage = ProductPackages::where('packageId', $packageId)->get();
        return $this->successProductPackageRequest($productPackage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($productPackageId)
    {
        $productPackage = ProductPackages::find($productPackageId);
        if(empty($this->request['productId'])){
            $this->request['productId'] = $productPackage['productId'];
        }
        if(empty($this->request['packageId'])){
            $this->request['packageId'] = $productPackage['packageId'];
        }
        if(empty($this->request['status'])){
            $this->request['status'] = $productPackage['status'];
        }
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'packageId' => 'integer|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $productPackage['productId'] = $this->request['productId'],
            $productPackage['packageId'] = $this->request['packageId'],
            $productPackage['status'] = $this->request['status'],
        ];
        $newInfoProductPackageId = $productPackage->update($params);
        return $this->successProductPackageRequest($newInfoProductPackageId);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productPackageId)
    {
        $productPackage = ProductPackages::find($productPackageId);
        $deleteProductPackage = $productPackage->delete();
        return $this->successProductPackageRequest($deleteProductPackage);
    }
}
