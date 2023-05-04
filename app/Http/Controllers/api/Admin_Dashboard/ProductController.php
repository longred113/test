<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Products;
use App\Http\Resources\Products as ProductsResource;
use App\Models\Packages;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
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
    }

    public function getAllProductHavePackage()
    {
        try {
            $products = Products::leftjoin('product_packages', 'products.productId', '=', 'product_packages.productId')
                ->leftjoin('packages', 'product_packages.packageId', '=', 'packages.packageId')
                ->leftJoin('matched_activities', 'products.productId', '=', 'matched_activities.productId')
                ->selectRaw(
                    'products.productId,
                    products.name,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":",packages.packageId,packages.name )) as packages,
                    MAX(products.level) as level,
                    products.activate,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":",matched_activities.matchedActivityId, matched_activities.name)) as matchedActivities'
                )
                ->groupBy('products.productId')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductsRequest($products);
    }

    public function getProductAndMatchActivity()
    {
        try {
            $products = Products::leftJoin('product_packages', 'products.productId', '=', 'product_packages.productId')
                ->leftJoin('packages', 'product_packages.packageId', '=', 'packages.packageId')
                ->leftJoin('product_matched_activities', 'products.productId', '=', 'product_matched_activities.productId')
                ->selectRaw(
                    'products.productId,
                    products.name,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":",packages.packageId,packages.name )) as packages,
                    MAX(products.level) as level,
                    products.activate,
                    GROUP_CONCAT(DISTINCT CONCAT_WS(":", product_matched_activities.matchedActivityId, product_matched_activities.matchedActivityName)) as matchedActivities'
                )
                ->groupBy('products.productId')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductsRequest($products);
    }

    public function getProductByLevel($level)
    {
        $products = Products::where('level', $level)->get();
        return $this->successProductsRequest($products);
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
            'level' => 'required',
            // 'details' => 'required',
            // 'image' => 'image|mimes:jpg,png,jpeg,gif,svg|max:5048',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $productId = IdGenerator::generate(['table' => 'products', 'trow' => 'productId', 'length' => 7, 'prefix' => 'PD']);
        // $name = $this->request->file('image')->getClientOriginalName();
        // $image_path = Cloudinary::upload($this->request->file('image')->getRealPath())->getSecurePath();
        $params = [
            'productId' => $productId,
            'name' => $this->request['name'],
            'level' => $this->request['level'],
            'details' => $this->request['details'],
            // 'image' => $image_path,
            'activate' => $this->request['activate'],
        ];
        if(!empty($this->request['duration'])){
            $params['duration'] = $this->request['duration'];
        }
        if(!empty($this->request['type'])){
            $params['type'] = $this->request['type'];
        }
        if(!empty($this->request['startDate'])){
            $params['startDate'] = $this->request['startDate'];
        }
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
        if (empty($this->request['name'])) {
            $this->request['name'] = $products['name'];
        }
        if (empty($this->request['level'])) {
            $this->request['level'] = $products['level'];
        }
        if (empty($this->request['details'])) {
            $this->request['details'] = $products['details'];
        }
        if (empty($this->request['image'])) {
            $this->request['image'] = $products['image'];
        }
        if (empty($this->request['activate'])) {
            $this->request['activate'] = $products['activate'];
        }
        if (empty($this->request['duration'])) {
            $this->request['duration'] = $products['duration'];
        }
        if (empty($this->request['type'])) {
            $this->request['type'] = $products['type'];
        }
        if (empty($this->request['startDate'])) {
            $this->request['startDate'] = $products['startDate'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'required|string',
            'level' => 'required',
            // 'details' => 'required',
            // 'image' => 'required',
            'activate' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $products['productId'] = $this->request['productId'],
            $products['name'] = $this->request['name'],
            $products['level'] = $this->request['level'],
            $products['details'] = $this->request['details'],
            $products['image'] = $this->request['image'],
            $products['duration'] = $this->request['duration'],
            $products['startDate'] = $this->request['startDate'],
            $products['type'] = $this->request['type'],
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
        if (empty($request->packageId)) {
            $request['packageId'] = $products['packageId'];
        }
        $validator = validator::make($request->all(), [
            'packageId' => 'required|integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
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
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $inputProductId = $this->request['productId'];
        $product = Products::find($inputProductId);
        $inputPackageId = $this->request['packageId'];
        $newPackageId = $product->update(['packageId' => $inputPackageId]);
        return $this->successProductsRequest($newPackageId);
    }
}
