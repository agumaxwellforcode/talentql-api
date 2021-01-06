<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use App\Models\TodoStatus;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{
    /**
     * Display a listing of the todos.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Retrieve all todos

        $todos = Todo::all();

        // check if todos is empty to tailor the response

        if ($todos->isEmpty()) {
            // todo is empty

            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "No todos yet",
                'data' => [
                    'todos' => $todos->toArray(),
                ]
            ], 200);
        } else {
            // todo is not empty

            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "All todos returned successfully",
                'data' => [
                    'todos' => $todos->toArray(),
                ]
            ], 200);
        }
    }

    /**
     * Store a newly created todos in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Create a new todo (Record)

        // Validate input from client

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:1',
            'body' => 'required|string|min:1',
            'status' => 'required|string|min:1',
            'start' => 'required|date_format:d/m/Y',
            'end' => 'required|date_format:d/m/Y'
        ]);

        // Handle Validation errors

        if ($validator->fails()) {
            return response()->json([
                'code' => '400',
                'action' => 'create',
                'status' => 'error',
                'message' => [
                    'Invalid or empty input parameters see affected field(s) below',
                    $validator->errors()
                ]
            ], 400);
        } else {
            // Validation passed, proceed

            // Fetch all predefined Todo status, This is to make sure a client dosent pass random todo status

            $todostatus_array = TodoStatus::all();

            // Compare the supplied ststus to predefined all status

            foreach ($todostatus_array as $todostatus) {
                if ($request->status == $todostatus->slug) {

                    /* There's a match

                     Create the todo */
                    $create_todo = Todo::create([
                        'title' => $request->title,
                        'body' => $request->body,
                        'status' => $request->status,
                        'start' => $request->start,
                        'end' => $request->end,
                    ]);

                    // Catch and return error response if there's a problem in creating the todo
                    if (!$create_todo)
                        return response()->json([
                            'code' => '500',
                            'action' => 'create',
                            'status' => 'error',
                            'message' => 'Problem adding Todo'
                        ], 500);
                    else

                        // Todo was created successfully
                        return response()->json([
                            'code' => '201',
                            'action' => 'create',
                            'status' => 'success',
                            'message' => "Todo created successfully",
                            'data' => [
                                'todo' => $request->toArray(),
                            ]
                        ], 201);
                } else {

                    // there's no match: invalid status supplied
                    return response()->json([
                        'code' => '400',
                        'action' => 'create',
                        'status' => 'error',
                        'message' => 'Invalid todo status supplied',
                        'issue' => $request->status

                    ], 400);
                }
            }
        }
    }

    /**
     * Display the specified todo.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function show(Todo $todo)
    {
        if (!$todo)
            return response()->json([
                'code' => '400',
                'action' => 'fetch',
                'status' => 'error',
                'message' => "Todo not found"
            ], 400);
        else
            return response()->json([
                'code' => '200',
                'action' => 'fetch',
                'status' => 'success',
                'message' => "Todo fetched successfully",
                'data' => [
                    'todo' => $todo->toArray(),
                ]
            ], 200);
    }

    /**
     * Update the specified todos in database.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Todo $todo)
    {
        // Update a todo (Record)

        // Validate input from client
        $validator = Validator::make($request->all(), [
            'title' => 'string|min:1',
            'body' => 'string|min:1',
            'status' => 'string|min:1',
            'start' => 'date_format:d/m/Y',
            'end' => 'date_format:d/m/Y'
        ]);

        // Handle Validation errors
        if ($validator->fails()) {
            return response()->json([
                'code' => '400',
                'action' => 'edit',
                'status' => 'error',
                'message' => [
                    'Invalid input parameters affected field(s) below',
                    $validator->errors()
                ]
            ], 400);
        } else

            // Validation passed, proceed

            // Fetch all predefined Todo status, This is to make sure a client dosent pass random todo status
            if (!$todo->exists) {
                return response()->json([
                    'code' => '400',
                    'action' => 'edit',
                    'status' => 'error',
                    'message' => 'todo not found'
                ], 400);
            } else

                // fetch all predefined status
                $todostatus_array = TodoStatus::all();

        // Compare the supplied ststus to predefined all status
        foreach ($todostatus_array as $todostatus) {
            if ($request->status == $todostatus->slug) {

                /* There's a match

                Update the todo */
                $updated = $todo->fill($request->all())->save();

                // Catch and return error response if there's a problem in creating the todo

                // Update successfull
                if ($updated)
                    return response()->json([
                        'code' => '200',
                        'action' => 'edit',
                        'status' => 'success',
                        'message' => "todo updated successfully",
                        'data' => [
                            'todo' => $todo->toArray(),
                        ]
                    ], 200);
                else

                    //Update Error
                    return response()->json([
                        'code' => '500',
                        'action' => 'edit',
                        'status' => 'error',
                        'message' => 'There was a problem updating the todo'
                    ], 500);
            } else {

                // there's no match: invalid status supplied
                return response()->json([
                    'code' => '400',
                    'action' => 'edit',
                    'status' => 'error',
                    'message' => 'Invalid todo status supplied',
                    'issue' => $request->status

                ], 400);
            }
        }
    }

    /**
     * Remove the specified Todo from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        // check if the specified todo exists
        if (!$todo->exists) {

            // it dosen't exist
            return response()->json([
                'code' => '400',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'todo not found'
            ], 400);
        }

        // it exists

        //Delete and check if it was succeded or failed
        if ($todo->delete())
            return response()->json([
                'code' => '200',
                'action' => 'remove',
                'status' => 'success',
                'message' => 'Todo deleted successfully'
            ], 200);
        else
            return response()->json([
                'code' => '500',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'Problem deleting todo'
            ], 500);
    }
}
