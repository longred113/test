<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Events\ChatMessageSent;
use App\Events\SendAllAnnounced;
use App\Events\SendStudentAnnounced;
use App\Events\SendTeacherAnnounced;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassBoardResource;
use App\Models\ClassBoards;
use App\Models\Students;
use App\Models\Teachers;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Pusher\Pusher;

class ClassBoardController extends Controller
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
        $classBoardsData = ClassBoardResource::collection(ClassBoards::all());
        return $this->successClassBoardRequest($classBoardsData);
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
            'message' => 'string',
            'title' => 'string',
            'teacherIds' => 'array|required_without:studentIds',
            'studentIds' => 'array|required_without:teacherIds',
            'date' => 'date',
            'type' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $classBoardId = IdGenerator::generate(['table' => 'class_boards', 'trow' => 'classBoardId', 'length' => 7, 'prefix' => 'CB']);
        if(!empty($this->request['teacherIds'])) {
            $paramTeachers = [
                'classBoardId' => $classBoardId,
                'message' => $this->request['message'],
                'title' => $this->request['title'],
                'date' => $this->request['date'],
                'type' => $this->request['type'],
            ];
            try{
                foreach($this->request['teacherIds'] as $teacher) {
                    $teachersName = Teachers::where('teacherId', $teacher)->get();
                    dd();
                    $teacherNameConvert = $teachersName->pluck('name')->toArray();
                    // foreach($teacherNameConvert as $name){
                        //     $paramTeachers['teacherName'][] = $name;
                        // }
                }
                dd(1);
            }
            catch(Exception $e) {
                return $e->getMessage();
            }
            ClassBoardController::sendMessage($this->request['title'], $this->request['message'], $this->request['type']);
            $newClassBoard = new ClassBoardResource(ClassBoards::create($paramTeachers));
            return $this->successClassBoardRequest($newClassBoard);
        }

        if(!empty($this->request['studentId'])) {
            $paramStudents = [
                'classBoardId' => $classBoardId,
                'message' => $this->request['message'],
                'title' => $this->request['title'],
                'studentId' => $this->request['studentId'],
                'date' => $this->request['date'],
                'type' => $this->request['type'],
            ];
        }
    }

    public function createClassBoardByStudent($paramStudents)
    {
        
    }
    public function createClassBoardByTeacher($paramTeachers)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $classBoardId
     * @return \Illuminate\Http\Response
     */
    public function show($classBoardId)
    {
        $classBoard = ClassBoards::find($classBoardId);
        $classBoardData = new ClassBoardResource($classBoard);
        return $this->successClassBoardRequest($classBoardData);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $classBoardId
     * @return \Illuminate\Http\Response
     */
    public function update($classBoardId)
    {
        $classBoard = ClassBoards::find($classBoardId);
        if (empty($this->request['writer'])) {
            $this->request['writer'] = $classBoard['writer'];
        }
        if (empty($this->request['class'])) {
            $this->request['class'] = $classBoard['class'];
        }
        if (empty($this->request['title'])) {
            $this->request['title'] = $classBoard['title'];
        }
        if (empty($this->request['view'])) {
            $this->request['view'] = $classBoard['view'];
        }
        if (empty($this->request['date'])) {
            $this->request['date'] = $classBoard['date'];
        }
        $validator = Validator::make($this->request->all(), [
            'writer' => 'string|required',
            'class' => 'string|required',
            'title' => 'string|required',
            'view' => 'integer|required',
            'date' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $classBoard['writer'] = $this->request['writer'],
            $classBoard['class'] = $this->request['class'],
            $classBoard['title'] = $this->request['title'],
            $classBoard['view'] = $this->request['view'],
            $classBoard['date'] = $this->request['date'],
        ];

        $newInfoClassBoard = $classBoard->update($params);
        return $this->successClassBoardRequest($newInfoClassBoard);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $classBoardId
     * @return \Illuminate\Http\Response
     */
    public function destroy($classBoardId)
    {
        $classBoard = ClassBoards::find($classBoardId);
        $deleteClassBoard = $classBoard->delete($classBoard);
        return $this->successClassBoardRequest($deleteClassBoard);
    }

    public function sendMessage($message)
    {
        // $validator = Validator::make($this->request->all(), [
        //     'classBoardId' => 'string|required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }

        // $classBoard = ClassBoards::find($this->request['classBoardId']);

        $newAppId = 1567865;
        $newAppKey = "3fcd7920ae1ad4d51c58";
        $newAppSecret = "1a1ac3cf49fe66975dde";

        $newPusher = new Pusher($newAppKey, $newAppSecret, $newAppId, [
            'cluster' => 'ap1',
            'useTLS' => true
        ]);
        // dd($this->request->all());
        if ($this->request['type'] == 'sendAll'){
            event(new SendAllAnnounced($message));
        }
        if ($this->request['type'] == 'sendStudent'){
            event(new SendStudentAnnounced($message));
        }
        if ($this->request['type'] == 'sendTeacher'){
            event(new SendTeacherAnnounced('woooo'));
        }
        // event(new ChatMessageSent('ora', 'orrrrrra'));
        $newPusher->trigger('messages-staging', 'my-event', ['message' => 'Hello World']);
        // return $classBoard;
    }
}
