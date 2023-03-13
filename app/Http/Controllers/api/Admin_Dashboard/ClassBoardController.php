<?php

namespace App\Http\Controllers\api\Admin_Dashboard;

use App\Events\FunctionAnnounced;
use App\Http\Controllers\Controller;
use App\Http\Resources\ClassBoardResource;
use App\Models\ClassBoards;
use Haruncpi\LaravelIdGenerator\IdGenerator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
            'writer' => 'string|required',
            'class' => 'string|required',
            'title' => 'string|required',
            'view' => 'integer|required',
            'date' => 'date|required',
        ]);
        if ($validator->fails()) {
            return $this->errorBadRequest($validator->getMessageBag()->toArray());
        }

        $classBoardId = IdGenerator::generate(['table' => 'class_boards', 'trow' => 'classBoardId', 'length' => 7, 'prefix' => 'CB']);
        $params = [
            'classBoardId' => $classBoardId,
            'writer' => $this->request['writer'],
            'class' => $this->request['class'],
            'title' => $this->request['title'],
            'view' => $this->request['view'],
            'date' => $this->request['date'],
        ];

        $newClassBoard = new ClassBoardResource(ClassBoards::create($params));
        event(new FunctionAnnounced($newClassBoard));
        return $this->successClassBoardRequest($newClassBoard);
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
}
