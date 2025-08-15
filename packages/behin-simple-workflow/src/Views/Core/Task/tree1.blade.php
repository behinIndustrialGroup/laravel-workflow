@foreach ($children as $child)
    @php
        if ($child->type == 'form') {
            $taskClass = 'task-form';
        }
        if ($child->type == 'script') {
            $taskClass = 'task-script';
        }
        if ($child->type == 'condition') {
            $taskClass = 'task-condition';
        }
        if ($child->type == 'end') {
            $taskClass = 'task-end';
        }
        if ($child->type == 'timed_condition') {
            $taskClass = 'task-timed_condition';
        }

    @endphp
    {{ $task->id }} --> {{ $child->id }}["<a type='submit' class="{{ $taskClass }} task-edit-link"
        href='{{ route('simpleWorkflow.task.edit', $child->id) }}'>{{ $child->name }}</a>"]:::{{ $taskClass }}
    @php
        $children = $child->children();
    @endphp
    @if ($child->next_element_id)
        {{ $child->id }} --> {{ $child->next_element_id }}
    @endif
    @if (count($children))
        @include('SimpleWorkflowView::Core.Task.tree1', [
            'children' => $children,
            'task' => $child,
        ])
    @endif
@endforeach
