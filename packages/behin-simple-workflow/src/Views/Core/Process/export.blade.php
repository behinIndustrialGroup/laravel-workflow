@extends('behin-layouts.app')

@section('title')
    {{ trans('Export Process') }}
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">{{ trans('Export Process') }}</h3>
                    </div>
                    <div class="card-body">
                        <pre id="process-json" class="p-3 border bg-light">{{ $json }}</pre>
                    </div>
                    <div class="card-footer">
                        <button onclick="download_json()" class="btn btn-primary">{{ trans('Download') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
<script>
function download_json(){
    var data = $('#process-json').text();
    var blob = new Blob([data], {type: 'application/json'});
    var url = URL.createObjectURL(blob);
    var a = document.createElement('a');
    a.href = url;
    a.download = 'process.json';
    a.click();
    URL.revokeObjectURL(url);
}
</script>
@endsection
