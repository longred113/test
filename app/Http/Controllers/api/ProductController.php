<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Http\Resources\Products as ProductsResource;
class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = ProductsResource::collection(Products::all());
        return $this->productsRequest($data);
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
        return $newProducts;
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
        return $this->productsRequest($ProductsData);
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
        if(empty($request->name)) {
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
            // 'packageId' => 'required|string',
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
        return $this->successRequest($deleteProducts);
    }
}