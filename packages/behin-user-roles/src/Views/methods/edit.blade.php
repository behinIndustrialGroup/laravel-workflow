<div class="card">
    {{ $role->name }}
    <form action="javascript:void()" id="method-form">
        @csrf
        <input type="text" name="role_id" id="" value="{{ $role->id }}">
        <table class="table table-stripped">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>دسته بندی</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($methods as $method)
                    <tr>
                        <td><input type="checkbox" name="{{ $method->id }}" id="" {{ $method->access ? 'checked' : '' }}>{{ $method->name }}</td>
                        <td>{{ $method->category }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </form>
    <button onclick="submit()">submit</button>


</div>
<script>
    function submit() {
        send_ajax_request(
            "{{ route('role.edit') }}",
            $('#method-form').serialize(),
            function(data) {
                console.log(data);
            }
        )
    }
</script>
