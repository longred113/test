<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\UnitResource;
use App\Models\Units;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UnitController extends Controller
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
        $unitsData = UnitResource::collection(Units::all());
        return $this->successUnitRequest($unitsData);
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
            'unitId' => 'string|required|unique:units',
            // 'productId' => 'string|required',
            'name' => 'string|required',
            // 'startDate' => 'required',
            // 'endDate' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $unitId = IdGenerator::generate(['table' => 'units', 'trow' => 'unitId', 'length' => 7, 'prefix' => 'UN']);
        $params = [
            'unitId' => $unitId,
            'productId' => $this->request['productId'],
            'name' => $this->request['name'],
            'startDate' => $this->request['startDate'],
            'endDate' => $this->request['endDate'],
        ];
        $newUnitData = new UnitResource(Units::create($params));
        return $this->successUnitRequest($newUnitData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($unitId)
    {
        $unit = Units::find($unitId);
        $unitData = new UnitResource($unit);
        return $this->successUnitRequest($unitData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($unitId)
    {
        $unit = Units::find($unitId);
        if (empty($this->request['productId'])) {
            $this->request['productId'] = $unit['productId'];
        }
        if (empty($this->request['name'])) {
            $this->request['name'] = $unit['name'];
        }
        if (empty($this->request['startDate'])) {
            $this->request['startDate'] = $unit['startDate'];
        }
        if (empty($this->request['endDate'])) {
            $this->request['endDate'] = $unit['endDate'];
        }
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'name' => 'string|required',
            // 'startDate' => 'required',
            // 'endDate' => 'required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $params = [
            $unit['productId'] = $this->request['productId'],
            $unit['name'] = $this->request['name'],
            $unit['startDate'] = $this->request['startDate'],
            $unit['endDate'] = $this->request['endDate'],
        ];

        $newInfoUnit = $unit->update($params);
        return $this->successUnitRequest($newInfoUnit);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($unitId)
    {
        $unit = Units::find($unitId);
        $deleteUnit = $unit->delete();
        return $this->successUnitRequest($deleteUnit);
    }
}
