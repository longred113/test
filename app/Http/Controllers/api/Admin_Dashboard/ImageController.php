<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ImageResource;
use App\Models\Images;
use Cloudinary\Asset\Image;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ImageController extends Controller
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
        $image = ImageResource::collection(Images::all());
        return $this->successImageRequest($image);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        // dd($this->request->all());
        // $validator = Validator::make($this->request, [
        //     'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:5048',
        //     'studentId' => 'string',
        //     'teacherId' => 'string',
        // ]);
        // if ($validator->fails()) {
        //     return $this->errorBadRequest($validator->getMessageBag()->toArray());
        // }
        try {
            var_dump($this->request->all());
            json_encode($this->request->all());
            $name = $this->request->file('file')->getClientOriginalName();
            $image_path = Cloudinary::upload($this->request->file('file')->getRealPath())->getSecurePath();
            $params = [
                'name' => $name,
                'image_path' => $image_path,
                'studentId' => $this->request['studentId'],
                'teacherId' => $this->request['teacherId'],
            ];
            $image = new ImageResource(Images::create($params));
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successImageRequest($image);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function getStudentImage($studentId)
    {
        $studentImage = Images::where('studentId', $studentId)->get();
        return $this->successImageRequest($studentImage);
    }

    public function getTeacherImage($teacherId)
    {
        $teacherImage = Images::where('teacherId', $teacherId)->get();
        return $this->successImageRequest($teacherImage);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id)
    {
        $image = Images::find($id);
        if (empty($this->request['studentId'])) {
            $image['studentId'] = $this->request['studentId'];
        }
        if (empty($this->request['teacherId'])) {
            $image['teacherId'] = $this->request['teacherId'];
        }
        $validator = Validator::make($this->request->all(), [
            'image' => 'required|image|mimes:jpg,png,jpeg,gif,svg|max:5048',
            'studentId' => 'string',
            'teacherId' => 'string',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        if (!empty($this->request['studentId'])) {
            $name = $this->request->file('image')->getClientOriginalName();
            $image_path = Cloudinary::upload($this->request->file('image')->getRealPath())->getSecurePath();
            $params = [
                $image['name'] = $name,
                $image['image_path'] = $image_path,
                $image['studentId'] = $this->request['studentId'],
            ];

            $updateImage = $image->update($params);
        }
        if (!empty($this->request['teacherId'])) {
            $name = $this->request->file('image')->getClientOriginalName();
            $image_path = Cloudinary::upload($this->request->file('image')->getRealPath())->getSecurePath();
            $params = [
                $image['name'] = $name,
                $image['image_path'] = $image_path,
                $image['teacherId'] = $this->request['teacherId'],
            ];

            $updateImage = $image->update($params);
        }
        return $this->successImageRequest($updateImage);
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
