<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Http\Resources\Products as ProductsResource;
use App\Models\Packages;
use Facade\Ignition\Support\Packagist\Package;

class ProductController extends Controller
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
        $data = ProductsResource::collection(Products::all());
        return $this->successProductsRequest($data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = validator::make($request->all(), [
            'productId' => 'required|unique:products',
            'packageId' => 'required',
            'name' => 'required',
            // 'startLevel' => 'required',
            // 'endLevel' => 'required',
            // 'details' => 'required',
            // 'image' => 'required',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            'productId' => request('productId'),
            'packageId' => request('packageId'),
            'name' => request('name'),
            'startLevel' => request('startLevel'),
            'endLevel' => request('endLevel'),
            'details' => request('details'),
            'image' => request('image'),
            'activate' => request('activate'),
        ];
        $newProducts = new ProductsResource(Products::create($params));
        return $this->successProductsRequest($newProducts);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($productId)
    {
        $Products = Products::find($productId);
        $ProductsData = new ProductsResource($Products);
        return $this->successProductsRequest($ProductsData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $productId)
    {
        $products = Products::find($productId);
        // if(empty($request->packageId)) {
        //     $request['packageId'] = $products['packageId'];
        // }
        if (empty($request->name)) {
            $request['name'] = $products['name'];
        }
        // if(empty($request->startLevel)) {
        //     $request['startLevel'] = $products['startLevel'];
        // }
        // if(empty($request->endLevel)) {
        //     $request['endLevel'] = $products['endLevel'];
        // }
        // if(empty($request->details)) {
        //     $request['details'] = $products['details'];
        // }
        // if(empty($request->image)) {
        //     $request['image'] = $products['image'];
        // }
        // if (empty($request->activate)) {
        //     $request['activate'] = $products['activate'];
        // }
        $validator = validator::make($request->all(), [
            'packageId' => 'required|integer',
            'name' => 'required|string',
            // 'startLevel' => 'required|string',
            // 'endLevel' => 'required',
            // 'details' => 'required',
            // 'image' => 'required',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $products['productId'] = $request['productId'],
            $products['packageId'] = $request['packageId'],
            $products['name'] = $request['name'],
            $products['startLevel'] = $request['startLevel'],
            $products['endLevel'] = $request['endLevel'],
            $products['details'] = $request['details'],
            $products['image'] = $request['image'],
            $products['activate'] = $request['activate'],
        ];
        $newInfoProducts = $products->update($params);
        return $this->productsRequest($newInfoProducts);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($productId)
    {
        $product = Products::find($productId);
        $deleteProducts = $product->delete();
        return $this->successProductsRequest($deleteProducts);
    }
    public function addPackages(Request $request, $productId)
    {
        $products = Products::find($productId);
        if(empty($request->packageId)) {
            $request['packageId'] = $products['packageId'];
        }
        $validator = validator::make($request->all(), [
            'packageId' => 'required|string',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            $products['packageId'] = $request['packageId'],
        ];
        $newAddpakage = $products->update($params);
        return $this->successProductsRequest($newAddpakage);
    }

    public function updatePackage()
    {
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'packageId' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $inputProductId = $this->request['productId'];
        $product = Products::find($inputProductId);
        $inputPackageId = $this->request['packageId'];
        $newPackageId = $product->update(['packageId'=>$inputPackageId]);
        return $this->successProductsRequest($newPackageId);
    }
}
