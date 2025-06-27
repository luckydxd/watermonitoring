<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\AboutSetting;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Storage;

class AboutApiController extends Controller
{
    public function index()
    {
        $data = AboutSetting::query()->latest();
        return DataTables::of($data)->make(true);
    }


    public function show($id)
    {
        try {
            $about = AboutSetting::findOrFail($id);

            return response()->json([
                'about' => $about
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal memuat data Tentang kami',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        DB::beginTransaction();

        try {
            $data = $request->except(['_token', '_method', 'image']);
            $data['id'] = (string) Str::uuid();

            // --- BLOK YANG DIPERBARUI ---
            if ($request->hasFile('image')) {
                // Gunakan metode 'store' untuk membuat nama unik dan menyimpan di folder 'about'
                // Ini akan mengembalikan path seperti 'about/namafileunik.jpg'
                $path = $request->file('image')->store('about', 'public');
                $data['image'] = $path;
            }
            // --- AKHIR BLOK YANG DIPERBARUI ---

            $about = AboutSetting::create($data);

            DB::commit();

            return response()->json([
                'message' => 'Tentang kami berhasil ditambahkan!',
                'about' => $about,
                'image_url' => $about->image ? asset('storage/' . $about->image) : null
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'message' => 'Gagal menambahkan Tentang kami',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Terjadi kesalahan server'
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => '|string|max:255',
            'description' => '|string',
            'image' => 'nullable|image|max:2048'
        ]);

        $about = AboutSetting::findOrFail($id);

        if ($request->hasFile('image')) {
            if ($about->image) {
                Storage::delete('public/' . $about->image);
            }
            $path = $request->file('image')->store('about', 'public');
            $validated['image'] = $path;
        }

        $about->update($validated);

        return response()->json([
            'message' => 'Tentang kami berhasil diperbarui',
            'data' => $about
        ]);
    }
    public function destroy($id)
    {
        DB::beginTransaction();

        try {
            $about = AboutSetting::findOrFail($id);
            $about->delete();

            DB::commit();

            return response()->json([
                'message' => 'Tentang kami berhasil dihapus!'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Gagal menghapus Tentang kami',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
