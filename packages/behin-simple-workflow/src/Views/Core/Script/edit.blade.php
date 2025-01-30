@extends('behin-layouts.app')

@section('title')
    اصلاح اسکریپت: {{ $script->name }}
@endsection

@php
    $filePath = base_path('packages/behin-simple-workflow/src/Controllers/Scripts/' . $script->executive_file . '.php');
    if (file_exists($filePath)) {
        $executive_file_content = File::get($filePath);
    } else {
        $executive_file_content = '';
    }
@endphp

@section('content')
    <h1>Edit Script</h1>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="row">
        <div class="col-md-6 card">
            <form action="{{ route('simpleWorkflow.scripts.update', $script->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="{{ $script->name }}" required>
                </div>
                <div class="form-group">
                    <label>Executive File:</label>
                    <select name="executive_file" class="form-control">
                        @foreach (File::files(base_path('packages/behin-simple-workflow/src/Controllers/Scripts')) as $file)
                            <option value="{{ str_replace('.php', '', $file->getFilename()) }}"
                                {{ $script->executive_file . '.php' == $file->getFilename() ? 'selected' : '' }}>
                                {{ $file->getFilename() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group">
                    <label>Content (JSON):</label>
                    <textarea name="content">{{ $script->content }}</textarea>
                </div> --}}
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
        <div class="col-md-6 card">
            <form action="javascript:void(0)" method="POST" id="test-form" class="form-inline">
                @csrf
                <div class="form-group">
                    <label for="caseId">{{ trans('fields.Case') }}</label>
                    <input type="text" name="caseId" id="caseId" class="form-control" list="cases">
                    <datalist id="cases">
                        <option value="">{{ trans('fields.Choose') }}</option>
                        @foreach (getCases() as $case)
                            <option value="{{ $case->id }}">{{ $case->number }} {{ $case->name }} </option>
                        @endforeach
                    </datalist>
                </div>
                <button type="submit" class="btn btn-primary ml-2" onclick="test()">{{ trans('fields.Test') }}</button>
            </form>
            <h5 class="mt-3" dir="ltr" >
                <pre style="text-align: left; white-space: pre;" dir="ltr">{{ trans('fields.Result') }}</pre>
            </h5>
            <div id="result" dir="ltr" style="text-align: left; white-space: pre;"></div>
        </div>
        <div class="col-md-12 card" dir="ltr">
            @if ($executive_file_content)
                <form action="{{ route('simpleWorkflow.scripts.update', $script->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-primary mt-3">{{ trans('fields.Save') }}</button>
                    <textarea name="executive_file_content" id="executive_file_content" class="form-control" rows="50"
                        style="text-align: left; white-space: pre; font-family: Monospace " dir="ltr">{{ $executive_file_content }}</textarea>
                    <button type="submit" class="btn btn-primary mt-3">{{ trans('fields.Save') }}</button>
                </form>
            @else
                <form action="{{ route('simpleWorkflow.scripts.store', $script->id) }}" method="POST">
                    @csrf
                    <input type="hidden" name="name" id="" value="{{ $script->name }}">
                    <label for="executive_file">{{ trans('fields.Executive File') }}</label>
                    <input type="text" name="executive_file" class="form-control" id="executive_file"
                        value="{{ $script->executive_file }}" placeholder="{{ trans('Without .php') }}">

                    <button type="submit" class="btn btn-primary mt-3">{{ trans('fields.Save') }}</button>
                </form>
            @endif

        </div>
    </div>
@endsection

@section('script')
    <script>
        function test() {
            var form = $('#test-form')[0];
            var fd = new FormData(form);
            send_ajax_formdata_request(
                "{{ route('simpleWorkflow.scripts.test', $script->id) }}",
                fd,
                function(response) {
                    console.log(response);
                    $('#result').html('<pre style="text-align: left; white-space: pre;" dir="ltr">' + response + '</pre>');
                },
                function(er) {
                    console.log(er);
                    result = er.responseJSON.message
                    if (result) {
                        $('#result').html('<pre style="text-align: left; white-space: pre;" dir="ltr">' + result + '</pre>');
                    } else {
                        $('#result').html('{{ trans('fields.True') }}')
                    }
                    hide_loading();
                }
            )
        }
    </script>
@endsection
