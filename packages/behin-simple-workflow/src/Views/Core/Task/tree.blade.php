@php
    // ایجاد فاصله برای سطح فعلی درخت
    $indentation = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
@endphp

@foreach ($children as $child)
    @php
        $bgColor = $child->type == 'form' ? 'bg-primary' : ($child->type == 'script' ? 'bg-success' : 'bg-warning');
    @endphp
    {{-- <div class=""> --}}
        <div class=" p-2 bg-light">

            {!! $indentation !!}
            <a type="submit" class="" style=""
                    href="{{ route('simpleWorkflow.task.edit', $child->id) }}"><i class="fa fa-edit"></i></a>
            <strong class="">
                <a data-toggle="collapse" href="#{{ $child->id }}">{{ $child->name }}</a>
                <span class="badge {{ $bgColor }}">
                    {{ ucfirst($child->type) }}
                </span>
                <input type="hidden" name="id" value="{{ $child->id }}">
                <div class="" style="display: inline">
                    @if ($child->next_element_id)
                        @php
                            $bgColor =
                                $child->nextTask()->type == 'form'
                                    ? 'bg-primary'
                                    : ($child->nextTask()->type == 'script'
                                        ? 'bg-success'
                                        : 'bg-warning');
                        @endphp
                        <span class="badge {{ $bgColor }}">{{ trans('Next Task') }} :
                            {{ $child->nextTask()->name }}
                        </span>
                    @endif
                </div>

            </strong>
            @if ($error = taskHasError($child->id))
                <i class="fa fa-exclamation-triangle text-danger" title="{{ $error['descriptions'] }}"></i>
            @endif


        </div>

        <div id="{{ $child->id }}" class="">
                @if (count($child->children()))
                    @include('SimpleWorkflowView::Core.Task.tree', [
                        'children' => $child->children(),
                        'level' => $level +1,
                    ])
                @endif
        </div>

    {{-- </div> --}}
@endforeach
