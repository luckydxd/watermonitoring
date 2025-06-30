<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Models\Page;
use App\Models\ContentBlock;
use App\Models\HeroBlock;
use App\Models\DownloadBlock;
use App\Models\FeatureBlock;
use App\Models\VideoBlock;
use App\Models\FaqBlock;
use Illuminate\Support\Str;

class LandingPageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Nonaktifkan pengecekan foreign key untuk proses truncate
        Schema::disableForeignKeyConstraints();

        // Bersihkan tabel-tabel terkait agar tidak ada data duplikat
        DB::table('feature_block_items')->truncate();
        DB::table('faq_items')->truncate();
        DB::table('download_links')->truncate();
        DB::table('content_blocks')->truncate();
        DB::table('hero_blocks')->truncate();
        DB::table('download_blocks')->truncate();
        DB::table('feature_blocks')->truncate();
        DB::table('video_blocks')->truncate();
        DB::table('faq_blocks')->truncate();
        DB::table('pages')->truncate();

        // Aktifkan kembali foreign key
        Schema::enableForeignKeyConstraints();

        // 1. Buat Halaman Utama (selalu ID 1)
        $page = Page::create([
            'id' => 1,
            'title' => 'Landing Page Utama',
            'slug' => '/',
            'is_published' => true,
        ]);

        $position = 0; // Inisialisasi penghitung posisi blok

        // ==========================================================
        // Blok 1: Hero Section (dari <section id="about">)
        // ==========================================================
        $heroBlock = HeroBlock::create([
            'headline' => 'Pantau Konsumsi Air di Rumah Anda',
            'description' => 'Fokus kami di konservasi air, Pantau & kelola penggunaan air rumah tangga Anda lebih efisien.',
            'image_path' => 'landingpage-blocks/vektor-2.png', // Sesuaikan dengan path file Anda
        ]);
        ContentBlock::create([
            'page_id' => $page->id,
            'position' => $position++,
            'blockable_id' => $heroBlock->id,
            'blockable_type' => HeroBlock::class,
        ]);

        // ==========================================================
        // Blok 2: Download Section (dari <section id="download">)
        // ==========================================================
        $downloadBlock = DownloadBlock::create([
            'title' => 'Download Aplikasi',
            'description' => 'Siap untuk monitoring konsumsi air dirumah Anda? Download aplikasi kami sekarang',
            'mockup_image_path' => 'landingpage-blocks/phone-side.png', // Sesuaikan dengan path file Anda
        ]);
        // Data untuk link download dinamis
        $downloadBlock->links()->createMany([
            ['platform' => 'iPhone', 'url' => '#', 'icon_path' => 'landingpage-blocks/icons/apple-app-store.png'],
            ['platform' => 'Android', 'url' => '#', 'icon_path' => 'landingpage-blocks/icons/google-play.png'],
            ['platform' => null, 'url' => '#', 'icon_path' => 'landingpage-blocks/icons/qr-app.png'], // Untuk QR Code, platform dikosongkan
        ]);
        ContentBlock::create([
            'page_id' => $page->id,
            'position' => $position++,
            'blockable_id' => $downloadBlock->id,
            'blockable_type' => DownloadBlock::class,
        ]);

        // ==========================================================
        // Blok 3: Features Section (dari <section id="features-1">)
        // ==========================================================
        $featureBlock = FeatureBlock::create([
            'tag' => 'FITUR UNGGULAN',
            'title' => 'Kontrol Penuh Konsumsi Air Anda',
            'description' => "Water Monitoring memberikan Anda kemampuan untuk memantau, menganalisis, dan mengelola penggunaan air di rumah Anda secara real-time. Hemat air, hemat biaya.", // Perubahan di sini
            'image_path' => 'landingpage-blocks/feature-full-monitor.png', // Sesuaikan path jika gambarnya berbeda (misal ilustrasi dashboard)
            'button_text' => 'Pelajari Lebih Lanjut',
            'button_url' => '#features', // Arahkan ke section fitur jika ada detail
        ]);
        // Data untuk item-item fitur
        $featureBlock->items()->createMany([
            ['icon_class' => 'ti ti-activity', 'text' => 'Pemantauan konsumsi air real-time 24/7.'],
            ['icon_class' => 'ti ti-alert-triangle', 'text' => 'Notifikasi instan untuk kebocoran atau anomali air.'],
            ['icon_class' => 'ti ti-chart-bar', 'text' => 'Analisis dan laporan penggunaan air harian, mingguan, bulanan.'],
            ['icon_class' => 'ti ti-device-heartbeat', 'text' => 'Deteksi status perangkat IoT (online/offline).'],
        ]);
        ContentBlock::create([
            'page_id' => $page->id,
            'position' => $position++,
            'blockable_id' => $featureBlock->id,
            'blockable_type' => FeatureBlock::class,
        ]);

        // ==========================================================
        // Blok 4: Video Section
        // ==========================================================
        $videoBlock = VideoBlock::create([
            'title' => 'Tonton Cara Kerjanya',
            'video_path' => 'landingpage-blocks/videos/video.mp4',
            'thumbnail_path' => 'landingpage-blocks/thumbnail.png', // Sesuaikan dengan path file Anda, pastikan ini ada
        ]);
        ContentBlock::create([
            'page_id' => $page->id,
            'position' => $position++,
            'blockable_id' => $videoBlock->id,
            'blockable_type' => VideoBlock::class,
        ]);

        // ==========================================================
        // Blok 5: FAQ Section
        // ==========================================================
        $faqBlock = FaqBlock::create([
            'title' => "Pertanyaan Umum \ntentang Water Monitoring", // Perubahan di sini
        ]);
        // Data untuk item-item FAQ
        $faqBlock->items()->createMany([
            ['question' => 'Apa itu Water Monitoring?', 'answer' => 'Water Monitoring adalah sistem pemantauan konsumsi air rumah tangga berbasis IoT yang membantu Anda melacak, mengelola, dan menghemat penggunaan air Anda.'], // Perubahan di sini
            ['question' => 'Bagaimana cara kerja pemantauan real-time?', 'answer' => 'Perangkat sensor Water Monitoring yang terpasang di pipa air Anda mengirimkan data secara terus-menerus ke aplikasi kami, memungkinkan Anda melihat konsumsi air secara instan.'], // Perubahan di sini
            ['question' => 'Apakah Water Monitoring dapat mendeteksi kebocoran?', 'answer' => 'Ya, sistem kami dilengkapi dengan algoritma yang dapat mendeteksi pola konsumsi air yang tidak biasa atau terus-menerus yang dapat mengindikasikan kebocoran, dan akan mengirimkan notifikasi kepada Anda.'], // Perubahan di sini
            ['question' => 'Perangkat apa saja yang kompatibel dengan Water Monitoring?', 'answer' => 'Water Monitoring dirancang untuk kompatibel dengan berbagai jenis sensor aliran, tekanan, level air, dan kekeruhan yang umum digunakan dalam sistem pemantauan air rumah tangga.'], // Perubahan di sini
            ['question' => 'Bagaimana cara saya memasang perangkat Water Monitoring?', 'answer' => 'Pemasangan awal membutuhkan bantuan teknisi atau orang yang memiliki pengetahuan dasar plumbing. Panduan lengkap akan tersedia di aplikasi.'], // Perubahan di sini
            ['question' => 'Apakah data saya aman?', 'answer' => 'Kami sangat menjaga privasi dan keamanan data Anda. Semua data dienkripsi dan disimpan di server yang aman, sesuai standar industri.'],
        ]);
        ContentBlock::create([
            'page_id' => $page->id,
            'position' => $position++,
            'blockable_id' => $faqBlock->id,
            'blockable_type' => FaqBlock::class,
        ]);

        $this->command->info('LandingPageSeeder berhasil dijalankan!');
    }
}
