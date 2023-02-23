<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Http\Resources\Products as ProductsResource;
use App\Models\Packages;
use Facade\Ignition\Support\Packagist\Package;
use Haruncpi\LaravelIdGenerator\IdGenerator;

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
        // $joinData = Products::join('packages','products.packageId', '=', 'packages.packageId')->get();
        // return $joinData;
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
            'name' => 'required',
            // 'startLevel' => 'required',
            // 'level' => 'required',
            // 'endLevel' => 'required',
            // 'details' => 'required',
            // 'image' => 'required',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        
        $productId = IdGenerator::generate(['table'=>'products', 'trow' => 'productId', 'length' => 7, 'prefix' => 'PD']);
        $params = [
            'productId' => $productId,
            'packageId' => $this->request['packageId'],
            'name' => $this->request['name'],
            'level' => $this->request['level'],
            'startLevel' => $this->request['startLevel'],
            'endLevel' => $this->request['endLevel'],
            'details' => $this->request['details'],
            'image' => $this->request['image'],
            'activate' => $this->request['activate'],
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
    public function update($productId)
    {
        $products = Products::find($productId);
        if(empty($this->request['packageId'])) {
            $this->request['packageId'] = $products['packageId'];
        }
        if (empty($this->request['name'])) {
            $this->request['name'] = $products['name'];
        }
        // if(empty($this->request['startLevel'])) {
        //     $this->request['startLevel'] = $products['startLevel'];
        // }
        // if(empty($this->request['level'])) {
        //     $this->request['level'] = $products['level'];
        // }
        // if(empty($this->request['endLevel'])) {
        //     $this->request['endLevel'] = $products['endLevel'];
        // }
        // if(empty($this->request['details'])) {
        //     $this->request['details'] = $products['details'];
        // }
        // if(empty($this->request['image'])) {
        //     $this->request['image'] = $products['image'];
        // }
        // if (empty($this->request['activate'])) {
        //     $this->request['activate'] = $products['activate'];
        // }
        $validator = Validator::make($this->request->all(), [
            'packageId' => 'integer',
            'name' => 'required|string',
            // 'startLevel' => 'required|string',
            // 'level' => 'required',
            // 'endLevel' => 'required',
            // 'details' => 'required',
            // 'image' => 'required',
            // 'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }

        $params = [
            $products['productId'] = $this->request['productId'],
            $products['packageId'] = $this->request['packageId'],
            $products['name'] = $this->request['name'],
            $products['startLevel'] = $this->request['startLevel'],
            $products['level'] = $this->request['level'],
            $products['endLevel'] = $this->request['endLevel'],
            $products['details'] = $this->request['details'],
            $products['image'] = $this->request['image'],
            $products['activate'] = $this->request['activate'],
        ];
        $newInfoProducts = $products->update($params);
        return $this->successProductsRequest($newInfoProducts);
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
            'packageId' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $validator->errors();
        }
        $params = [
            $products['packageId'] = $request['packageId'],
        ];
        $newAddPackage = $products->update($params);
        return $this->successProductsRequest($newAddPackage);
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