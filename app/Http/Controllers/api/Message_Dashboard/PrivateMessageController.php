<?php

namespace App\Http\Controllers\api\Message_Dashboard;

use App\Events\AllGroupMessage;
use App\Events\GroupClassMessage;
use App\Events\PrivateMessage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class PrivateMessageController extends Controller
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
    public function sendMessages()
    {
        $validator = Validator::make($this->request->all(), [
            'classId' => 'string',
            'studentId' => 'string',
            'message' => 'string',
        ]);
        if ($validator->failed()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        if(!empty($this->request['classId'])) {
            var_dump(1);
            event(new GroupClassMessage($this->request['message'], $this->request['classId']));
        }
        if(!empty($this->request['studentId'])) {
            var_dump(2);
            event(new PrivateMessage($this->request['message'], $this->request['studentId']));
        }
        if(empty($this->request['classId']) && empty($this->request['studentId'])) {
            var_dump(3);
            event(new AllGroupMessage($this->request['message']));
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
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
