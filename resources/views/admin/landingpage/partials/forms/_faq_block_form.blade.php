{{-- resources/views/admin/landingpage/partials/forms/_faq_block_form.blade.php --}}

@php
    $isEdit = isset($block);
    $actionUrl = $isEdit ? route('admin.landing.blocks.update', $block->id) : route('admin.landing.blocks.store');
@endphp

<div data-form-title="{{ $isEdit ? 'Edit Blok FAQ' : 'Tambah Blok FAQ' }}"></div>

<form action="{{ $actionUrl }}" method="POST" enctype="multipart/form-data">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif
    <input type="hidden" name="block_type" value="FaqBlock">

    <div class="mb-3"><label class="form-label">Judul Section</label><input type="text" class="form-control"
            name="title" value="{{ old('title', $block->blockable->title ?? 'Pertanyaan yang Sering Diajukan') }}"
            required></div>
    <hr>

    <h5>Daftar Pertanyaan & Jawaban</h5>
    <div id="faq-items-wrapper">
        @if ($isEdit && $block->blockable->items->count() > 0)
            @foreach ($block->blockable->items as $index => $item)
                @include('admin.landingpage.partials.forms.items._faq_item', [
                    'index' => $index,
                    'item' => $item,
                ])
            @endforeach
        @endif
    </div>
    <button type="button" class="btn btn-sm btn-outline-primary add-item-btn mt-2" data-wrapper="faq-items-wrapper"
        data-template="faq-item-template">Tambah Pertanyaan</button>
    <hr>


    <button type="submit" class="btn btn-primary me-2">Simpan</button>
    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="offcanvas">Batal</button>
</form>

<template id="faq-item-template">
    @include('admin.landingpage.partials.forms.items._faq_item', ['index' => 'INDEX', 'item' => null])
</template>
