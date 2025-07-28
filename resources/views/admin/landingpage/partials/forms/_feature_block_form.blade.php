{{-- resources/views/admin/landingpage/partials/forms/_feature_block_form.blade.php --}}

@php
    $isEdit = isset($block);
    $actionUrl = $isEdit ? route('admin.landing.blocks.update', $block->id) : route('admin.landing.blocks.store');
@endphp

<div data-form-title="{{ $isEdit ? 'Edit Daftar Fitur' : 'Tambah Daftar Fitur' }}">
</div>

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="block_type" value="FeatureBlock">

    {{-- Kolom utama --}}
    <div class="mb-3"><label class="form-label">Tag</label><input type="text" class="form-control" name="tag"
            value="{{ old('tag', $block->blockable->tag ?? 'FITUR') }}"></div>
    <div class="mb-3"><label class="form-label">Judul</label><input type="text" class="form-control" name="title"
            value="{{ old('title', $block->blockable->title ?? '') }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="description" rows="3">{{ old('description', $block->blockable->description ?? '') }}</textarea>
    </div>
    <div class="mb-3"><label class="form-label">Gambar</label><input class="form-control" type="file"
            name="image_path">
        @if ($isEdit && $block->blockable->image_path)
            <img src="{{ asset('storage/' . $block->blockable->image_path) }}" alt="image" height="100"
                class="mt-2">
        @endif
    </div>
    <hr>

    {{-- Bagian untuk Repeating Items --}}
    <h5>Item Fitur</h5>
    <div id="feature-items-wrapper">
        @if ($isEdit && $block->blockable->items->count() > 0)
            @foreach ($block->blockable->items as $index => $item)
                @include('admin.landingpage.partials.forms.items._feature_item', [
                    'index' => $index,
                    'item' => $item,
                ])
            @endforeach
        @endif
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary add-item-btn mt-2" data-wrapper="feature-items-wrapper"
        data-template="feature-item-template">Tambah Item</button>
    <hr>

    <button type="submit" class="btn btn-primary me-2">Simpan</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Batal</button>
</form>

{{-- TEMPLATE UNTUK ITEM BARU --}}
<template id="feature-item-template">
    @include('admin.landingpage.partials.forms.items._feature_item', ['index' => 'INDEX', 'item' => null])
</template>
