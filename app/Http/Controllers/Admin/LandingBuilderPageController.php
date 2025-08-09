<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

// Import semua model blok Anda
use App\Models\HeroBlock;
use App\Models\FeatureBlock;
use App\Models\FaqBlock;
use App\Models\VideoBlock;
use App\Models\DownloadBlock;

class LandingBuilderPageController extends Controller
{
    /**
     * Menampilkan halaman editor utama (Page Builder).
     */
    public function editor()
    {
        // Kita asumsikan ID halaman utama selalu 1.
        // firstOrCreate akan membuat halaman jika belum ada saat pertama kali diakses.
        $page = Page::firstOrCreate(
            ['id' => 1],
            ['title' => 'Landing Page Utama', 'slug' => '/']
        );

        // Eager load semua blok kontennya, diurutkan berdasarkan posisi.
        // Ini sangat efisien karena menghindari N+1 query problem.
        $page->load(['content_blocks' => function ($query) {
            $query->orderBy('position', 'asc');
        }, 'content_blocks.blockable']);

        // Kirim data halaman ke view.
        return view('admin.landingpage.editor', compact('page'));
    }

    /**
     * Menampilkan form untuk membuat blok baru.
     * (Akan dipanggil oleh modal/popup di frontend)
     */
    public function createBlock(Request $request, Page $page)
    {
        // Ambil tipe dari query string, e.g., 'hero_block', 'feature_block'
        $type = $request->query('type');

        // Pastikan view partial ada sebelum me-return
        $viewPath = 'admin.landingpage.partials.forms._' . $type . '_form';
        if (!view()->exists($viewPath)) {
            abort(404, 'Form untuk tipe blok ini tidak ditemukan.');
        }

        return view($viewPath, compact('page'));
    }

    /**
     * Menyimpan blok baru ke database.
     */
    public function storeBlock(Request $request)
    {
        try {
            $blockType = $request->input('block_type');
            if (!$blockType) {
                // Langsung lempar exception jika tipe blok tidak ada
                throw new \Exception('Tipe blok tidak valid atau tidak dikirimkan.');
            }

            // 1. VALIDASI & FILTER DATA BERDASARKAN TIPE BLOK
            $validatedData = [];
            $modelClass = "App\\Models\\" . $blockType;


            switch ($blockType) {
                case 'HeroBlock':
                    $validatedData = $request->validate([
                        'headline' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'image_path' => 'nullable|image|max:2048',
                    ]);
                    break;

                case 'FeatureBlock':
                    $validatedData = $request->validate([
                        'tag' => 'nullable|string|max:50',
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'button_text' => 'nullable|string|max:100',
                        'button_url' => 'nullable|url',
                        'image_path' => 'nullable|image|max:2048',
                        'items' => 'nullable|array',
                        'items.*.icon_class' => 'nullable|string',
                        'items.*.text' => 'required|string',
                    ]);
                    break;

                case 'FaqBlock':
                    $validatedData = $request->validate([
                        'title' => 'required|string|max:255',
                        'items' => 'required|array',
                        'items.*.question' => 'required|string|max:255',
                        'items.*.answer' => 'required|string',
                    ]);
                    break;

                case 'DownloadBlock':
                    $validatedData = $request->validate([
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'mockup_image_path' => 'nullable|image|max:2048',
                        'items' => 'nullable|array',
                        'items.*.platform' => 'nullable|string|max:50',
                        'items.*.url' => 'nullable|url',
                        'items.*.icon_path' => 'nullable|image|max:1024',
                    ]);
                    break;
                case 'VideoBlock':
                    $validatedData = $request->validate([
                        'title' => 'nullable|string|max:255',
                        // Validasi untuk file video dengan batasan ukuran 50MB
                        'video_path' => 'required|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
                        'thumbnail_path' => 'nullable|image|max:2048',
                    ]);
                    break;
                // ======================================================

                default:
                    return back()->with('error', 'Aturan validasi untuk blok ini tidak ditemukan.');
            }

            // ======================================================
            // 2. LOGIKA UPLOAD FILE YANG DIPERBARUI & LEBIH FLEKSIBEL
            // ======================================================
            // Daftar semua kemungkinan nama field file dari semua blok
            $fileFields = ['image_path', 'mockup_image_path', 'thumbnail_path', 'video_path'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $validatedData[$field] = $request->file($field)->store('landingpage-blocks', 'public');
                }
            }
            $itemsData = [];
            if ($request->has('items')) {
                $itemsData = $request->input('items'); // Ambil data teks terlebih dahulu

                // Kasus Khusus: Jika ini adalah DownloadBlock, proses file di dalam items
                if ($blockType === 'DownloadBlock') {
                    foreach ($itemsData as $index => &$item) { // Gunakan '&' untuk referensi
                        if ($request->hasFile("items.{$index}.icon_path")) {
                            $item['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
                        }
                    }
                    unset($item); // Hapus referensi setelah loop selesai
                }
            }
            // Hapus 'items' dari data utama karena akan diproses terpisah
            unset($validatedData['items']);

            // 4. Jalankan Transaksi Database
            $newContentBlock = DB::transaction(function () use ($modelClass, $validatedData, $itemsData, $blockType) {
                $page = Page::find(1);
                $specificBlock = $modelClass::create($validatedData);

                if (!empty($itemsData)) {
                    if (method_exists($specificBlock, 'items')) {
                        $specificBlock->items()->createMany($itemsData);
                    } elseif (method_exists($specificBlock, 'links')) {
                        $specificBlock->links()->createMany($itemsData);
                    }
                }

                return $page->content_blocks()->create([
                    'blockable_id' => $specificBlock->id,
                    'blockable_type' => $modelClass,
                    'position' => $page->content_blocks()->count(),
                ]);
            });

            // =================================================================
            // <<<<<<<<<<<<<<< PERBAIKAN UTAMA DI SINI (Eager Loading) >>>>>>>>>>>
            // =================================================================
            // Setelah transaksi, kita perlu me-load kembali semua relasi yang dibutuhkan
            // agar view bisa me-render data dengan lengkap tanpa error.
            $newContentBlock->load('blockable'); // 1. Load relasi polimorfik (misal: ke FeatureBlock)

            if ($newContentBlock->blockable) {
                // 2. Cek jika blockable punya relasi 'items' atau 'links', lalu load juga relasi tersebut
                if (method_exists($newContentBlock->blockable, 'items')) {
                    $newContentBlock->blockable->load('items');
                }
                if (method_exists($newContentBlock->blockable, 'links')) {
                    $newContentBlock->blockable->load('links');
                }
            }
            // =================================================================

            // Sekarang $newContentBlock sudah memiliki semua data yang dibutuhkan oleh view
            // Kode Baru (disesuaikan dengan path Anda)
            $html = view('landing-page.partials._block_card', ['block' => $newContentBlock])->render();
            // Pastikan ini adalah satu-satunya 'return' di path sukses
            return response()->json([
                'status' => 'success',
                'message' => 'Blok baru berhasil ditambahkan!',
                'newBlockHtml' => $html
            ]);
        } catch (\Exception $e) {
            // Pastikan ini adalah satu-satunya 'return' di path error
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan blok.',
                'error_details' => config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Terjadi kesalahan internal.'
            ], 500);
        }
    }

    /**
     * Menampilkan form untuk mengedit blok.
     * (Akan dipanggil oleh modal/popup di frontend)
     */
    // app/Http/Controllers/Admin/LandingBuilderPageController.php

    /**
     * Menampilkan form untuk mengedit blok. (VERSI BARU)
     */
    public function editBlock(ContentBlock $content_block)
    {
        // 1. Load relasi polimorfik utamanya (misal: ke DownloadBlock)
        $content_block->load('blockable');

        // Pastikan relasi blockable ada sebelum melanjutkan
        if ($content_block->blockable) {

            // ================================================================
            // <<<<<<<<<<<<<<<<<<<< PERBAIKAN UTAMA DI SINI >>>>>>>>>>>>>>>>>>>>
            // ================================================================
            // 2. Cek jika blockable punya relasi 'items' atau 'links', lalu load juga relasi tersebut
            if (method_exists($content_block->blockable, 'items')) {
                $content_block->blockable->load('items');
            }
            if (method_exists($content_block->blockable, 'links')) {
                $content_block->blockable->load('links');
            }
            // ================================================================

            // Ambil tipe blok untuk menentukan view form yang akan dirender
            $type = rtrim($content_block->blockable->getTable(), 's');
            $viewPath = 'admin.landingpage.partials.forms._' . $type . '_form';

            if (!view()->exists($viewPath)) {
                abort(404, 'Form untuk tipe blok ini tidak ditemukan.');
            }

            // Kirim data yang sudah LENGKAP ke view
            return view($viewPath, ['block' => $content_block]);
        }

        // Jika karena suatu hal relasi blockable tidak ada, kembalikan 404
        abort(404);
    }

    /**
     * Mengupdate blok yang sudah ada.
     */
    /**
     * Mengupdate blok yang sudah ada. (VERSI LENGKAP)
     */
    public function updateBlock(Request $request, ContentBlock $content_block)
    {
        try {
            $specificBlock = $content_block->blockable;
            $blockType = class_basename($specificBlock);
            $validatedData = [];

            switch ($blockType) {
                case 'HeroBlock':
                    $validatedData = $request->validate([
                        'headline' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'image_path' => 'nullable|image|max:2048',
                    ]);
                    break;

                case 'FeatureBlock':
                    $validatedData = $request->validate([
                        'tag' => 'nullable|string|max:50',
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'button_text' => 'nullable|string|max:100',
                        'button_url' => 'nullable|url',
                        'image_path' => 'nullable|image|max:2048',
                        'items' => 'nullable|array',
                        'items.*.icon_class' => 'nullable|string',
                        'items.*.text' => 'required|string',
                    ]);
                    break;

                case 'FaqBlock':
                    $validatedData = $request->validate([
                        'title' => 'required|string|max:255',
                        'items' => 'required|array',
                        'items.*.question' => 'required|string|max:255',
                        'items.*.answer' => 'required|string',
                    ]);
                    break;

                case 'DownloadBlock':
                    $validatedData = $request->validate([
                        'title' => 'required|string|max:255',
                        'description' => 'nullable|string',
                        'mockup_image_path' => 'nullable|image|max:2048',
                        'items' => 'nullable|array',
                        'items.*.platform' => 'nullable|string|max:50',
                        'items.*.url' => 'required_with:items|url',
                        'items.*.icon_path' => 'nullable|image|max:1024',
                        'items.*.existing_icon_path' => 'nullable|string',
                    ]);
                    break;

                case 'VideoBlock':
                    $validatedData = $request->validate([
                        'title' => 'nullable|string|max:255',
                        'video_path' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200', // Boleh kosong saat update
                        'thumbnail_path' => 'nullable|image|max:2048', // Boleh kosong saat update
                    ]);
                    break;
                // ======================================================

                default:
                    // Jika tipe blok tidak dikenal, kita lempar error
                    throw new \Exception('Aturan validasi untuk blok ini tidak ditemukan.');
            }

            // 2. PENANGANAN FILE UPLOAD (UPDATE)
            $fileFields = ['image_path', 'mockup_image_path', 'thumbnail_path', 'video_path'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    // Hapus file lama jika ada
                    if ($specificBlock->$field) {
                        Storage::disk('public')->delete($specificBlock->$field);
                    }
                    // Simpan file baru dan update path di data
                    $validatedData[$field] = $request->file($field)->store('landingpage-blocks', 'public');
                }
            }

            $itemsData = [];
            if ($request->has('items')) {
                $itemsData = $request->input('items');

                if ($blockType === 'DownloadBlock') {
                    // Hapus file lama yang tidak ada di request baru
                    $existingPaths = $specificBlock->links->pluck('icon_path')->filter();
                    $submittedPaths = collect($itemsData)->pluck('existing_icon_path')->filter();
                    $pathsToDelete = $existingPaths->diff($submittedPaths);
                    Storage::disk('public')->delete($pathsToDelete->all());

                    foreach ($itemsData as $index => &$item) {
                        // Jika ada file baru, upload dan ganti pathnya.
                        if ($request->hasFile("items.{$index}.icon_path")) {
                            // Hapus file lama jika ada sebelum menimpa
                            if (isset($item['existing_icon_path'])) {
                                Storage::disk('public')->delete($item['existing_icon_path']);
                            }
                            $item['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
                        } else {
                            // Jika tidak ada file baru, gunakan path lama
                            $item['icon_path'] = $item['existing_icon_path'] ?? null;
                        }
                        unset($item['existing_icon_path']); // Hapus field bantu
                    }
                    unset($item);
                }
            } else {
                // Jika tidak ada 'items' yang dikirim, hapus semua item lama (misal, semua link download dihapus)
                if (method_exists($specificBlock, 'items')) $specificBlock->items()->delete();
                if (method_exists($specificBlock, 'links')) $specificBlock->links()->delete();
            }
            unset($validatedData['items']);

            DB::transaction(function () use ($specificBlock, $validatedData, $itemsData) {
                $specificBlock->update($validatedData);
                if (!empty($itemsData)) {
                    if (method_exists($specificBlock, 'items')) {
                        $specificBlock->items()->delete();
                        $specificBlock->items()->createMany($itemsData);
                    } elseif (method_exists($specificBlock, 'links')) {
                        $specificBlock->links()->delete();
                        $specificBlock->links()->createMany($itemsData);
                    }
                }
            });

            return response()->json(['status' => 'success', 'message' => 'Blok berhasil diperbarui!']);
        } catch (ValidationException $e) {
            // =================================================================
            // <<<<<<<<<<<<<<< INI BAGIAN PALING PENTING >>>>>>>>>>>>>>>>>>>>>
            // =================================================================
            // Jika terjadi error VALIDASI, tangkap di sini dan kirim sebagai JSON 422
            return response()->json([
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            // Tangkap semua jenis error server lainnya
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server.',
                'error_details' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }

    /**
     * Menghapus sebuah blok konten.
     */
    // Versi Baru (AJAX-friendly)
    // Versi Baru (AJAX-friendly)
    public function destroyBlock(ContentBlock $content_block)
    {
        try {
            DB::transaction(function () use ($content_block) {
                if ($content_block->blockable) {
                    $content_block->blockable->delete();
                }
                $content_block->delete();
            });

            return response()->json([
                'status' => 'success',
                'message' => 'Blok berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus blok. Terjadi kesalahan server.'
            ], 500);
        }
    }

    /**
     * Mengubah urutan blok.
     */
    public function reorder(Request $request)
    {
        $orderedIds = $request->input('order');

        foreach ($orderedIds as $index => $id) {
            ContentBlock::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan berhasil disimpan.']);
    }

    // Tambahkan method ini di dalam class LandingBuilderPageController
    private function processDownloadLinkItems(Request $request, $existingItems = null)
    {
        $processedItems = [];
        if (!$request->has('items')) {
            return [];
        }

        foreach ($request->input('items') as $index => $itemData) {
            $newItemData = [
                'platform' => $itemData['platform'],
                'url' => $itemData['url'],
                'icon_path' => $itemData['existing_icon_path'] ?? null, // Ambil path lama sebagai default
            ];

            // Cek jika ada file baru yang diupload untuk item ini
            if ($request->hasFile("items.{$index}.icon_path")) {
                // Hapus file lama jika ada
                if (isset($newItemData['icon_path'])) {
                    Storage::disk('public')->delete($newItemData['icon_path']);
                }
                // Simpan file baru dan update path
                $newItemData['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
            }

            $processedItems[] = $newItemData;
        }

        // (Opsional) Hapus gambar lama yang itemnya dihapus dari form
        if ($existingItems) {
            $newIconPaths = collect($processedItems)->pluck('icon_path')->filter();
            $oldIconPaths = $existingItems->pluck('icon_path')->filter();
            $deletedIconPaths = $oldIconPaths->diff($newIconPaths);
            Storage::disk('public')->delete($deletedIconPaths->all());
        }

        return $processedItems;
    }
}
