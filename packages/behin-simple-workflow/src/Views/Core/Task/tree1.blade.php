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

    @endphp
    {{ $task->id }} --> {{ $child->id }}["<a type='submit' target="_blank" class="{{ $taskClass }}"
        href='{{ route('simpleWorkflow.task.edit', $child->id) }}'>{{ $child->name }}</a>"]:::{{ $taskClass }}
    @php
        $children = $child->children();
    @endphp
    @if (count($children))
        @include('SimpleWorkflowView::Core.Task.tree1', [
            'children' => $children,
            'task' => $child,
        ])
    @endif
@endforeach
