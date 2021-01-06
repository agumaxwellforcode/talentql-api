<?php

namespace App\Http\Controllers;

use App\Models\Todo;
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
        $todos = Todo::all();
        if (!$todos) {
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
        // array check
        $validator = Validator::make(
            ['data' => $request],
            ['title' => 'string|min:1'],
            ['body' => 'string|min:1'],
            ['status' => 'string|min:1'],
            ['start' => 'required|date_format:d/m/Y'],
            ['due' => 'required|date_format:d/m/Y']
        );

        if ($validator->fails()) {
            $response['message'] = $validator->errors();
            $response['action'] = 'create';
            $response['status'] = 'error';
            return response()->json($response, Response::HTTP_BAD_REQUEST);
        }

        $create_todo = Todo::create([
            'title' => $request->title,
            'body' => $request->body,
            'status' => $request->status,
            'start' => $request->start,
            'due' => $request->due,
        ]);

        if (!$create_todo)
            return response()->json([
                'code' => '500',
                'action' => 'create',
                'status' => 'error',
                'message' => 'Problem adding Todo'
            ], 500);
        else
            return response()->json([
                'code' => '201',
                'action' => 'create',
                'status' => 'success',
                'message' => "Todo created successfully",
                'data' => [
                    'todo' => $request->toArray(),
                ]
            ], 201);
    }

    /**
     * Display the specified todos.
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
                    'todos' => $todo->toArray(),
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
        if (!$todo) {
            return response()->json([
                'code' => '400',
                'action' => 'edit',
                'status' => 'error',
                'message' => 'todo not found'
            ], 400);
        }
        $updated = $todo->fill($request->all())->save();

        if ($updated)
            return response()->json([
                'code' => '200',
                'action' => 'edit',
                'status' => 'success',
                'message' => "todo updated successfully",
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
     * Remove the specified Todo from storage.
     *
     * @param  \App\Models\Todo  $todo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Todo $todo)
    {
        if (!$todo) {
            return response()->json([
                'code' => '400',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'todo not found'
            ], 400);
        }

        if ($todo->delete())
            return response()->json([
                'code' => '200',
                'action' => 'remove',
                'status' => 'success',
                'message' => 'Todo deleted successfully'
            ], 204);
        else
            return response()->json([
                'code' => '500',
                'action' => 'remove',
                'status' => 'error',
                'message' => 'Problem deleting todo'
            ], 500);
    }
}
