@extends('behin-layouts.welcome')

@section('content')

@endsection

@section('script')
    <script>
        $.get('https://survey.porsline.ir/file/b74ab4fe6d9f882c-x0chO1h1/359859538/32368695-034-امضا.png/download?hli=1', function(response){
            return response;
        })
    </script>
@endsection