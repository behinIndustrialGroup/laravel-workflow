@extends('behin-layouts.app')

@section('title')
    {{ trans('fields.Edit Entity') }}
@endsection

@section('content')
    <div class="container table-responsive card p-2">
        <form action="{{ route('simpleWorkflow.entities.update', $entity->id) }}" method="POST">
            @csrf
            @method('PUT')
            {!!  Form::text(
                'name',
                [
                    'name' => 'name',
                    'value' => $entity->name,
                    'class' => 'form-control',
                    'required' => true,
                    'dir' => 'ltr',
                ]
            ) !!}
            {!! Form::textarea('description', [
                'value' => $entity->description,
                'class' => 'form-control',
                'required' => false,
                'placeholder' => trans('fields.Entity Description'),
                'rows' => 5
            ]) !!}
            <span dir="ltr" style="display:block; float:left">name,type,nullable(yes,no)</span>
            {!! Form::textarea('columns', [
                'value' => $entity->columns,
                'class' => 'form-control',
                'required' => false,
                'dir' => 'ltr',
                'placeholder' => trans('fields.Entity Columns'),
                'rows' => 5
            ]) !!}
            {!! Form::textarea('uses', [
                'value' => $entity->uses,
                'class' => 'form-control',
                'required' => false,
                'placeholder' => trans('fields.Uses'),
                'rows' => 5,
                'dir' => 'ltr',
            ]) !!}
            {!! Form::textarea('class_contents', [
                'value' => $entity->class_contents,
                'class' => 'form-control',
                'rows' => 5,
                'placeholder' => trans('fields.Class Contents'),
                'dir' => 'ltr',
                'rows' => 5,
                'required' => false
            ]) !!}
            <button class="btn btn-primary">{{ trans('fields.Submit') }}</button>
        </form>
        <a class="btn btn-danger" href="{{ route('simpleWorkflow.entities.createTable', $entity->id) }}" target="_blank">{{ trans('fields.Create Table') }}</a>
    </div>
@endsection
