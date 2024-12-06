<form action="javascript:void(0)" method="POST" id="city-detail">
    {{-- @method('PUT') --}}
    @csrf
    <input type="hidden" name="id" id="" value="{{ $city->id }}">
    <div class="col-sm-12 mt-2">
        <label for="city" class="col-sm-12">شهر :</label>
        <input type="text" id="city" name="city" value="{{ $city->city }}" class="col-sm-12 mb-2">
    </div>
    <div class="col-sm-12 mt-2">
        <label for="province" class="col-sm-12">استان :</label>
        <input type="text" id="province" name="province" value="{{ $city->province }}" class="col-sm-12 mb-2">
    </div>
    <div class="col-sm-12 mt-2">
        <label for="longitude" class="col-sm-12">longitude :</label>
        <input type="text" id="longitude" name="longitude" value="{{ $city->longitude }}" class="col-sm-12 mb-2">
    </div>
    <div class="col-sm-12 mt-2">
        <label for="latitude" class="col-sm-12">latitude :</label>
        <input type="text" id="latitude" name="latitude" value="{{ $city->latitude }}" class="col-sm-12 mb-2">
    </div>

    <button type="submit" onclick="update()" class="col-sm-12 mt-2 btn btn-primary">بروزرسانی</button>
</form>

<script>
    function update() {
        fd = new FormData($('#city-detail')[0])
        send_ajax_formdata_request(
            "{{ route('city.update') }}",
            fd,
            function(res) {
                show_message(res);
                refresh_table();
            }
        )
    }
</script>
