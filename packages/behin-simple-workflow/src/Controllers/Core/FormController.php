<?php

namespace Behin\SimpleWorkflow\Controllers\Core;

use App\Http\Controllers\Controller;
use Behin\SimpleWorkflow\Models\Core\Form;
use Behin\SimpleWorkflow\Models\Core\Process;
use Illuminate\Http\Request;

class FormController extends Controller
{
    public static function getById($id){
        return Form::find($id);
    }

    public static function getAll(){
        return Form::get();
    }

    public function index(){
        $forms = Form::get();
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
                    'required' => $request->required[$index],
                    'class' => $request->class[$index]
                ];
            }

            $index++;
        }
        $form->content = json_encode($ar);
        $form->save();
        return redirect()->back();
    }

    public function store(Request $request)
    {
        Form::updateOrCreate(
            [ 'id' => $request->id ],
            $request->all()
        );
        return redirect(route('simpleWorkflow.form.index'));
    }
}
