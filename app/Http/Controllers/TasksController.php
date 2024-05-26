<?php

namespace App\Http\Controllers;
use App\Models\Tasks;

use Illuminate\Http\Request;

class TasksController extends Controller
{
    //index function
    public function index($show_all=null){
        $tasks= Tasks::where('status','')->get();        
        $data= compact('tasks');
        return view('index')->with($data);
    }

    //show all function
    public function show_all(){
        $tasks= Tasks::all();           
        $data= compact('tasks');
        return view('index')->with($data);
    }

    //add function
    public function add(Request $request){
        try{
            $task= $request['task'];       
            $taskData= $request->validate([
                'task'=>'required|unique:tasks,task',            
            ]);
            
            $newtask= new Tasks;
            $newtask->task = $task;
            $newtask->status= "";
            $newtask->save();
    
            return response()->json([
                'status'=>true,
                'message'=>'Task Added Succesfully',
                'task'=>$newtask
            ]);
        }catch (ValidationException $e){
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }
    }


    //delete function
    public function delete($id){
        $task= Tasks::find($id);
        if($task){
            $task->delete();
            return response()->json(['status'=>true,'message'=>'Task deleted succesfuly']);
        }else{
            return response()->json(['status'=>false,'message'=>'Invalid Id. Task not found']);
        }
    }

    //update functon
    public function update($id){
        $task= Tasks::find($id);
        if($task){
            $task->status= "Done";
            $task->save();
            return response()->json(['status'=>true,'message'=>'Task Update succesfuly']);
        }else{
            return response()->json(['status'=>false,'message'=>'Invalid Id. Task not found']);
        }
    }
}
