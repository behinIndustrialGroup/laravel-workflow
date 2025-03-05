<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Process;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class FormController extends Controller
{
    public static function getById($id){
        return Form::find($id);
    }

    public static function getAll(){
        return Form::get();
    }

    public static function getFormFields($id){
        $form = self::getById($id);
        $fields = json_decode($form->content);
        $ar = [];
        foreach($fields as $field){
            $fieldDetails = getFieldDetailsByName($field->fieldName);
            if($fieldDetails){
                $ar[] = $field->fieldName;
            }else{
                $childAr = self::getFormFields($field->fieldName);
                $ar = array_merge($ar, $childAr);
            }
        }
        return $ar;         
    }

    public static function requiredFields($id){
        $form = self::getById($id);
        $fields = json_decode($form->content);
        $ar = [];
        foreach($fields as $field){
            $fieldDetails = getFieldDetailsByName($field->fieldName);
            if(!$fieldDetails){
                if($field->readOnly != 'on'){
                    $formId = $field->fieldName;
                    $childAr = self::requiredFields($formId);
                    $ar = array_merge($ar, $childAr); 
                }
            }
            if($field->required == 'on'){
                $ar[] = $field->fieldName;
            }
        }

        return $ar;
    }

    public function index(){
        $forms = Form::orderBy('created_at', 'desc')->get();
        return view('SimpleWorkflowView::Core.Form.list')->with([
            'forms' => $forms
        ]);
    }

    public function edit($id){
        $form = self::getById($id);
        return view('SimpleWorkflowView::Core.Form.edit')->with([
            'form' => $form
        ]);
    }

    public function update(Request $request){

        $form = self::getById($request->formId);
        $ar = [];
        $index = 0;
        foreach($request->fieldName as $fieldName){
            if($fieldName){
                $ar[] = [
                    'fieldName' => $fieldName,
                    'order' => $request->order[$index] ? $request->order[$index] : $index+1,
                    'required' => isset($request->required[$index]) ? $request->required[$index] : 'off',
                    'readOnly' => isset($request->readOnly[$index]) ? $request->readOnly[$index] : 'off',
                    'class' => $request->class[$index]
                ];
            }
            $index++;
        }
        $form->content = json_encode($ar);
        $form->name = $request->name;
        $form->save();
        return redirect()->back();
    }

    public function editContent($id){
        $form = self::getById($id);
        return view('SimpleWorkflowView::Core.Form.editContent')->with([
            'form' => $form
        ]);
    }

    public function updateContent(Request $request){

        $form = self::getById($request->formId);
        $form->content = $request->content;
        $form->name = $request->name;
        $form->save();
        return redirect()->back();
    }

    public function editScript($id){
        $form = self::getById($id);
        return view('SimpleWorkflowView::Core.Form.editScript')->with([
            'form' => $form
        ]);
    }

    public function updateScript(Request $request){

        $form = self::getById($request->formId);
        $form->scripts = $request->scripts;
        $form->save();
        return redirect()->back();
    }

    public function store(Request $request)
    {
        $form = self::getById($request->formId);
        $fields = json_decode($form->content);
        $ar= [];
        foreach($fields as $field){
            $ar[] = [
                'fieldName' => $field->fieldName,
                'order' => $field->order,
                'required' => $field->required,
                'readOnly' => $field->readOnly,
                'class' => $field->class
            ];
        }
        $ar[] = [
            'fieldName' => $request->fieldName,
            'order' => $request->order,
            'required' => isset($request->required) ? $request->required : 'off',
            'readOnly' => isset($request->readOnly) ? $request->readOnly : 'off',
            'class' => $request->class
        ];
        $form->content = json_encode($ar);
        $form->save();
        return redirect(route('simpleWorkflow.form.edit', ['id' => $form->id, '#createForm']))->with([
            'success' => trans('Form updated successfully')
        ]);
    }

    public static function createForm(Request $request){
        $form = new Form();
        $form->name = $request->name;
        $form->content = json_encode([]);
        $form->save();
        return redirect(route('simpleWorkflow.form.edit', ['id' => $form->id]))->with([
            'success' => trans('Form created successfully')
        ]);
    }

    public static function preview($id){
        $form = self::getById($id);
        return view('SimpleWorkflowView::Core.Form.preview')->with([
            'form' => $form
        ]);
    }

    public static function copy(Request $request){
        $form = self::getById($request->id);
        $newForm = new Form();
        $newForm->name = $form->name . ' - Copy';
        $newForm->executive_file = $form->executive_file;
        $newForm->content = $form->content;
        $newForm->save();
        return response()->json([
            'msg' => trans('Form copied successfully'),
            'id' => $newForm->id
        ]);
    }

    public static function delete(Request $request){
        $form = self::getById($request->id);
        $form->delete();
        return response()->json([
            'msg' => trans('Form deleted successfully')
        ]);
    }
}
