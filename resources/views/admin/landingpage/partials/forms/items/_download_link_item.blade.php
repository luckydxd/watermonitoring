<div class="row align-items-center repeating-item border-bottom mb-3 pb-3">
    <div class="col-12 col-md-6 mb-2">
        <label class="form-label form-label-sm">Nama Platform</label>
        <input type="text" class="form-control" name="items[{{ $index }}][platform]" placeholder="e.g., iPhone"
            value="{{ $item->platform ?? '' }}">
    </div>
    <div class="col-12 col-md-6 mb-2">
        <label class="form-label form-label-sm">URL Download</label>
        <input type="url" class="form-control" name="items[{{ $index }}][url]" placeholder="https://..."
            value="{{ $item->url ?? '' }}">
    </div>
    <div class="col-12 col-md-8">
        <label class="form-label form-label-sm">Ikon (Gambar)</label>
        <input type="file" class="form-control" name="items[{{ $index }}][icon_path]">
        @if (isset($item) && $item->icon_path)
            <input type="hidden" name="items[{{ $index }}][existing_icon_path]" value="{{ $item->icon_path }}">
        @endif
    </div>
    <div class="col-6 col-md-3">
        @if (isset($item) && $item->icon_path)
            <img src="{{ asset('storage/' . $item->icon_path) }}" alt="ikon" class="img-thumbnail mt-2"
                width="50">
        @endif
    </div>
    <div class="col-6 col-md-1 text-end">
        <button type="button" class="btn btn-sm btn-danger remove-item-btn mt-3">X</button>
    </div>
</div>
