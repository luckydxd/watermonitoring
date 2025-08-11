<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContentBlock;
use App\Models\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

use App\Models\HeroBlock;
use App\Models\FeatureBlock;
use App\Models\FaqBlock;
use App\Models\VideoBlock;
use App\Models\DownloadBlock;

class LandingBuilderPageController extends Controller
{
    public function editor()
    {
        $page = Page::firstOrCreate(
            ['id' => 1],
            ['title' => 'Landing Page Utama', 'slug' => '/']
        );

        $page->load(['content_blocks' => function ($query) {
            $query->orderBy('position', 'asc');
        }, 'content_blocks.blockable']);

        return view('admin.landingpage.editor', compact('page'));
    }

    public function createBlock(Request $request, Page $page)
    {
        $type = $request->query('type');

        $viewPath = 'admin.landingpage.partials.forms._' . $type . '_form';
        if (!view()->exists($viewPath)) {
            abort(404, 'Form untuk tipe blok ini tidak ditemukan.');
        }

        return view($viewPath, compact('page'));
    }

    public function storeBlock(Request $request)
    {
        try {
            $blockType = $request->input('block_type');
            if (!$blockType) {
                throw new \Exception('Tipe blok tidak valid atau tidak dikirimkan.');
            }

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
                        'video_path' => 'required|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
                        'thumbnail_path' => 'nullable|image|max:2048',
                    ]);
                    break;

                default:
                    return back()->with('error', 'Aturan validasi untuk blok ini tidak ditemukan.');
            }

            $fileFields = ['image_path', 'mockup_image_path', 'thumbnail_path', 'video_path'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    $validatedData[$field] = $request->file($field)->store('landingpage-blocks', 'public');
                }
            }
            $itemsData = [];
            if ($request->has('items')) {
                $itemsData = $request->input('items');

                if ($blockType === 'DownloadBlock') {
                    foreach ($itemsData as $index => &$item) {
                        if ($request->hasFile("items.{$index}.icon_path")) {
                            $item['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
                        }
                    }
                    unset($item);
                }
            }
            unset($validatedData['items']);

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

            $newContentBlock->load('blockable');

            if ($newContentBlock->blockable) {
                if (method_exists($newContentBlock->blockable, 'items')) {
                    $newContentBlock->blockable->load('items');
                }
                if (method_exists($newContentBlock->blockable, 'links')) {
                    $newContentBlock->blockable->load('links');
                }
            }

            $html = view('landing-page.partials._block_card', ['block' => $newContentBlock])->render();
            return response()->json([
                'status' => 'success',
                'message' => 'Blok baru berhasil ditambahkan!',
                'newBlockHtml' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan blok.',
                'error_details' => config('app.debug') ? $e->getMessage() . ' in ' . $e->getFile() . ':' . $e->getLine() : 'Terjadi kesalahan internal.'
            ], 500);
        }
    }


    public function editBlock(ContentBlock $content_block)
    {
        $content_block->load('blockable');

        if ($content_block->blockable) {

            if (method_exists($content_block->blockable, 'items')) {
                $content_block->blockable->load('items');
            }
            if (method_exists($content_block->blockable, 'links')) {
                $content_block->blockable->load('links');
            }

            $type = rtrim($content_block->blockable->getTable(), 's');
            $viewPath = 'admin.landingpage.partials.forms._' . $type . '_form';

            if (!view()->exists($viewPath)) {
                abort(404, 'Form untuk tipe blok ini tidak ditemukan.');
            }

            return view($viewPath, ['block' => $content_block]);
        }

        abort(404);
    }

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
                        'items.*.url' => ' nullable|url',
                        'items.*.icon_path' => 'nullable|image|max:1024',
                        'items.*.existing_icon_path' => 'nullable|string',
                    ]);
                    break;

                case 'VideoBlock':
                    $validatedData = $request->validate([
                        'title' => 'nullable|string|max:255',
                        'video_path' => 'nullable|file|mimetypes:video/mp4,video/webm,video/ogg|max:51200',
                        'thumbnail_path' => 'nullable|image|max:2048',
                    ]);
                    break;

                default:
                    throw new \Exception('Aturan validasi untuk blok ini tidak ditemukan.');
            }

            $fileFields = ['image_path', 'mockup_image_path', 'thumbnail_path', 'video_path'];
            foreach ($fileFields as $field) {
                if ($request->hasFile($field)) {
                    if ($specificBlock->$field) {
                        Storage::disk('public')->delete($specificBlock->$field);
                    }
                    $validatedData[$field] = $request->file($field)->store('landingpage-blocks', 'public');
                }
            }

            $itemsData = [];
            if ($request->has('items')) {
                $itemsData = $request->input('items');

                if ($blockType === 'DownloadBlock') {
                    $existingPaths = $specificBlock->links->pluck('icon_path')->filter();
                    $submittedPaths = collect($itemsData)->pluck('existing_icon_path')->filter();
                    $pathsToDelete = $existingPaths->diff($submittedPaths);
                    Storage::disk('public')->delete($pathsToDelete->all());

                    foreach ($itemsData as $index => &$item) {
                        if ($request->hasFile("items.{$index}.icon_path")) {
                            if (isset($item['existing_icon_path'])) {
                                Storage::disk('public')->delete($item['existing_icon_path']);
                            }
                            $item['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
                        } else {
                            $item['icon_path'] = $item['existing_icon_path'] ?? null;
                        }
                        unset($item['existing_icon_path']);
                    }
                    unset($item);
                }
            } else {
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
            return response()->json([
                'message' => 'Data yang diberikan tidak valid.',
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan pada server.',
                'error_details' => config('app.debug') ? $e->getMessage() : 'Internal Server Error'
            ], 500);
        }
    }

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

    public function reorder(Request $request)
    {
        $orderedIds = $request->input('order');

        foreach ($orderedIds as $index => $id) {
            ContentBlock::where('id', $id)->update(['position' => $index]);
        }

        return response()->json(['status' => 'success', 'message' => 'Urutan berhasil disimpan.']);
    }

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
                'icon_path' => $itemData['existing_icon_path'] ?? null,
            ];

            if ($request->hasFile("items.{$index}.icon_path")) {
                if (isset($newItemData['icon_path'])) {
                    Storage::disk('public')->delete($newItemData['icon_path']);
                }
                $newItemData['icon_path'] = $request->file("items.{$index}.icon_path")->store('landingpage-blocks/icons', 'public');
            }

            $processedItems[] = $newItemData;
        }

        if ($existingItems) {
            $newIconPaths = collect($processedItems)->pluck('icon_path')->filter();
            $oldIconPaths = $existingItems->pluck('icon_path')->filter();
            $deletedIconPaths = $oldIconPaths->diff($newIconPaths);
            Storage::disk('public')->delete($deletedIconPaths->all());
        }

        return $processedItems;
    }
}
