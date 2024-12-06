@extends('behin-layouts.app')

@section('content')
    <h1>Edit Script</h1>
    <div class="row">
        <div class="col-md-6">
            <form action="{{ route('simpleWorkflow.scripts.update', $script->id) }}" method="POST">
                @csrf
                @method('PUT')
                <div class="form-group">
                    <label>Name:</label>
                    <input type="text" name="name" value="{{ $script->name }}" required>
                </div>
                <div class="form-group">
                    <label>Executive File:</label>
                    <select name="executive_file" class="form-control">
                        @foreach (File::files(base_path('packages/behin-simple-workflow/src/Controllers/Scripts')) as $file)
                            <option value="{{ str_replace('.php', '', $file->getFilename()) }}"
                                {{ $script->executive_file == $file->getFilename() ? 'selected' : '' }}>
                                {{ $file->getFilename() }}
                            </option>
                        @endforeach
                    </select>
                </div>
                {{-- <div class="form-group">
                    <label>Content (JSON):</label>
                    <textarea name="content">{{ $script->content }}</textarea>
                </div> --}}
                <button type="submit" class="btn btn-primary">Update</button>
            </form>
        </div>
    </div>
@endsection
