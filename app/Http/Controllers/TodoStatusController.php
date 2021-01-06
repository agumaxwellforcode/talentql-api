<?php

namespace App\Http\Controllers;

use App\Models\TodoStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/*
   This part is to give the user the ability to set his/her metrics of mesuring tasks,
   therefore a todo (task) can asume the status of only defined status(in the db)

*/

class TodoStatusController extends Controller
{
    /**
     * Display a listing of all predefined todo status.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all predefined todo status
        $todostatus = TodoStatus::all();

        if ($todostatus->isEmpty()) {

            // todostatus is empty
            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "No Todo status added yet",
                'data' => [
                    'todos_status' => $todostatus->toArray(),
                ]
            ], 200);
        } else {

            // todostatus is not empty
            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "All todos status returned successfully",
                'data' => [
                    'todos_status' => $todostatus->toArray(),
                ]
            ], 200);
        }
    }


    /**
     * Store a newly created todo status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create a new todo status (Record)

        // Validate input from client application
        $validator = Validator::make($request->all(), [
            'slug' => 'required|string|min:1',
        ]);

        // Handle Validation errors
        if ($validator->fails()) {
            return response()->json([
                'code' => '400',
                'action' => 'create',
                'status' => 'error',
                'message' => [
                    'Invalid or empty input parameter',
                    $validator->errors()
                ]
            ], 400);
        } else {

            //Create new todo status
            $create_todo_status = TodoStatus::create([
                'slug' => $request->slug,
            ]);

            // Catch and return error response if there's a problem in creating the todo status
            if (!$create_todo_status)
                return response()->json([
                    'code' => '500',
                    'action' => 'create',
                    'status' => 'error',
                    'message' => 'Problem adding todos status'
                ], 500);
            else
                return response()->json([
                    'code' => '201',
                    'action' => 'create',
                    'status' => 'success',
                    'message' => "Todo status created successfully",
                    'data' => [
                        'todo_status' => $request->toArray(),
                    ]
                ], 201);
        }
    }

    /**
     * Display the specified todo status.
     *
     * @param  \App\Models\TodoStatus  $todoStatus
     * @return \Illuminate\Http\Response
     */
    public function show(TodoStatus $todostatus)
    {
        if (!$todostatus)
            return response()->json([
                'code' => '400',
                'action' => 'fetch',
                'status' => 'error',
                'message' => "Todo status not found"
            ], 400);
        else
            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "Todo status fetched successfully",
                'data' => [
                    'todo_status' => $todostatus->toArray(),
                ]
            ], 200);
    }

    /**
     * Update the specified todo status in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TodoStatus  $todoStatus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TodoStatus $todostatus)
    {
        $validator = Validator::make($request->all(), [
            'slug' => 'string|min:1'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'code' => '400',
                'action' => 'edit',
                'status' => 'error',
                'message' => [
                    'Invalid input parameter',
                    $validator->errors()
                ]
            ], 400);
        } else

        if (!$todostatus->exists) {
            return response()->json([
                'code' => '400',
                'action' => 'edit',
                'status' => 'error',
                'message' => 'Todo status not found'
            ], 400);
        } else
            $updated = $todostatus->fill($request->all())->save();

        if ($updated)
            return response()->json([
                'code' => '200',
                'action' => 'edit',
                'status' => 'success',
                'message' => "Todo status updated successfully",
                'data' => [
                    'todo_status' => $todostatus->toArray(),
                ]
            ], 200);
        else
            return response()->json([
                'code' => '500',
                'action' => 'edit',
                'status' => 'error',
                'message' => 'There was a problem updating the todo'
            ], 500);
    }

    /**
     * Remove the specified todo status from db.
     *
     * @param  \App\Models\TodoStatus  $todoStatus
     * @return \Illuminate\Http\Response
     */
    public function destroy(TodoStatus $todostatus)
    {
        if (!$todostatus->exists) {
            return response()->json([
                'code' => '400',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'Todo status not found'
            ], 400);
        }

        if ($todostatus->delete())
            return response()->json([
                'code' => '200',
                'action' => 'remove',
                'status' => 'success',
                'message' => 'Todo status deleted successfully'
            ], 200);
        else
            return response()->json([
                'code' => '500',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'Problem deleting todo status'
            ], 500);
    }
}
