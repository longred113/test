<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentResource;
use App\Models\Parents;
use App\Models\Students;
use App\Models\Users;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Validation\Rule;

class StudentController extends Controller
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
        $studentsData = StudentResource::collection(Students::all());
        return $this->successStudentRequest($studentsData);
    }

    public function studentWithdrawalList()
    {
        $studentWithdrawal = StudentResource::collection(Students::where('type', 'online-break')->orWhere('type', 'offline-break')->get());
        return $this->successStudentRequest($studentWithdrawal);
    }

    public function studentJoinedList()
    {
        $studentId = Users::get('studentId');
        $joinedStudent = Students::join('users', 'students.studentId', '=', 'users.studentId')
            ->join('campuses', 'students.campusId', '=', 'campuses.campusId')
            ->select(
                'students.studentId',
                'students.campusId',
                'campuses.name as campusName',
                'students.name',
                'students.email',
                'users.email as userName',
                'users.password',
                'students.gender',
                'students.dateOfBirth',
            )
            ->where('students.type', 'online')->get();
        return $this->successStudentRequest($joinedStudent);
    }

    public function getStudentWithId($studentId)
    {
        $student = Students::find($studentId);
        if ($student != NULL) {
            $studentData = $student;
        } else {
            $studentData = "";
        }
        return $this->successStudentRequest($studentData);
    }

    public function getStudentByParent($parentId)
    {
        $student = Students::where('parentId', $parentId)->get();
        return $this->successStudentRequest($student);
    }

    public function getEnrollmentCount($studentId)
    {
        $student = Students::where('studentId', $studentId)->get('enrollmentCount');
        return $this->successStudentRequest($student);
    }

    public function getStudentByCampus($campusId)
    {
        $students = Students::where('campusId', $campusId)->get();
        return $this->successStudentRequest($students);
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
            'email' => 'string|required|unique:students',
            'password' => 'string|required|min:8',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            // 'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string|required',
            'type' => [Rule::in(['online', 'offline', 'online-break', 'offline-break', 'reserve', 'wait-for-approval'])],
            'enrollmentCount' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentId = IdGenerator::generate(['table' => 'students', 'trow' => 'studentId', 'length' => 7, 'prefix' => 'ST']);
        $studentParams = [
            'studentId' => $studentId,
            'name' => $this->request['name'],
            'enrollmentCount' => $this->request['enrollmentCount'],
            'email' => $this->request['email'],
            'gender' => $this->request['gender'],
            'dateOfBirth' => $this->request['dateOfBirth'],
            'country' => $this->request['country'],
            'timeZone' => $this->request['timeZone'],
            'status' => $this->request['status'],
            'joinedDate' => $this->request['joinedDate'],
            'withDrawal' => $this->request['withDrawal'],
            'introduction' => $this->request['introduction'],
            'talkSamId' => $this->request['talkSamId'],
            'basicPoint' => $this->request['basicPoint'],
            'campusId' => $this->request['campusId'],
            'type' => $this->request['type'],
        ];

        $userParams = [
            'name' => $this->request['name'],
            'email' => $this->request['email'],
            'password' => $this->request['password'],
            'studentId' => $studentId,
        ];
        $newStudentData = new StudentResource(Students::create($studentParams));
        dd(1);
        UserController::store($userParams);
        return $this->successStudentRequest($newStudentData);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function show($studentId)
    {
        $student = Students::find($studentId);
        $studentData = new StudentResource($student);
        return $this->successStudentRequest($studentData);
    }

    public function updateEnrollmentCount($studentId)
    {
        $validator = Validator::make($this->request->all(), [
            'enrollmentCount' => 'int',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $student = Students::where('studentId',$studentId)->update(['enrollmentCount' => $this->request['enrollmentCount']]);
        return $this->successStudentRequest($student);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function update($studentId)
    {
        try {
            $student = Students::find($studentId);
            if (empty($this->request['name'])) {
                $this->request['name'] = $student['name'];
            }
            if (empty($this->request['email'])) {
                $this->request['email'] = $student['email'];
            }
            if (empty($this->request['gender'])) {
                $this->request['gender'] = $student['gender'];
            }
            if (empty($this->request['dateOfBirth'])) {
                $this->request['dateOfBirth'] = $student['dateOfBirth'];
            }
            if (empty($this->request['country'])) {
                $this->request['country'] = $student['country'];
            }
            if (empty($this->request['timeZone'])) {
                $this->request['timeZone'] = $student['timeZone'];
            }
            if (empty($this->request['status'])) {
                $this->request['status'] = $student['status'];
            }
            if (empty($this->request['joinedDate'])) {
                $this->request['joinedDate'] = $student['joinedDate'];
            }
            if (empty($this->request['withDrawal'])) {
                $this->request['withDrawal'] = $student['withDrawal'];
            }
            if (empty($this->request['introduction'])) {
                $this->request['introduction'] = $student['introduction'];
            }
            if (empty($this->request['talkSamId'])) {
                $this->request['talkSamId'] = $student['talkSamId'];
            }
            if (empty($this->request['basicPoint'])) {
                $this->request['basicPoint'] = $student['basicPoint'];
            }
            if (empty($this->request['campusId'])) {
                $this->request['campusId'] = $student['campusId'];
            }
            if (empty($this->request['type'])) {
                $this->request['type'] = $student['type'];
            }
            if (empty($this->request['enrollmentId'])) {
                $this->request['enrollmentId'] = $student['enrollmentId'];
            }
            if (empty($this->request['parentId'])) {
                $this->request['parentId'] = $student['parentId'];
            }
            if (is_null($this->request['enrollmentCount'])) {
                $this->request['enrollmentCount'] = $student['enrollmentCount'];
            }

            $validator = Validator::make($this->request->all(), [
                'name' => 'string',
                'email' => 'string',
                // 'gender' => 'string|required',
                // 'dateOfBirth' => 'date|required',
                // 'country' => 'string|required',
                // 'timeZone' => 'string|required',
                // 'status' => 'string|required',
                // 'joinedDate' => 'date|required',
                // 'withDrawal' => 'date|required',
                // 'introduction' => 'string|required',
                // 'talkSamId' => 'string|required',
                // 'basicPoint' => 'integer|required',
                'campusId' => 'string',
                'type' => 'string',
                // 'enrollmentId' => 'string',
                // 'parentId' => 'string',
                'enrollmentCount' => 'int',
            ]);
            if ($validator->fails()) {
                return $this->errorBadRequest($validator->getMessageBag()->toArray());
            }

            $params = [
                $student['name'] = $this->request['name'],
                $student['email'] = $this->request['email'],
                $student['gender'] = $this->request['gender'],
                $student['dateOfBirth'] = $this->request['dateOfBirth'],
                $student['country'] = $this->request['country'],
                $student['timeZone'] = $this->request['timeZone'],
                $student['status'] = $this->request['status'],
                $student['joinedDate'] = $this->request['joinedDate'],
                $student['withDrawal'] = $this->request['withDrawal'],
                $student['introduction'] = $this->request['introduction'],
                $student['talkSamId'] = $this->request['talkSamId'],
                $student['basicPoint'] = $this->request['basicPoint'],
                $student['campusId'] = $this->request['campusId'],
                $student['type'] = $this->request['type'],
                $student['enrollmentId'] = $this->request['enrollmentId'],
                $student['parentId'] = $this->request['parentId'],
                $student['enrollmentCount'] = $this->request['enrollmentCount'],
            ];


            $newStudentInfoData = $student->update($params);
            $user = Users::where('studentId', $studentId)->first();
            if(!empty($user)){
                if(empty($this->request['userName'])){
                    $this->request['userName'] = $user['userName'];
                }
                if(empty($this->request['password'])){
                    $this->request['password'] = $user['password'];
                }
                $userParams = [
                    'name' => $this->request['name'],
                    'userName' => $this->request['userName'],
                    'email' => $this->request['email'],
                    'password' => $this->request['password'],
                    'studentId' => $studentId,
                ];
                if (!empty($user)) {
                    UserController::update($userParams);
                }
            }
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentRequest($newStudentInfoData);
    }

    public function updateWithdrawalStudent($studentId)
    {
        $student = Students::find($studentId);
        if (empty($this->request['name'])) {
            $this->request['name'] = $student['name'];
        }
        if (empty($this->request['email'])) {
            $this->request['email'] = $student['email'];
        }
        if (empty($this->request['gender'])) {
            $this->request['gender'] = $student['gender'];
        }
        if (empty($this->request['dateOfBirth'])) {
            $this->request['dateOfBirth'] = $student['dateOfBirth'];
        }
        if (empty($this->request['country'])) {
            $this->request['country'] = $student['country'];
        }
        if (empty($this->request['timeZone'])) {
            $this->request['timeZone'] = $student['timeZone'];
        }
        if (empty($this->request['introduction'])) {
            $this->request['introduction'] = $student['introduction'];
        }
        if (empty($this->request['talkSamId'])) {
            $this->request['talkSamId'] = $student['talkSamId'];
        }
        if (empty($this->request['basicPoint'])) {
            $this->request['basicPoint'] = $student['basicPoint'];
        }
        if (empty($this->request['campusId'])) {
            $this->request['campusId'] = $student['campusId'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $student['status'];
        }
        if (empty($this->request['enrollmentId'])) {
            $this->request['enrollmentId'] = $student['enrollmentId'];
        }
        if (empty($this->request['parentId'])) {
            $this->request['parentId'] = $student['parentId'];
        }
        if (empty($this->request['enrollmentCount'])) {
            $this->request['enrollmentCount'] = $student['enrollmentCount'];
        }
        $validator = Validator::make($this->request->all(), [
            'name' => 'string',
            'email' => 'string',
            // 'gender' => 'string|required',
            // 'dateOfBirth' => 'date|required',
            // 'country' => 'string|required',
            // 'timeZone' => 'string|required',
            // 'status' => 'string|required',
            // 'joinedDate' => 'date|required',
            // 'withDrawal' => 'date|required',
            // 'introduction' => 'string|required',
            // 'talkSamId' => 'string|required',
            // 'basicPoint' => 'integer|required',
            'campusId' => 'string',
            'type' => 'string',
            // 'enrollmentId' => 'string',
            // 'parentId' => 'string',
            'enrollmentCount' => 'int',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $student['name'] = $this->request['name'],
            $student['email'] = $this->request['email'],
            $student['gender'] = $this->request['gender'],
            $student['dateOfBirth'] = $this->request['dateOfBirth'],
            $student['country'] = $this->request['country'],
            $student['timeZone'] = $this->request['timeZone'],
            $student['status'] = $this->request['status'],
            $student['joinedDate'] = Carbon::now(),
            $student['withDrawal'] = "",
            $student['introduction'] = $this->request['introduction'],
            $student['talkSamId'] = $this->request['talkSamId'],
            $student['basicPoint'] = $this->request['basicPoint'],
            $student['campusId'] = $this->request['campusId'],
            $student['type'] = 'online',
            $student['enrollmentId'] = $this->request['enrollmentId'],
            $student['parentId'] = $this->request['parentId'],
            $student['enrollmentCount'] = $this->request['enrollmentCount'],
        ];
        $newStudentInfoData = $student->update($params);
        $user = Users::where('studentId', $studentId)->update(['activate' => 1]);
        return $this->successStudentRequest($newStudentInfoData);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $studentId
     * @return \Illuminate\Http\Response
     */
    public function destroy($studentId)
    {
        $student = Students::find($studentId);
        $deleteStudent = $student->delete();
        return $this->successStudentRequest($deleteStudent);
    }
}
