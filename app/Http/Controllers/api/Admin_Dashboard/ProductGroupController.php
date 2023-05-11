<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\GroupActivities;
use App\Models\MatchedActivities;
use App\Models\ProductGroups;
use App\Models\Products;
use App\Models\TblGroups;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductGroupController extends Controller
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
        try {
            $productGroups = Products::leftJoin('product_groups', 'products.productId', '=', 'product_groups.productId')
                ->leftJoin('group_activities', 'product_groups.groupId', '=', 'group_activities.groupId')
                ->select(
                    'products.productId',
                    'products.name',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":", group_activities.groupId, group_activities.groupName) SEPARATOR ",") as `groups`'),
                 )
                ->groupBy('products.productId')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductGroupRequest($productGroups);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public static function store($productGroupParams)
    {
        // $validator = Validator::make($this->request->all(), [
        //     'productId' => 'string|required',
        //     'groupIds' => 'required|array',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }

        try {
            $productName = Products::where('productId', $productGroupParams['productId'])->first()->name;
            foreach ($productGroupParams['groupIds'] as $groupId) {
                $productGroupId = IdGenerator::generate(['table' => 'product_groups', 'trow' => 'productGroupId', 'length' => 7, 'prefix' => 'PG']);
                $groupName = TblGroups::where('groupId', $groupId)->first()->name;
                $params = [
                    'productGroupId' => $productGroupId,
                    'productId' => $productGroupParams['productId'],
                    'productName' => $productName,
                    'groupId' => $groupId,
                    'groupName' => $groupName,
                ];
                $productGroup[] = ProductGroups::create($params);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        // return $this->successProductGroupRequest($productGroup);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($productId)
    {
        try {
            $product = Products::where('productId', $productId)->first();
            $product->group = ProductGroups::join('group_activities', 'product_groups.groupId', '=', 'group_activities.groupId')
                ->where('productId', $productId)
                ->select(
                    'group_activities.groupId',
                    'group_activities.groupName',
                    DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",group_activities.matchedActivityId, group_activities.matchedActivityName)) as activities')
                )
                ->groupBy('group_activities.groupId', 'group_activities.groupName')
                ->get();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductGroupRequest($product);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update()
    {
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'groupIds' => 'required|array',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $productName = Products::where('productId', $this->request->productId)->first()->name;
            ProductGroups::where('productId', $this->request->productId)->delete();
            foreach ($this->request->groupIds as $groupId) {
                $productGroupId = IdGenerator::generate(['table' => 'product_groups', 'trow' => 'productGroupId', 'length' => 7, 'prefix' => 'PG']);
                $groupName = TblGroups::where('groupId', $groupId)->first()->name;
                $params = [
                    'productGroupId' => $productGroupId,
                    'productId' => $this->request->productId,
                    'productName' => $productName,
                    'groupId' => $groupId,
                    'groupName' => $groupName,
                ];
                $productGroup[] = ProductGroups::create($params);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductGroupRequest($productGroup);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
