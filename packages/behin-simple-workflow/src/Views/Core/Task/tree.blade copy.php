@php
    // ایجاد فاصله برای سطح فعلی درخت
    $indentation = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
@endphp

@foreach ($children as $child)
    @php
        $bgColor = $child->type == 'form' ? 'bg-primary' : ($child->type == 'script' ? 'bg-success' : 'bg-warning');
    @endphp
    <div class="panel panel-default">
        <div class="panel-heading p-2 bg-light">
            @if ($error = taskHasError($child->id))
                <i class="fa fa-exclamation-triangle text-danger" title="{{ $error['descriptions'] }}"></i>
            @endif
            {!! $indentation !!}

            <strong class="panel-title">
                ><a data-toggle="collapse" href="#{{ $child->id }}">{{ $child->name }}</a>
                <span class="badge {{ $bgColor }}">
                    {{ ucfirst($child->type) }}
                </span>
                <input type="hidden" name="id" value="{{ $child->id }}">
                <div class="" style="display: inline">
                    <span class="badge {{ $bgColor }}">{{ trans('Executive File') }} :
                        {{ $child->executive_element_id ? $child->executiveElement()->name : '' }}
                    </span>
                    @if ($child->assignment_type)
                        <span class="badge {{ $bgColor }}">{{ trans('Assignment') }}:
                            {{ $child->assignment_type }}
                        </span>
                    @endif
                    @if ($child->actors()->count() > 0)
                        <span class="badge bg-info">{{ trans('Actors') }}:
                            {{ $child->actors()->pluck('actor')->implode(', ') }}
                        </span>
                    @endif
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
                <a type="submit" class="" style="float: left"
                    href="{{ route('simpleWorkflow.task.edit', $child->id) }}">{{ trans('Edit') }}</a>
            </strong>

        </div>

        <div id="{{ $child->id }}" class="panel-collapse">
            <div class="panel-body">
                @if (count($child->children()))
                    @include('SimpleWorkflowView::Core.Task.tree', [
                        'children' => $child->children(),
                        'level' => $level + 1,
                    ])
                @endif
            </div>
        </div>

    </div>
@endforeach
