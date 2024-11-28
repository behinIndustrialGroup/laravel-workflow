<div class="form-group">
    @if(isset($label))
        <label for="{{ $id }}">{{ $label }}</label>
    @endif
    {{ $slot }}
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>