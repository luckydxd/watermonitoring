{{-- resources/views/admin/landingpage/partials/forms/_video_block_form.blade.php --}}

@php
    $isEdit = isset($block);
    $actionUrl = $isEdit
        ? route('admin.landing.blocks.update', $block->id)
        : route('admin.landing.blocks.store', ['page' => $page->id]);
@endphp

<div data-form-title="{{ $isEdit ? 'Edit Blok Video' : 'Tambah Blok Video' }}"></div>

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="block_type" value="VideoBlock">

    <div class="mb-3">
        <label class="form-label">Judul Video (Opsional)</label>
        <input type="text" class="form-control" name="title"
            value="{{ old('title', $block->blockable->title ?? '') }}">
    </div>
    <div class="mb-3">
        <label class="form-label">File Video</label>
        <input class="form-control" type="file" name="video_path" {{ $isEdit ? '' : 'required' }}>
        @if ($isEdit && $block->blockable->video_path)
            <small class="d-block mt-2">Video saat ini: {{ basename($block->blockable->video_path) }}</small>
        @endif
    </div>
    <div class="mb-3">
        <label class="form-label">Gambar Thumbnail</label>
        <input class="form-control" type="file" name="thumbnail_path">
        @if ($isEdit && $block->blockable->thumbnail_path)
            <img src="{{ asset('storage/' . $block->blockable->thumbnail_path) }}" alt="thumbnail" height="100"
                class="mt-2">
        @endif
    </div>

    <div class="d-flex justify-content-end mt-4">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
