<?php

namespace App\Http\Controllers\api\Message_Dashboard;

use App\Events\AllGroupMessage;
use App\Events\GroupClassMessage;
use App\Events\NewChatMessage;
use App\Events\PrivateMessage;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrivateMessageResource;
use App\Models\Chat;
use App\Models\Students;
use App\Models\Teachers;
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

        if (!empty($this->request['classId'])) {
            event(new GroupClassMessage($this->request['message'], $this->request['classId']));
        }
        if (!empty($this->request['studentId'])) {
            event(new PrivateMessage($this->request['message'], $this->request['studentId']));
        }
        if (empty($this->request['classId']) && empty($this->request['studentId'])) {
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
            'message' => 'text|required',
            'studentId' => 'string',
            'teacherId' => 'string',
        ]);
        if ($validator->failed()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try {
            if (!empty($this->request['studentId'])) {
                $studentName = Students::where('studentId', $this->request['studentId'])->get('name');
                $studentNameConvert = $studentName->pluck('name')->toArray();
                $userName = implode(', ', $studentNameConvert);
            }
            if (!empty($this->request['teacherId'])) {
                $teacherName = Teachers::where('teacherId', $this->request['teacherId'])->get('name');
                $teacherNameConvert = $teacherName->pluck('name')->toArray();
                $userName = implode(', ', $teacherNameConvert);
            }
            if (empty($this->request['studentId']) && empty($this->request['teacherId'])) {
                $userName = 'Admin';
            }
            $params = [
                'userName' => $userName,
                'message' => $this->request['message'],
                'studentId' => $this->request['studentId'],
                'teacherId' => $this->request['teacherId'],
            ];
            $chat = Chat::create($params);

            broadcast(new NewChatMessage($chat))->toOthers();
            return response()->json(['status' => 'Message Sent!']);
        } catch (Exception $e) {
            return $e->getMessage();
        }
    }

    public function getAllMessage()
    {
        $getAllMessage = Chat::whereNull('studentId')->get();
        return $getAllMessage;
    }

    public function getStudentMessage($studentId)
    {
        $getStudentMessage = Chat::where('studentId', $studentId)->get();
        // $getStudentMessages = new PrivateMessageResource($getStudentMessage);
        // $getAllMessage = Chat::orderBy('id','DESC')->get();
        return $getStudentMessage;
    }

    public function getTeacherMessage($teacherId)
    {
        $getTeacherMessage = Chat::where('teacherId', $teacherId)->get();
        // $getAllMessage = Chat::orderBy('id','DESC')->get();
        return $getTeacherMessage;
    }

    public function getAdminMessage()
    {
        $getAdminMessage = Chat::where('userName', 'Admin')->get();
        return $getAdminMessage;
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
