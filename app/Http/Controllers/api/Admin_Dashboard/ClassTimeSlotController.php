<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassTimes;
use App\Models\ClassTimeSlots;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassTimeSlotController extends Controller
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
            $classTimeSlotData = ClassTimeSlots::all();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successClassTimeSlotRequest($classTimeSlotData);
    }

    public function getByName($name)
    {
        try {
            $classTimeSlotData = ClassTimeSlots::where('name', $name)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successClassTimeSlotRequest($classTimeSlotData);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        try {
            $validator = Validator::make($this->request->all(), [
                'name' => 'string|required|unique:class_time_slots',
                'classStart' => 'string|required',
                'classEnd' => 'string|required',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }

            $classTimeSlotId = IdGenerator::generate(['table' => 'class_time_slots', 'trow' => 'classTimeSlotId', 'length' => 8, 'prefix' => 'CTS']);
            $params = [
                'classTimeSlotId' => $classTimeSlotId, // 'CTS00001
                'name' => $this->request->name,
                'classStart' => Carbon::parse($this->request->classStart)->format('H:i:s'),
                'classEnd' => Carbon::parse($this->request->classEnd)->format('H:i:s'),
            ];
            $newClassTimeSlot = ClassTimeSlots::create($params);
            return $this->successClassTimeSlotRequest($newClassTimeSlot);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classTimeSlotId)
    {
        $classTimeSlotData = ClassTimeSlots::where('classTimeSlotId', $classTimeSlotId)->first();
        return $this->successClassTimeSlotRequest($classTimeSlotData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classTimeSlotId)
    {
        $classTimeSlotData = ClassTimeSlots::where('classTimeSlotId', $classTimeSlotId)->first();
        $oldName = $classTimeSlotData->name;
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            // 'classStart' => 'string',
            // 'classEnd' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        if (!empty($this->request->name)) {
            $params['name'] = $this->request->name;
            $classTimeParams['classTimeSlot'] = $this->request->name;
        }
        if (!empty($this->request->classStart)) {
            $params['classStart'] = Carbon::parse($this->request->classStart)->format('H:i:s');
        }
        if (!empty($this->request->classEnd)) {
            $params['classEnd'] = Carbon::parse($this->request->classEnd)->format('H:i:s');
        }
        $classTimeSlotData->update($params);
        ClassTimes::where('classTimeSlot', $oldName)->update($classTimeParams);
        return $this->successClassTimeSlotRequest($classTimeSlotData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classTimeSlotId)
    {
        $deleteClassTimeSlot = ClassTimeSlots::where('classTimeSlotId', $classTimeSlotId)->first();
        if (!empty($deleteClassTimeSlot)) {
            $deleteClassTimeSlot->delete();
            return $this->successClassTimeSlotRequest($deleteClassTimeSlot);
        } else {
            return $this->errorBadRequest('Class Time Slot not found');
        }
    }
}
