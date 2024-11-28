@extends('behin-layouts.app')

@section('content')
    <div class="box">
        <div class="card">
            <form action="javascript:void()" id="method-form">
                @csrf
                <table class="table table-stripped" id="method-table">
                    <thead>
                        <tr>
                            <th>شناسه</th>
                            <th>نام</th>
                            <th>دسته بندی</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($methods as $method)
                            <tr>
                                <td>{{ $method->id }}</td>
                                <td>{{ $method->name }}</td>
                                <td><input type="text" value="{{ $method?->category }}" placeholder="نام دسته بندی ..." name="{{ $method->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </form>
            <button onclick="submit()">submit</button>
        </div>
    </div>
@endsection

<script>
    function submit() {
        send_ajax_request(
            "{{ route('method.edit') }}",
            $('#method-form').serialize(),
            function(data) {
                console.log(data);
            }
        )
    }
</script>

