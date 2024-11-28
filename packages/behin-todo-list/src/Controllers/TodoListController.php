<?php

namespace TodoList\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Services\File\FileService;
use App\Models\User;
use Brick\Math\BigInteger;
use Carbon\Carbon;
use FileService\Controllers\FileServiceController;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use TodoList\Models\Todo;
use TodoList\Models\TodoFile;

class TodoListController extends Controller
{
    public function index()
    {
        $users = User::all();
        return view('TodoListViews::index', compact('users'));
    }

    public static function get($id)
    {
        return Todo::find($id);
    }

    public function list()
    {
        $tasks = Todo::where('user_id', Auth::id())->orWhere('creator', Auth::id())->get()->each(function ($row) {
            $row->creator_name = User::find($row->creator)->display_name;
        });
        return [
            'data' => $tasks
        ];
    }

    public function create(Request $request)
    {
        $task = Todo::create([
            'creator' => $request->creator,
            'user_id' => $request->user_id,
            'task' => $request->task,
            'description' => $request->description,
            'reminder_date' => $request->reminder_date,
            'due_date' => $request->due_date,
        ]);
        return response(trans("task assigned successfully"));
    }

    public function edit(Request $request)
    {
        $task = self::get($request->id);
        $files = TodoFile::where('task_id', $request->id)->get();
        // dd($files);
        if ($files) {
            return view('TodoListViews::edit', compact('task', 'files'));
        }
        return view('TodoListViews::edit', compact('task'));
    }

    public function update(Request $request, TodoFile $file)
    {
        // return $request->all();
        if ($request->hasFile('file')) {
            $fileService = new FileServiceController;
            $fileResult = $fileService->uploadAndGetFile($request, $file, 'todo-files');
            $file->task_id = $request->id;
            $file->file_path = $fileResult->file_path;
            $file->file_size = $fileResult->file_size;
            $file->file_type = $fileResult->file_type;
            $file->save();
        }
        $task = self::get($request->id);
        if ($task->creator != Auth::id()) {
            return response(trans("update not ok"), 403);
        }
        $task->task = $request->task;
        $task->description = $request->description;
        $task->reminder_date = $request->reminder_date;
        $task->due_date = $request->due_date;
        $task->done = $request->done ? 1 : 0;
        $task->save();


        return response(trans("update ok"));
    }

    public function delete(Request $request)
    {
        $task = self::get($request->id);
        if ($task->creator != Auth::id()) {
            return response(trans("delete not ok"), 403);
        }
        $task->delete();
        return response(trans("delete ok"));
    }

    public function othersList(Request $request)
    {
        $tasks = Todo::where('user_id', $request->user_id)->get();
        return [
            'data' => $tasks
        ];
    }

    public function today()
    {
        $start_today = Carbon::today()->timestamp * 1000;
        $end_today = Carbon::tomorrow()->timestamp * 1000;
        $tasks = Todo::where('user_id', Auth::id())->where('due_date', '>', $start_today)
            ->where('due_date', '<', $end_today)->get()->each(function ($row) {
                $row->creator_name = User::find($row->creator)->display_name;
            });
        $tasks1 = Todo::where('user_id', Auth::id())->where('reminder_date', '>', $start_today)
            ->where('reminder_date', '<', $end_today)->get()->each(function ($row) {
                $row->creator_name = User::find($row->creator)->display_name;
            });
        $tasks = $tasks->merge($tasks1);
        return [
            'data' => $tasks
        ];
    }

    public function expired()
    {
        $start_today = Carbon::today()->timestamp * 1000;
        $tasks = Todo::where('user_id', Auth::id())->where('due_date', '<', $start_today)->where('done', 0)->get()->each(function ($row) {
            $row->creator_name = User::find($row->creator)->display_name;
        });
        return [
            'data' => $tasks
        ];
    }
}
