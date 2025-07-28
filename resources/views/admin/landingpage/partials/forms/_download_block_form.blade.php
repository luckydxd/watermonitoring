{{-- resources/views/admin/landingpage/partials/forms/_download_block_form.blade.php --}}

@php
    $isEdit = isset($block);
    $actionUrl = $isEdit ? route('admin.landing.blocks.update', $block->id) : route('admin.landing.blocks.store');
@endphp

<div data-form-title="{{ $isEdit ? 'Edit Blok Download' : 'Tambah Blok Download' }}">
</div>

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="block_type" value="DownloadBlock">

    <div class="mb-3"><label class="form-label">Judul</label><input type="text" class="form-control" name="title"
            value="{{ old('title', $block->blockable->title ?? 'Download Aplikasi') }}" required></div>
    <div class="mb-3"><label class="form-label">Deskripsi</label>
        <textarea class="form-control" name="description" rows="3">{{ old('description', $block->blockable->description ?? '') }}</textarea>
    </div>
    <div class="mb-3"><label class="form-label">Gambar Mockup</label><input class="form-control" type="file"
            name="mockup_image_path">
        @if ($isEdit && $block->blockable->mockup_image_path)
            <img src="{{ asset('storage/' . $block->blockable->mockup_image_path) }}" alt="image" height="100"
                class="mt-2">
        @endif
    </div>
    <hr>

    <h5>Link Download</h5>
    <div id="download-links-wrapper">
        @if ($isEdit && $block->blockable->links->count() > 0)
            @foreach ($block->blockable->links as $index => $link)
                @include('admin.landingpage.partials.forms.items._download_link_item', [
                    'index' => $index,
                    'item' => $link,
                ])
            @endforeach
        @endif
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary add-item-btn mt-2"
        data-wrapper="download-links-wrapper" data-template="download-link-template">Tambah Link</button>
    <hr>


    <button type="submit" class="btn btn-primary me-2">Simpan</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Batal</button>
</form>

<template id="download-link-template">
    @include('admin.landingpage.partials.forms.items._download_link_item', [
        'index' => 'INDEX',
        'item' => null,
    ])
</template>
