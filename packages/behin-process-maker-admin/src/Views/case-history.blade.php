{{-- @extends(config('pm_config.layout_name')) --}}

{{-- @section('content') --}}
    <div class="table-responsive">
        <table class="table" id="list">
            <thead>
                <tr>
                    <th>{{__('del_index')}}</th>
                    <th>{{__('tas_type')}}</th>
                    <th>{{__('del_init_date')}}</th>
                    <th>{{__('tas_title')}}</th>
                    <th>{{__('status')}}</th>
                    <th>{{__('del_finish_date')}}</th>
                    <th>{{__('usr_firstname')}}</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $item)
                @php
                    if($item[0]['status'] == 'TASK_IN_PROGRESS' and $item[0]['del_finish_date'] == 'Not finished'){
                        $color = '#fceae8';
                    }else{
                        $color = '#f4fae1';
                    }
                @endphp
                    <tr style="background: {{$color}}">
                        <td>{{$item[0]['del_index']}}</td>
                        <td>{{trans($item[0]['tas_type'])}}</td>
                        <td>
                            @if ($item[0]['tas_type'] == 'SCRIPT-TASK')
                                {{trans($item[0]['del_finish_date'])}}
                            @else
                                {{trans($item[0]['del_init_date'])}}
                            @endif
                        </td>
                        <td>{{$item[0]['tas_title']}}</td>
                        <td>{{trans($item[0]['status'])}}</td>
                        <td>{{trans($item[0]['del_finish_date'])}}</td>
                        <td>{{$item[0]['usr_firstname']}} {{$item[0]['usr_lastname']}}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
