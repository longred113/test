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
        $classBoardsData = ClassBoardResource::collection(ClassBoards::where('type', 'sendAll')
        ->orderBy('classBoardId','DESC')->take(5)->get());
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
            'teacherIds' => 'array',
            'studentIds' => 'array',
            'type' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        if (!empty($this->request['teacherIds'])) {
            try {
                foreach ($this->request['teacherIds'] as $teacher) {
                    $classBoardId = IdGenerator::generate(['table' => 'class_boards', 'trow' => 'classBoardId', 'length' => 7, 'prefix' => 'CB']);
                    $paramTeachers = [
                        'classBoardId' => $classBoardId,
                        'message' => $this->request['message'],
                        'title' => $this->request['title'],
                        'date' => $this->request['date'],
                        'type' => $this->request['type'],
                    ];
                    $paramTeachers['teacherId'] = $teacher;
                    $teachersName = Teachers::where('teacherId', $teacher)->get('name');
                    $teacherNameConvert = $teachersName->pluck('name')->toArray();
                    $paramTeachers['teacherName'] = implode(', ', $teacherNameConvert);
                    $newClassBoard = new ClassBoardResource(ClassBoards::create($paramTeachers));
                }
                ClassBoardController::sendMessage($this->request['title'], $this->request['message']);
                return $this->successClassBoardRequest();
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if (!empty($this->request['studentIds'])) {
            try {
                foreach ($this->request['studentIds'] as $student) {
                    $classBoardId = IdGenerator::generate(['table' => 'class_boards', 'trow' => 'classBoardId', 'length' => 7, 'prefix' => 'CB']);
                    $paramStudents = [
                        'classBoardId' => $classBoardId,
                        'message' => $this->request['message'],
                        'title' => $this->request['title'],
                        'studentId' => $this->request['studentId'],
                        'date' => $this->request['date'],
                        'type' => $this->request['type'],
                    ];
                    $paramStudents['studentId'] = $student;
                    $studentsName = Students::where('studentId', $student)->get('name');
                    $studentNameConvert = $studentsName->pluck('name')->toArray();
                    $paramStudents['studentName'] = implode(', ', $studentNameConvert);
                    $newClassBoard = new ClassBoardResource(ClassBoards::create($paramStudents));
                }
                ClassBoardController::sendMessage($this->request['title'], $this->request['message']);
                return $this->successClassBoardRequest();
            } catch (Exception $e) {
                return $e->getMessage();
            }
        }

        if (empty($this->request['studentId']) && empty($this->request['teacherId'])) {
            $classBoardId = IdGenerator::generate(['table' => 'class_boards', 'trow' => 'classBoardId', 'length' => 7, 'prefix' => 'CB']);
            $params = [
                'classBoardId' => $classBoardId,
                'message' => $this->request['message'],
                'title' => $this->request['title'],
                'date' => $this->request['date'],
                'type' => $this->request['type'],
            ];
            $newClassBoard = new ClassBoardResource(ClassBoards::create($params));
            ClassBoardController::sendMessage($this->request['title'], $this->request['message']);
            return $this->successClassBoardRequest($newClassBoard);
        }
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

    public function getStudentAnnouncement($studentId)
    {
        $getStudentAnnouncement = ClassBoards::where('studentId', $studentId)
        ->orWhere('type', 'sendAll')
        ->select('title','message')
        ->orderBy('classBoardId','DESC')->take(5)->get();
        return $this->successClassBoardRequest($getStudentAnnouncement);
    }

    public function getTeacherAnnouncement($teacherId)
    {
        $getTeacherAnnouncement = ClassBoards::where('teacherId', $teacherId)
        ->orWhere('type', 'sendAll')
        ->select('title','message')
        ->orderBy('classBoardId','DESC')->take(5)->get();
        return $this->successClassBoardRequest($getTeacherAnnouncement);
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

    public function sendMessage($title, $message)
    {
        // $validator = Validator::make($this->request->all(), [
        //     'classBoardId' => 'string|required',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }

        // $classBoard = ClassBoards::find($this->request['classBoardId']);

        $newAppId = 1567162;
        $newAppKey = "ef7b5bab9016721f8248";
        $newAppSecret = "d55e4fed621ad049048f";

        $newPusher = new Pusher($newAppKey, $newAppSecret, $newAppId, [
            'cluster' => 'ap1',
            'useTLS' => true,
            'dedupe' => true,
        ]);
        // dd($this->request->all());
        if ($this->request['type'] == 'sendAll') {
            event(new SendAllAnnounced($title, $message));
        }
        // if ($this->request['type'] == 'sendStudent') {
        //     event(new SendStudentAnnounced($message));
        // }
        // if ($this->request['type'] == 'sendTeacher') {
        //     event(new SendTeacherAnnounced('woooo'));
        // }
        // event(new ChatMessageSent('ora', 'orrrrrra'));
        // $newPusher->trigger('messages-staging', ['my-event', 'next-event'], ['message' => 'Hello World']);
        // return $classBoard;
    }
}
