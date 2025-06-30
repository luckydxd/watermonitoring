{{-- resources/views/admin/landingpage/partials/forms/_hero_block_form.blade.php --}}

{{-- Tentukan Aksi Form (Tambah atau Edit) --}}
@php
    $isEdit = isset($block);
    $actionUrl = $isEdit
        ? route('admin.landing.blocks.update', $block->id)
        : route('admin.landing.blocks.store', ['page' => $page->id]);
@endphp

{{-- Judul untuk Offcanvas --}}
<div data-form-title="{{ $isEdit ? 'Edit Hero Section' : 'Tambah Hero Section' }}"></div>

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- Hidden input untuk tipe blok saat membuat baru --}}
    <input type="hidden" name="block_type" value="HeroBlock">

    <div class="mb-3">
        <label for="headline" class="form-label">Headline</label>
        <input type="text" class="form-control" id="headline" name="headline"
            value="{{ old('headline', $block->blockable->headline ?? '') }}" required>
    </div>

    <div class="mb-3">
        <label for="description" class="form-label">Deskripsi</label>
        <textarea class="form-control" id="description" name="description" rows="4">{{ old('description', $block->blockable->description ?? '') }}</textarea>
    </div>

    <div class="mb-3">
        <label for="image_path" class="form-label">Gambar</label>
        <input class="form-control" type="file" id="image_path" name="image_path">
        @if ($isEdit && $block->blockable->image_path)
            <div class="mt-2">
                <small>Gambar saat ini:</small><br>
                <img src="{{ asset('storage/' . $block->blockable->image_path) }}" alt="image" height="100">
            </div>
        @endif
    </div>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="offcanvas">Batal</button>
        <button type="submit" class="btn btn-primary">Simpan</button>
    </div>
</form>
