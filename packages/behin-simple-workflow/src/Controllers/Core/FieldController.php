<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Fields;
use Illuminate\Http\Request;

class FieldController extends Controller
{
    public function index()
    {
        $fields = self::getAll();
        return view('SimpleWorkflowView::Core.Field.index', compact('fields'));
    }

    public function create()
    {
        return view('SimpleWorkflowView::Core.Condition.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'query' => 'nullable|string',
            'placeholder' => 'nullable|string',
        ]);
        // $attributes = [
        //     'query' => $request->input('query') ? $request->input('query') : null,
        //     'placeholder' => $request->placeholder,
        //     'style' => $request->style,
        //     'script' => $request->script,
        //     'datalist_from_database' => $request->datalist_from_database
        // ];

        Fields::create([
            'name' => $request->name,
            'type' => $request->type,
            'attributes' => null //json_encode($attributes)
        ]);

        return redirect()->route('simpleWorkflow.fields.index', ['#createForm'])->with('success', 'Fields created successfully.');
    }

    public function edit(Fields $field)
    {
        return view('SimpleWorkflowView::Core.Field.edit', compact('field'));
    }

    public function update(Request $request, Fields $field)
    {

        $attributes = [
            'query' => $request->input('query') ? $request->input('query') : null,
            'placeholder' => $request->placeholder,
            'options' => $request->options ? $request->options : null,
            'style' => $request->style,
            'script' => $request->script,
            'datalist_from_database' => $request->datalist_from_database
        ];
        if ($request->columns !== null) {
            $attributes['columns'] = $request->columns;
        }
        if ($request->id !== null) {
            $attributes['id'] = $request->id;
        }
        $field->update([
            'name' => $request->name,
            'type' => $request->type,
            'attributes' => json_encode($attributes)
        ]);

        return redirect()->route('simpleWorkflow.fields.edit', $field->id)->with('success', 'Fields updated successfully.');
    }

    public static function getAll()
    {
        return Fields::orderBy('created_at')->get();
    }
    public static function getById($id)
    {
        return Fields::find($id);
    }

    public static function getByName($fieldName)
    {
        $field = Fields::where('name', $fieldName)->first();
        if ($field) {
            return $field;
        }
        return null;
    }
}
