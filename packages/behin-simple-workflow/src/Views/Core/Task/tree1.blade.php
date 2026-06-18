@foreach ($children as $child)
    @php
        // تعیین کلاس هر نوع تسک
        switch ($child->type) {
            case 'form': $taskClass = 'task-form'; $shapeStart = '('; $shapeEnd = ')'; break;
            case 'script': $taskClass = 'task-script'; $shapeStart = '['; $shapeEnd = ']'; break;
            case 'condition': $taskClass = 'task-condition'; $shapeStart = '{'; $shapeEnd = '}'; break;
            // case 'end': $taskClass = 'task-end'; $shapeStart = '(('; $shapeEnd = '))'; break;
            case 'timed_condition': $taskClass = 'task-timed_condition'; $shapeStart = '['; $shapeEnd = ']'; break;
            default: $taskClass = 'task-default'; $shapeStart = '['; $shapeEnd = ']'; break;
        }
        $taskName = $child->name . ($child->is_preview ? ' (' . trans('fields.Preview') . ')' : '');
    @endphp

    {{-- لینک پدر به فرزند --}}
    {{ $task->id }}-->{{ $child->id }}{{ $shapeStart }}"{{ $taskName }}"{{ $shapeEnd }}:::{{ $taskClass }}

    {{-- اضافه‌کردن لینک قابل کلیک به نود --}}
    click {{ $child->id }} "{{ route('simpleWorkflow.task.edit', $child->id) }}" "{{ trans('fields.Edit') }}"

    @php
        $children = $child->children();
    @endphp

    {{-- اگر المنت بعدی دارد --}}
    @if ($child->next_element_id)
        {{ $child->id }} --> {{ $child->next_element_id }}
    @endif

    {{-- اگر فرزند دارد بازگشتی تکرار کن --}}
    @if (count($children))
        @include('SimpleWorkflowView::Core.Task.tree1', [
            'children' => $children,
            'task' => $child,
        ])
    @endif
@endforeach
