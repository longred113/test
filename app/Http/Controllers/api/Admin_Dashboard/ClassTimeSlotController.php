<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\ClassTimeslot;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ClassTimeSlotController extends Controller
{
    protected Request $request;

    Public function __construct(
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
        try{
            $classTimeSlotData = ClassTimeSlot::all();
        }catch(Exception $e){
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
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|required',
            'classStart' => 'string|required',
            'classEnd' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try{
            $classTimeSlotId = IdGenerator::generate(['table' => 'class_time_slots', 'trow' => 'classTimeSlotId', 'length' => 8, 'prefix' => 'CTS']);
            $params = [
                'classTimeSlotId' => $classTimeSlotId, // 'CTS00001
                'name' => $this->request->name,
                'classStart' => Carbon::parse($this->request->classStart)->format('H:i:s'),
                'classEnd' => Carbon::parse($this->request->classEnd)->format('H:i:s'),
            ];
            $newClassTimeSlot = ClassTimeSlot::create($params);
            return $this->successClassTimeSlotRequest($newClassTimeSlot);
        }catch(Exception $e){
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
        $classTimeSlotData = ClassTimeSlot::where('classTimeSlotId', $classTimeSlotId)->first();
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
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'classStart' => 'string',
            'classEnd' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        if(!empty($this->request->name)){
            $params['name'] = $this->request->name;
        }
        if(!empty($this->request->classStart)){
            $params['classStart'] = Carbon::parse($this->request->classStart)->format('H:i:s');
        }
        if(!empty($this->request->classEnd)){
            $params['classEnd'] = Carbon::parse($this->request->classEnd)->format('H:i:s');
        }
        $classTimeSlotData = ClassTimeSlot::where('classTimeSlotId', $classTimeSlotId)->first();
        $classTimeSlotData->update($params);
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
        $deleteClassTimeSlot = ClassTimeSlot::where('classTimeSlotId', $classTimeSlotId)->first();
        if (!empty($deleteClassTimeSlot)) {
            $deleteClassTimeSlot->delete();
            return $this->successClassTimeSlotRequest($deleteClassTimeSlot);
        } else {
            return $this->errorBadRequest('Class Time Slot not found');
        }
    }
}
