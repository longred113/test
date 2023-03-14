<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\StudentEnrollmentResource;
use App\Models\StudentEnrollments;
use Carbon\Carbon;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpParser\Node\Stmt\TryCatch;

class StudentEnrollmentController extends Controller
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
        $studentEnrollmentsData = StudentEnrollmentResource::collection(StudentEnrollments::all());
        return $this->successStudentRequest($studentEnrollmentsData);
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
            'studentId' => 'string|required',
            'enrollmentId' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        try { 
        $studentEnrollmentId = IdGenerator::generate(['table' => 'student_enrollments', 'trow' => 'studentEnrollmentId', 'length' => 7, 'prefix' => 'SE']);
        $params = [
            'studentEnrollmentId' => $studentEnrollmentId,
            'studentId' => $this->request['studentId'],
            'enrollmentId' => $this->request['enrollmentId'],
            'check' => $this->request['check'],
            'date' => Carbon::now(),
        ];
        $newStudentEnrollment = new StudentEnrollmentResource(StudentEnrollments::create($params));
        } catch(Exception $e) {
            return $e->getMessage();
        }
        return $this->successStudentEnrollmentRequest($newStudentEnrollment);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($studentEnrollmentId)
    {
        $studentEnrollment = StudentEnrollments::find($studentEnrollmentId);
        $studentEnrollmentData = new StudentEnrollmentResource($studentEnrollment);
        return $this->successStudentEnrollmentRequest($studentEnrollmentData);
    }

    public function updateCheck()
    {
        $validator = Validator::make($this->request->all(), [
            'enrollmentId' => 'string|required',
            'check' => 'integer',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }
        $studentEnrollment = StudentEnrollments::where('enrollmentId', $this->request['enrollmentId'])->update(['check' => $this->request['check']]);
        return $this->successStudentEnrollmentRequest($studentEnrollment);
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
