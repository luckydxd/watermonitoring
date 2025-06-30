<div class="block-card mb-2 rounded p-3" data-id="{{ $block->id }}">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center">
            <span class="handle" style="cursor: move;">☰</span>
            <div>
                <h6 class="mb-0">
                    {{-- Menampilkan nama tipe blok, e.g., "HeroBlock" --}}
                    {{ class_basename($block->blockable_type) }}
                </h6>
                <small class="text-muted">
                    {{-- Menampilkan judul/headline dari blok tersebut --}}
                    {{ $block->blockable->title ?? ($block->blockable->headline ?? 'Blok Konten #' . $block->id) }}
                </small>
            </div>
        </div>
        <div class="block-actions">
            {{-- Tombol Edit untuk memicu AJAX --}}
            <button type="button" class="btn btn-sm btn-outline-info edit-block-btn"
                data-url="{{ route('admin.landing.blocks.edit', $block->id) }}">
                <i class="ti ti-edit"></i>
            </button>

            {{-- Tombol Hapus untuk memicu AJAX & Notiflix --}}
            <button type="button" class="btn btn-sm btn-outline-danger delete-block-btn"
                data-url="{{ route('admin.landing.blocks.destroy', $block->id) }}" data-block-id="{{ $block->id }}">
                <i class="ti ti-trash"></i>
            </button>
        </div>
    </div>
</div>
