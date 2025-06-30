@extends('layouts.app') {{-- Pastikan ini sesuai dengan layout admin Anda --}}

@section('title', 'Editor Landing Page')

@push('css')
    {{-- Kita bisa tetap menggunakan CSS dari template Anda untuk konsistensi tampilan --}}
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/select2/select2.css') }}" />
    {{-- CSS untuk DataTables bisa dihapus jika tidak digunakan di halaman lain, tapi tidak masalah jika dibiarkan --}}
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/libs/datatables-bs5/datatables.bootstrap5.css') }}" />

    {{-- CSS tambahan untuk tampilan kartu blok --}}
    <style>
        .block-card {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            transition: box-shadow .2s;
        }

        .block-card .handle {
            cursor: move;
            font-size: 1.2rem;
            margin-right: 15px;
            color: #a1acb8;
        }

        .block-card:hover {
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, .075);
        }

        /* Style untuk item yang sedang di-drag oleh SortableJS */
        .sortable-ghost {
            background-color: #e9ecef;
            border-style: dashed;
        }
    </style>
@endpush

@section('content')
    <div class="container-xxl flex-grow-1 container-p-y">
        {{-- Menampilkan notifikasi sukses --}}
        @if (session('success'))
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        @endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Susunan Konten Landing Page</h5>
                {{-- Tombol untuk memicu Offcanvas/Modal tambah blok baru --}}
                <button type="button" class="btn btn-primary" data-bs-toggle="offcanvas"
                    data-bs-target="#offcanvasAddBlock">
                    <i class="ti ti-plus me-1"></i> Tambah Blok Baru
                </button>
            </div>

            {{-- Ini adalah area "Page Builder" kita, menggantikan <table> --}}
            <div class="card-body">
                {{-- 
        Wadah utama ini sekarang berada di luar perulangan. 
        Ini memastikan elemen dengan id="content-blocks-container" SELALU ada di halaman,
        bahkan saat tidak ada blok sama sekali, sehingga JavaScript tidak akan error.
    --}}
                <div id="content-blocks-container">

                    {{-- 
            @forelse adalah cara Blade yang elegan untuk melakukan loop.
            Ia akan menjalankan 'include' untuk setiap blok jika ada.
            Jika tidak ada blok sama sekali, ia akan menjalankan bagian @empty.
        --}}
                    @forelse($page->content_blocks as $block)
                        {{-- 
                Kita memanggil partial view yang sudah kita standarisasi.
                Ini membuat kode di sini tetap bersih dan mudah dibaca.
            --}}
                        @include('landing-page.partials._block_card', ['block' => $block])

                    @empty

                        {{-- Bagian ini hanya akan ditampilkan jika $page->content_blocks kosong --}}
                        <div id="empty-block-message" class="p-5 text-center">
                            <p>Belum ada konten di halaman ini.</p>
                            <p>Silakan klik tombol "Tambah Blok Baru" untuk memulai.</p>
                        </div>
                    @endforelse
                </div>
            </div>
            <div class="card-footer text-muted">
                <i class="ti ti-info-circle"></i> Anda bisa mengubah urutan blok dengan cara drag-and-drop pada ikon ☰.
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasAddBlock" aria-labelledby="offcanvasAddBlockLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasAddBlockLabel" class="offcanvas-title">Pilih Tipe Blok Baru</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            <p>Pilih jenis konten yang ingin Anda tambahkan ke halaman:</p>

            {{-- Ganti link-link lama dengan ini --}}
            <div class="list-group">
                <a href="{{ route('admin.landing.blocks.create', ['page' => $page, 'type' => 'hero_block']) }}"
                    class="list-group-item list-group-item-action add-block-link">Hero Section</a>
                <a href="{{ route('admin.landing.blocks.create', ['page' => $page, 'type' => 'feature_block']) }}"
                    class="list-group-item list-group-item-action add-block-link">Daftar Fitur</a>
                <a href="{{ route('admin.landing.blocks.create', ['page' => $page, 'type' => 'download_block']) }}"
                    class="list-group-item list-group-item-action add-block-link">Download Aplikasi</a>
                <a href="{{ route('admin.landing.blocks.create', ['page' => $page, 'type' => 'video_block']) }}"
                    class="list-group-item list-group-item-action add-block-link">Video</a>
                <a href="{{ route('admin.landing.blocks.create', ['page' => $page, 'type' => 'faq_block']) }}"
                    class="list-group-item list-group-item-action add-block-link">FAQ</a>
            </div>
        </div>
    </div>

    {{-- Di sini Anda akan menempatkan Offcanvas untuk FORM TAMBAH dan FORM EDIT setiap tipe blok --}}
    {{-- Contoh Offcanvas untuk Form Tambah/Edit (bisa dibuat terpisah dan dipanggil dengan AJAX) --}}

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasFormContainer" aria-labelledby="offcanvasFormLabel">
        <div class="offcanvas-header">
            <h5 id="offcanvasFormLabel" class="offcanvas-title">Memuat Form...</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body mx-0 flex-grow-0">
            {{-- Konten form akan dimuat di sini oleh JavaScript --}}
            <div id="form-content-wrapper">
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@push('scripts')
    {{-- Library untuk Drag-and-Drop --}}
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // ==========================================================
            // INISIALISASI VARIABEL GLOBAL
            // ==========================================================
            const sortableContainer = document.getElementById('content-blocks-container');
            const offcanvasElement = document.getElementById('offcanvasFormContainer');
            const offcanvas = new bootstrap.Offcanvas(offcanvasElement);
            const offcanvasTitle = document.getElementById('offcanvasFormLabel');
            const offcanvasBody = document.getElementById('form-content-wrapper');

            // ==========================================================
            // BAGIAN 1: LOGIKA UNTUK SORTABLEJS (DRAG-AND-DROP)
            // ==========================================================
            if (sortableContainer) {
                new Sortable(sortableContainer, {
                    animation: 150,
                    handle: '.handle',
                    ghostClass: 'sortable-ghost',
                    onEnd: function(evt) {
                        const order = Array.from(sortableContainer.children).map(child => child.dataset
                            .id);
                        Notiflix.Loading.standard('Menyimpan urutan...');
                        fetch('{{ route('admin.landing.blocks.reorder') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({
                                    order: order
                                })
                            })
                            .then(response => response.ok ? response.json() : Promise.reject(
                                'Gagal menyimpan urutan.'))
                            .then(data => Notiflix.Notify.success(data.message ||
                                'Urutan berhasil disimpan!'))
                            .catch(error => Notiflix.Notify.failure(error.toString()))
                            .finally(() => Notiflix.Loading.remove());
                    }
                });
            }

            // ==========================================================
            // BAGIAN 2: LOGIKA UNTUK MEMUAT FORM KE OFFCANVAS
            // ==========================================================
            async function loadFormIntoOffcanvas(url) {
                Notiflix.Loading.standard('Memuat form...');
                offcanvasTitle.innerText = 'Memuat...';
                offcanvas.show();
                try {
                    const response = await fetch(url);
                    if (!response.ok) throw new Error(`Gagal memuat form (Status: ${response.status})`);
                    const html = await response.text();
                    offcanvasBody.innerHTML = html;
                    const titleElement = offcanvasBody.querySelector('[data-form-title]');
                    if (titleElement) offcanvasTitle.innerText = titleElement.dataset.formTitle;
                } catch (error) {
                    offcanvas.hide();
                    Notiflix.Notify.failure(error.toString());
                } finally {
                    Notiflix.Loading.remove();
                }
            }

            // ==========================================================
            // BAGIAN 3: EVENT DELEGATION UNTUK SEMUA AKSI KLIK
            // ==========================================================
            document.addEventListener('click', function(event) {
                // --- Pemicu untuk Buka Form Tambah/Edit & Hapus Blok ---
                const addLink = event.target.closest('.add-block-link');
                const editButton = event.target.closest('.edit-block-btn');
                const deleteButton = event.target.closest('.delete-block-btn');

                if (addLink || editButton) {
                    event.preventDefault();
                    const targetElement = addLink || editButton;
                    loadFormIntoOffcanvas(targetElement.getAttribute('href') || targetElement.dataset.url);
                }

                if (deleteButton) {
                    event.preventDefault();
                    handleDeleteBlock(deleteButton);
                }

                // --- Pemicu untuk Tambah/Hapus Item di DALAM form ---
                const addItemBtn = event.target.closest('.add-item-btn');
                if (addItemBtn) {
                    handleAddItem(addItemBtn);
                }

                const removeItemBtn = event.target.closest('.remove-item-btn');
                if (removeItemBtn) {
                    removeItemBtn.closest('.repeating-item').remove();
                }
            });

            // ==========================================================
            // BAGIAN 4: EVENT LISTENER UNTUK SUBMIT FORM VIA AJAX
            // ==========================================================
            offcanvasElement.addEventListener('submit', async function(event) {
                if (event.target.tagName.toLowerCase() === 'form') {
                    event.preventDefault();
                    Notiflix.Loading.standard('Menyimpan...');
                    const form = event.target;
                    const formData = new FormData(form);
                    const actionUrl = form.getAttribute('action');
                    const method = form.querySelector('input[name="_method"]')?.value || 'POST';

                    try {
                        const response = await fetch(actionUrl, {
                            method: 'POST',
                            body: formData,
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await response.json();
                        if (!response.ok) {
                            if (response.status === 422) {
                                displayValidationErrors(data.errors, form);
                                Notiflix.Notify.failure('Periksa kembali isian Anda.');
                            } else {
                                throw new Error(data.message || 'Terjadi kesalahan server.');
                            }
                        } else {
                            offcanvas.hide();
                            Notiflix.Notify.success(data.message);
                            // Jika ini adalah proses tambah, tambahkan kartu baru. Jika edit, refresh halaman.
                            if (method === 'POST' && data.newBlockHtml) {
                                sortableContainer.insertAdjacentHTML('beforeend', data.newBlockHtml);
                            }
                            const emptyMessage = document.getElementById('empty-block-message');
                            if (emptyMessage) {
                                emptyMessage.remove();
                            } else {
                                window.location.reload(); // Cara termudah untuk melihat perubahan edit
                            }
                        }
                    } catch (error) {
                        Notiflix.Notify.failure(error.toString());
                    } finally {
                        Notiflix.Loading.remove();
                    }
                }
            });

            // ==========================================================
            // BAGIAN 5: FUNGSI-FUNGSI PEMBANTU (HELPERS)
            // ==========================================================

            function handleDeleteBlock(button) {
                const deleteUrl = button.dataset.url;
                const blockId = button.dataset.blockId;
                Notiflix.Confirm.show('Konfirmasi Penghapusan', 'Apakah Anda yakin ingin menghapus blok ini?',
                    'Ya, Hapus', 'Batal',
                    () => { // onOk
                        const blockCard = document.querySelector(`.block-card[data-id="${blockId}"]`);
                        Notiflix.Block.standard(blockCard, 'Menghapus...');
                        fetch(deleteUrl, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                    'Accept': 'application/json'
                                }
                            })
                            .then(response => response.ok ? response.json() : Promise.reject(
                                'Gagal menghapus blok.'))
                            .then(data => {
                                Notiflix.Notify.success(data.message);
                                blockCard.style.transition = 'opacity 0.5s';
                                blockCard.style.opacity = 0;
                                setTimeout(() => blockCard.remove(), 500);
                            })
                            .catch(error => Notiflix.Notify.failure(error.toString()))
                            .finally(() => Notiflix.Block.remove(blockCard));
                    },
                    () => {}, {
                        titleColor: '#ff4c51',
                        okButtonBackground: '#ff4c51'
                    }
                );
            }

            function handleAddItem(button) {
                const templateId = button.dataset.template;
                const wrapperId = button.dataset.wrapper;
                const template = document.getElementById(templateId);
                const wrapper = document.getElementById(wrapperId);

                if (template && wrapper) {
                    const index = Date.now();
                    let content = template.innerHTML.replace(/\[INDEX\]/g, `[${index}]`);
                    wrapper.insertAdjacentHTML('beforeend', content);
                }
            }

            function displayValidationErrors(errors, form) {
                form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
                form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                for (const field in errors) {
                    // Menangani field array seperti 'items.0.text'
                    const formattedField = field.replace(/\.(\d+)\./g, '[$1].');
                    const input = form.querySelector(`[name^="${formattedField}"]`);
                    if (input) {
                        input.classList.add('is-invalid');
                        const errorHtml = `<div class="invalid-feedback d-block">${errors[field][0]}</div>`;
                        input.closest('.form-control, .form-check').parentNode.insertAdjacentHTML('beforeend',
                            errorHtml);
                    }
                }
            }
        });
    </script>
@endpush
