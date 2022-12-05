<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Resources\TasksResource;
use App\Models\Task;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TasksController extends Controller
{
    use HttpResponses;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return TasksResource::collection(
            Task::where('user_id', Auth::user()->id)->get()
        );
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $request->validated($request->all());
        $newTask = Task::create([
            'user_id' => Auth::user()->id,
            'name' => $request->name,
            'description' => $request->description,
            'priority' => $request->priority,
            'is_done' => $request->is_done===true,
        ]);
        return new TasksResource($newTask);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return TasksResource
     */
    public function show(Task $task)
    {
        return $this->isNotAuthorized($task)?$this->isNotAuthorized($task): new TasksResource($task);
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return TasksResource
     */
    public function update(Request $request, Task $task)
    {
        $this->isNotAuthorized($task);
        $task->update($request->all());
        return new TasksResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $this->isNotAuthorized($task);
        $task->delete();
        return $this->success($task->name, 'Deleted successfully',200);
    }

    private function isNotAuthorized($task)
    {
        if (Auth::user()->id !== $task->user_id){
            return $this->error('', 'You are not authorized', 403);
        }
    }
}
