@php
    $selectedField = isset($selectedField) ? $selectedField : null;
@endphp
@foreach (getProcessFields() as $field)
    <option dir="ltr" value="{{ $field->name }}" {{ $field->name == $selectedField ? 'selected' : '' }}>{{ $field->type }}:
        {{ $field->name }}
    </option>
@endforeach
@foreach (getProcessForms() as $item)
    @if ($item->id != $form->id)
        <option dir="ltr" value="{{ $item->id }}" {{ $item->id == $selectedField ? 'selected' : '' }}>Form: {{ $item->name }}
        </option>
    @endif
@endforeach
