{{-- resources/views/admin/landingpage/partials/forms/items/_faq_item.blade.php --}}
<div class="repeating-item mb-3 rounded border p-3">
    <div class="mb-2">
        <label class="form-label">Pertanyaan</label>
        <input type="text" class="form-control" name="items[{{ $index }}][question]"
            placeholder="Tulis pertanyaan..." value="{{ $item->question ?? '' }}" required>
    </div>
    <div class="mb-2">
        <label class="form-label">Jawaban</label>
        <textarea class="form-control" name="items[{{ $index }}][answer]" rows="3" placeholder="Tulis jawaban..."
            required>{{ $item->answer ?? '' }}</textarea>
    </div>
    <div class="text-end">
        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">Hapus Item</button>
    </div>
</div>
