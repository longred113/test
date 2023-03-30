<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\MatchedActivities;
use App\Models\ProductMatchedActivities;
use App\Models\Products;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductMatchedActivityController extends Controller
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
        $productMatchActivities = ProductMatchedActivities::all();
        return $this->successProductMatchActivityRequest($productMatchActivities);
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
            'matchedActivityIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try {
            foreach ($this->request['matchedActivityIds'] as $matchedActivityId) {
                $productMatchedActivityId = IdGenerator::generate(['table' => 'product_matched_activities', 'trow' => 'productMatchedActivityId', 'length' => 8, 'prefix' => 'PMA']);
                $params = [
                    'productMatchedActivityId' => $productMatchedActivityId,
                    'productId' => $this->request['productId'],
                ];
                $productName = Products::where('productId', $this->request['productId'])->pluck('name')->toArray();
                $params['productName'] = implode(', ', $productName);
                $params['matchedActivityId'] = $matchedActivityId;
                $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchedActivityId)->pluck('name')->toArray();
                $params['matchedActivityName'] = implode(', ', $matchedActivityName);

                $newProductMatchActivity = ProductMatchedActivities::create($params);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductMatchActivityRequest();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateMatchedActivity()
    {
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'matchedActivityIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $productId = $this->request['productId'];
            $matchedActivityIds = $this->request['matchedActivityIds'];
            $productMatchActivities = ProductMatchedActivities::where('productId', $productId)->delete();
            foreach ($matchedActivityIds as $matchedActivityId) {
                $productMatchedActivityId = IdGenerator::generate(['table' => 'product_matched_activities', 'trow' => 'productMatchedActivityId', 'length' => 8, 'prefix' => 'PMA']);
                $params = [
                    'productMatchedActivityId' => $productMatchedActivityId,
                    'productId' => $this->request['productId'],
                ];
                $productName = Products::where('productId', $this->request['productId'])->pluck('name')->toArray();
                $params['productName'] = implode(', ', $productName);
                $params['matchedActivityId'] = $matchedActivityId;
                $matchedActivityName = MatchedActivities::where('matchedActivityId', $matchedActivityId)->pluck('name')->toArray();
                $params['matchedActivityName'] = implode(', ', $matchedActivityName);

                ProductMatchedActivities::create($params);
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successProductMatchActivityRequest();
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
