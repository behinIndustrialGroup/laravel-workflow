@php
    // ایجاد فاصله برای سطح فعلی درخت
    $indentation = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
@endphp

@foreach ($children as $child)
    <div class="panel panel-default">
            <div
                class="panel-heading p-2 bg-light">
                {!! $indentation !!}
                <strong class="panel-title">
                    <a data-toggle="collapse" href="#{{ $child->id }}">{{ $child->name }}</a>
                    <span
                        class="badge bg-{{ $child->type == 'form' ? 'primary' : ($child->type == 'script' ? 'success' : 'warning') }}">
                        {{ ucfirst($child->type) }}
                    </span>
                    <input type="hidden" name="id" value="{{ $child->id }}">
                    <div class="" style="display: inline">
                        <select name="executive_element_id" class="form-select">
                            <option value="">{{ trans('Select an option') }}</option>
                            @if ($child->type == 'form')
                                @foreach ($forms as $form)
                                    <option value="{{ $form->id }}"
                                        {{ $form->id == $child->executive_element_id ? 'selected' : '' }}>
                                        {{ $form->name }}
                                    </option>
                                @endforeach
                            @endif
                            @if ($child->type == 'script')
                                @foreach ($scripts as $script)
                                    <option value="{{ $script->id }}"
                                        {{ $script->id == $child->executive_element_id ? 'selected' : '' }}>
                                        {{ $script->name }}
                                    </option>
                                @endforeach
                            @endif
                            @if ($child->type == 'condition')
                                @foreach ($conditions as $condition)
                                    <option value="{{ $condition->id }}"
                                        {{ $condition->id == $child->executive_element_id ? 'selected' : '' }}>
                                        {{ $condition->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
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
