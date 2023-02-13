<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Validator;
use App\Models\CampusManager;
use App\Http\Resources\CampusManager as CampusManagerResource;

class CampusManagerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = CampusManager::all();
        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'name' => 'required', 
            'email' => 'require',
            'gender' => 'require',
            'dateOfBirth' => 'require',
            'country' => 'require',
            'timeZone' => 'require',
            'startDate' => 'require',
            'resignation' => 'require',
            'campusId' => 'require',
            'memo' => 'require',
        ]);
        if($validator->fails()){
            $arr = [
                'success' => false,
                'message' => 'Lỗi kiểm tra dữ liệu',
                'data' => $validator->errors()
            ];
            return response() ->json($arr, 200);
        }
        $campusManager = CampusManager::create($input);
        $arr = [
            'status' => true,
            'message' => "Sản phẩm lưu thành công",
            'data' => new CampusManagerResource($campusManager)
        ];
        return response()->json($arr, 201);
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
    public function update(Request $request, $id)
    {
        //
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