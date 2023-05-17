<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Holidays;
use DateInterval;
use DateTime;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HolidayController extends Controller
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
            $holidayData = Holidays::all();
        } catch (Exception $e) {
            return ($e->getMessage());
        }

        return $this->successHolidayRequest($holidayData);
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
            'name' => 'string|required|unique:holidays',
            'timeZone' => 'string|required',
            'startDate' => 'date|required',
            'endDate' => 'date|required',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try {
            $holidayId = IdGenerator::generate(['table' => 'holidays', 'trow' => 'holidayId', 'length' => 7, 'prefix' => 'HD']);
            $startDate = new DateTime($this->request['startDate']);
            $endDate = new DateTime($this->request['endDate']);
            $duration = $endDate->diff($startDate)->days + 1;
            $days = [];
            while ($startDate <= $endDate) {
                $day = strtoupper($startDate->format('D'));
                $days[] = $day;
                $startDate->add(new DateInterval('P1D')); // add this to increment by 1 day
            }
            $params = [
                'holidayId' => $holidayId,
                'name' => $this->request['name'],
                'timeZone' => $this->request['timeZone'],
                'startDate' => $this->request['startDate'],
                'endDate' => $this->request['endDate'],
                'duration' => $duration,
            ];
            $newHoliday = Holidays::create($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successHolidayRequest($newHoliday);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($holidayId)
    {
        try {
            $holidayData = Holidays::where('holidayId', $holidayId)->first();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successHolidayRequest($holidayData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($holidayId)
    {
        $validator = Validator::make($this->request->all(), [
            'name' => 'string|unique:holidays',
            'timeZone' => 'string',
            'startDate' => 'date',
            'endDate' => 'date',
        ]);

        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $holidayData = Holidays::where('holidayId', $holidayId)->first();
            if (!empty($this->request['name'])) {
                $params['name'] = $this->request['name'];
            }
            if (!empty($this->request['timeZone'])) {
                $params['timeZone'] = $this->request['timeZone'];
            }
            if (!empty($this->request['startDate'])) {
                $params['startDate'] = $this->request['startDate'];
            }
            if (!empty($this->request['endDate'])) {
                $params['endDate'] = $this->request['endDate'];
            }
            if (!empty($this->request['startDate']) && !empty($this->request['endDate'])) {
                $startDate = new DateTime($this->request['startDate']);
                $endDate = new DateTime($this->request['endDate']);
                $duration = $endDate->diff($startDate)->days + 1;
                $params['duration'] = $duration;
            }
            if (!empty($this->request['startDate']) && empty($this->request['endDate'])) {
                $startDate = new DateTime($this->request['startDate']);
                $endDate = new DateTime($holidayData->endDate);
                $duration = $endDate->diff($startDate)->days + 1;
                $params['duration'] = $duration;
            }
            if (empty($this->request['startDate']) && !empty($this->request['endDate'])) {
                $startDate = new DateTime($holidayData->startDate);
                $endDate = new DateTime($this->request['endDate']);
                $duration = $endDate->diff($startDate)->days + 1;
                $params['duration'] = $duration;
            }
            $holidayData->update($params);
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successHolidayRequest($holidayData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($holidayId)
    {
        try {
            $holidayData = Holidays::where('holidayId', $holidayId)->first();
            $holidayData->delete();
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successHolidayRequest($holidayData);
    }

    public function getAvailableHolidays()
    {
        $validator = Validator::make($this->request->all(), [
            'startDate' => 'date|required',
            'productIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try {
            $classStartDate = $this->request['startDate'];
            $productNumber = count($this->request['productIds']);
            $classEndDate = date('Y-m-d', strtotime($classStartDate . ' + ' . $productNumber * 2 . ' months'));
            $holidays = Holidays::all();
            $availableHolidays = [];
            foreach ($holidays as $holiday) {
                $holidayStartDate = $holiday->startDate;
                $holidayEndDate = $holiday->endDate;
                if ($classStartDate <= $holidayStartDate && $classEndDate >= $holidayEndDate) {
                    $availableHolidays[] = $holiday;
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }

        return $this->successHolidayRequest($availableHolidays);
    }
}
