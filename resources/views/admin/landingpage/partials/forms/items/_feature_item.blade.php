{{-- File: _feature_item.blade.php --}}
<div class="row align-items-center repeating-item mb-2">
    <div class="col-md-5">
        <input type="text" class="form-control" name="items[{{ $index }}][icon_class]" placeholder="Kelas Ikon"
            value="{{ $item->icon_class ?? '' }}">
    </div>
    <div class="col-md-6">
        <input type="text" class="form-control" name="items[{{ $index }}][text]" placeholder="Teks Fitur"
            value="{{ $item->text ?? '' }}" required>
    </div>
    <div class="col-md-1">
        <button type="button" class="btn btn-sm btn-danger remove-item-btn">X</button>
    </div>
</div>
