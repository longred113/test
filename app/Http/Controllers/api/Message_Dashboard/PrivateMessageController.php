<?php

namespace App\Http\Controllers\api\Message_Dashboard;

use App\Events\AllGroupMessage;
use App\Events\GroupClassMessage;
use App\Events\NewChatMessage;
use App\Events\PrivateMessage;
use App\Http\Controllers\Controller;
use App\Models\Chat;
use Exception;
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
            event(new GroupClassMessage($this->request['message'], $this->request['classId']));
        }
        if(!empty($this->request['studentId'])) {
            event(new PrivateMessage($this->request['message'], $this->request['studentId']));
        }
        if(empty($this->request['classId']) && empty($this->request['studentId'])) {
            event(new AllGroupMessage($this->request['message']));
        }
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
            'username' => 'string|required',
            'message' => 'text|required',
        ]);
        if ($validator->failed()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try{
            $params = [
                'userName' => $this->request['userName'],
                'message' => $this->request['message'],
            ];
            $chat = Chat::create($params);
        } catch(Exception $e) {
            return $e->getMessage();
        }
        broadcast(new NewChatMessage($chat))->toOthers();
        return response()->json(['status' => 'Message Sent!']);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
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
