<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClassProductResource;
use App\Models\Classes;
use App\Models\ClassProducts;
use App\Models\StudentClasses;
use Exception;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ClassProductController extends Controller
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
        $classProductsData = ClassProductResource::collection(ClassProducts::all());
        return $this->successClassProductRequest($classProductsData);
    }

    public function getClassFromProduct()
    {
        $validator = Validator::make($this->request->all(), [
            'productIds' => 'array|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        try {
            $productIds = $this->request['productIds'];
            $classProductsData = ClassProducts::join('classes', 'classes.classId', '=', 'class_products.classId')
                ->select(
                    'classes.classId',
                    'classes.name',
                    'class_products.productId',
                    'classes.availableNumStudent',
                    'classes.numberOfStudent',
                )
                ->whereIn('class_products.productId', $productIds)
                ->whereRaw('classes.availableNumStudent < classes.numberOfStudent')
                ->distinct()
                ->get();
            // foreach ($classProductsData as $classProduct) {
            //     $studentClass[] = StudentClasses::where('classId', $classProduct['classId'])
            //         ->select(
            //             'classId',
            //             DB::raw('GROUP_CONCAT(DISTINCT CONCAT_WS(":",studentId)) as students'),
            //         )
            //         ->groupBy('classId')
            //         ->pluck('students', 'classId')
            //         ->toArray();
            // }
            // foreach ($studentClass as $value) {
            //     $numberOfStudent = count($value);
            // }
            // return $studentClass;
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return $this->successClassProductRequest($classProductsData);
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
            'productId' => 'string|required',
            'classId' => 'string|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classProductId = IdGenerator::generate(['table' => 'class_products', 'trow' => 'classProductId', 'length' => 8, 'prefix' => 'CLP']);
        $params = [
            'classProductId' => $classProductId,
            'productId' => $this->request['productId'],
            'classId' => $this->request['classId'],
            'status' => $this->request['status'],
        ];

        $newClassProduct = new ClassProductResource(ClassProducts::create($params));
        return $this->successClassProductRequest($newClassProduct);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($classProductId)
    {
        $classProduct = ClassProducts::find($classProductId);
        $classProductData = new CLassProductResource($classProduct);
        return $this->successClassProductRequest($classProductData);
    }

    public function displayByClass($classId)
    {
        $classProduct = ClassProducts::where('classId', $classId)->get();
        return $this->successClassProductRequest($classProduct);
    }

    public function displayByProduct($productId)
    {
        $classProduct = ClassProducts::where('productId', $productId)->get();
        return $this->successClassProductRequest($classProduct);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($classProductId)
    {
        $classProduct = ClassProducts::find($classProductId);
        if (empty($this->request['classId'])) {
            $this->request['classId'] = $classProduct['classId'];
        }
        if (empty($this->request['productId'])) {
            $this->request['productId'] = $classProduct['productId'];
        }
        if (empty($this->request['status'])) {
            $this->request['status'] = $classProduct['status'];
        }
        $validator = Validator::make($this->request->all(), [
            'productId' => 'string|required',
            'classId' => 'string|required',
            'status' => 'string|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $params = [
            $classProduct['productId'] = $this->request['productId'],
            $classProduct['classId'] = $this->request['classId'],
            $classProduct['status'] = $this->request['status'],
        ];
        $newInfoClassProduct =  $classProduct->update($params);
        return $this->successClassProductRequest($newInfoClassProduct);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($classProductId)
    {
        $classProduct = ClassProducts::find($classProductId);
        $deleteClassProduct = $classProduct->delete();
        return $this->successClassProductRequest($deleteClassProduct);
    }

    public static function updateMultipleProduct($classProductParams)
    {
        $classId = $classProductParams['classId'];
        $productIds = $classProductParams['productIds'];

        ClassProducts::where('classId', $classId)->delete();

        foreach ($productIds as $productId) {
            $classProductId = IdGenerator::generate(['table' => 'class_products', 'trow' => 'classProductId', 'length' => 8, 'prefix' => 'CLP']);
            $params = [
                'classProductId' => $classProductId,
                'productId' => $productId,
                'classId' => $classId,
            ];
            ClassProducts::create($params);
        }
    }
}
