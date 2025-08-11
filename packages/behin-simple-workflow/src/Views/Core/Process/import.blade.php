@extends('behin-layouts.app')

@section('title')
    {{ trans('Import Process') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('Import Process') }}</h3>
                    </div>
                    <div class="card-body">
                        <form id="import-process-form" action="javascript:void(0)">
                            <div class="form-group">
                                <textarea id="process-json" class="form-control" rows="10"
                                    placeholder="{{ trans('Paste process JSON here') }}"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="card-footer">
                        <button onclick="import_process()" class="btn btn-primary">{{ trans('Import') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function import_process(){
    var json = $('#process-json').val();
    $.ajax({
        url: "{{ route('simpleWorkflow.process.import') }}",
        data: json,
        processData: false,
        contentType: 'application/json',
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        success: function(response){
            show_message("{{ trans('Process imported successfully') }}");
            console.log(response);
        },
        error: function(response){
            show_error(response);
        }
    });
}
</script>
@endsection
