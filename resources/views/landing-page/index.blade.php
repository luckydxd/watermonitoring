<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Landingpage</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&display=swap" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=PT+Sans:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('demo2/assets/vendor/fonts/tabler-icons.css') }}" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('landing-page/css/style-v1.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <header>
        <section id="home">
            <div class="header-bg"> <img src="{{ asset('landing-page/images/downside-1.png') }}" alt="Header Background"
                    class="bg-image" />
                <div class="header-fade"></div>
            </div>
            <nav>
                <div class="logo">
                    <a href="{{ asset('landing-page/images/logo.png') }}">
                        <img src="{{ asset('landing-page/images/logo.png') }}" alt="Logo FloWater" class="logo-img" />
                    </a>

                </div>
                <ul class="nav-links">
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#features-1">Fitur</a></li> {{-- Ubah ID ini --}}
                    <li><a href="#contact" class="track-contact">Kontak</a></li>
                    <li class="track-login"><a href="{{ route('login.user') }}" class="login-btn">Masuk</a></li>
                </ul>
                <div class="dropdown">
                    <button class="dropbtn">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="white" stroke-width="1" stroke-linecap="round"
                            stroke-linejoin="round" class="icon icon-tabler icons-tabler-outline icon-tabler-menu-2">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M4 6l16 0" />
                            <path d="M4 12l16 0" />
                            <path d="M4 18l16 0" />
                        </svg>
                    </button>
                    <div class="dropdown-content">
                        <a href="#home">Beranda</a>
                        <a href="#about">Tentang</a>
                        <a href="#features-1">Fitur</a> {{-- Ubah ID ini --}}
                        <a href="#contact" class="track-contact">Kontak</a>
                        <a href="{{ route('login.user') }}" class="login-btn track-login">Masuk</a>
                    </div>
                </div>
            </nav>

            <div class="jumbotron">
                <div class="jumbotron-content">
                    <div class="text">
                        <h1>Water Monitoring tes cd</h1>
                        <p>Sistem Pemantauan Konsumsi Air Rumah Tangga</p>
                    </div>
                    <div class="image">
                        <img src="{{ asset('landing-page/images/phone.png') }}" alt="Phone display" />
                    </div>
                </div>
            </div>
        </section>

    </header>

    <main>
        <div id="content">
            {{-- About --}}
            <section id="about">
                <article>
                    <h2>Pantau Konsumsi Air di Rumah Anda</h2>
                    <p>
                        Kami adalah startup yang berfokus pada bidang konservasi air yang membantu Anda memantau dan
                        mengelola penggunaan air
                        <br />
                        rumah tangga secara efisien.
                    </p>
                </article>
                <button class="btn-download track-download">Download Aplikasi</button>
            </section>

            {{-- Feature Section 1: Kanan-Kiri (Teks Kiri, Gambar Kanan) --}}
            {{-- <section id="features-1" class="split-section">
                <div class="split-container">
                    <div class="text-content">
                        <h2>24/7 Akses</h2>
                        <p>
                            Sistem kami secara otomatis tersinkronisasi di semua perangkat Anda,
                            sehingga Anda dapat mengakses informasi terpenting kapan saja, di mana saja.
                            Tidak ada WiFi? Tidak masalah—mode offline berarti Anda dapat terus menggunakan sistem
                            pemantauan bahkan saat internet terputus.
                        </p>
                        <a href="#" class="cta-button">Dapatkan Akses Gratis</a>
                    </div>

                    <div class="image-content">
                        <img src="{{ asset('landing-page/images/feature-1.jpg') }}" alt="24/7 Access"
                            class="feature-image">
                    </div>
                </div>
            </section> --}}

            {{-- Feature Section 2: Kiri-Kanan (Gambar Kiri, Teks Kanan) --}}
            {{-- <section id="features-2" class="split-section reverse-split">
                <div class="split-container">
                    <div class="image-content">
                        <img src="{{ asset('landing-page/images/feature-2.jpg') }}" alt="Your Information, Your Way"
                            class="feature-image">
                    </div>

                    <div class="text-content">
                        <h2>Informasi Anda, Cara Anda</h2>
                        <p>
                            Gunakan FloWater untuk menangkap lebih dari sekadar angka konsumsi.
                            Manfaatkan kekuatan internet dengan analisis data mendalam, laporan visual,
                            dan notifikasi cerdas. Tidak peduli apakah itu laporan harian, mingguan, bulanan,
                            atau peringatan anomali, FloWater menjaga informasi Anda tetap aman dan mudah diakses.
                        </p>
                    </div>
                </div>
            </section> --}}
            {{-- Ini adalah fitur lama Anda, jika ingin diganti dengan yang di atas, bisa dihapus --}}
            {{-- <section id="features" class="split-section reverse-split">
                <div class="split-container">
                    <div class="image-content">
                        <img src="{{ asset('landing-page/images/features.png') }}" alt="Fitur Aplikasi"
                            class="feature-image">
                    </div>
                    <div class="text-content">
                        <h2>Fitur Unggulan Kami</h2>
                        <ul class="feature-list">
                            <li>Pemantauan real-time konsumsi air</li>
                            <li>Notifikasi kebocoran air</li>
                            <li>Analisis penggunaan bulanan</li>
                            <li>Kompatibel dengan berbagai perangkat</li>
                        </ul>
                    </div>
                </div>
            </section> --}}
        </div>
    </main>
    <footer>
        <div class="footer-fade"></div>
        <div class="footer-bg"> <img src="{{ asset('landing-page/images/upside.png') }}" alt="Background Footer"
                class="footer-bg-image" /> </div>
        <div class="footer-content">
            <section id="menu" class="list-menu">
                <h3>Menu</h3>
                <ul>
                    <li>Login</li>
                    <li>Beranda</li>
                    <li>Tentang</li>
                    <li>Fitur</li>
                    <li>Kontak</li>
                </ul>
            </section>


            <section id="produk" class="list-menu">
                <h3>Produk</h3>
                <ul>
                    <li><span>Download Aplikasi</span></li>
                </ul>
            </section>

            <section id="contact" class="contact-info">
                <h3>Kontak</h3>
                <ul>
                    <li><i class="ti ti-map-pin-filled"></i> <span>Perumahan Perum Graha Panyindangan No.A8</span></li>
                </ul>
                <ul>
                    <li><i class="ti ti-mail-filled"></i> <span>flowater@polindra.ac.id</span></li>
                </ul>

                <ul>
                    <li><i class="ti ti-phone-filled"></i> <span>0895345990299</span></li>
                </ul>
            </section>

            <section class="social-links">
                <h3>Sosial Media</h3>
                <ul>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="currentColor" class="icon icon-tabler icons-tabler-filled icon-tabler-brand-whatsapp">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M18.497 4.409a10 10 0 0 1 -10.36 16.828l-.223 -.098l-4.759 .849l-.11 .011a1 1 0 0 1 -.11 0l-.102 -.013l-.108 -.024l-.105 -.037l-.099 -.047l-.093 -.058l-.014 -.011l-.012 -.007l-.086 -.073l-.077 -.08l-.067 -.088l-.056 -.094l-.034 -.07l-.04 -.108l-.028 -.128l-.012 -.102a1 1 0 0 1 0 -.125l.012 -.1l.024 -.11l.045 -.122l1.433 -3.304l-.009 -.014a10 10 0 0 1 1.549 -12.454l.215 -.203a10 10 0 0 1 13.226 -.217m-8.997 3.09a1.5 1.5 0 0 0 -1.5 1.5v1a6 6 0 0 0 6 6h1a1.5 1.5 0 0 0 0 -3h-1l-.144 .007a1.5 1.5 0 0 0 -1.128 .697l-.042 .074l-.022 -.007a4.01 4.01 0 0 1 -2.435 -2.435l-.008 -.023l.075 -.041a1.5 1.5 0 0 0 .704 -1.272v-1a1.5 1.5 0 0 0 -1.5 -1.5" />
                        </svg> <span>Whatsapp</span></li>
                    <li><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="currentColor"
                            class="icon icon-tabler icons-tabler-filled icon-tabler-brand-instagram">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path
                                d="M16 3a5 5 0 0 1 5 5v8a5 5 0 0 1 -5 5h-8a5 5 0 0 1 -5 -5v-8a5 5 0 0 1 5 -5zm-4 5a4 4 0 0 0 -3.995 3.8l-.005 .2a4 4 0 1 0 4 -4m4.5 -1.5a1 1 0 0 0 -.993 .883l-.007 .127a1 1 0 0 0 1.993 .117l.007 -.127a1 1 0 0 0 -1 -1" />
                        </svg> <span>Instagram</span></li>
                    <li><i class="ti ti-brand-discord-filled"></i> <span>Discord</span></li>
                    <li><i class="ti ti-brand-youtube-filled"></i> <span>Youtube</span></li>
                </ul>
            </section>
        </div>
    </footer>

    {{-- JS --}}
    <script src="{{ asset('landing-page/js/script.js') }}"></script>

    <script>
        // Fungsi untuk tracking activity
        function trackActivity(type) {
            fetch(`/track-activity/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            }).catch(error => console.error('Error:', error));
        }

        // Event listeners untuk elemen yang ingin di-track
        document.addEventListener('DOMContentLoaded', function() {
            // Track login clicks
            const loginLinks = document.querySelectorAll('a[href="{{ route('login.user') }}"]');
            loginLinks.forEach(link => {
                link.addEventListener('click', () => trackActivity('login'));
            });

            // Track contact clicks (sesuaikan dengan elemen kontak Anda)
            const contactLinks = document.querySelectorAll('a[href="#contact"], .contact-card');
            contactLinks.forEach(link => {
                link.addEventListener('click', () => trackActivity('contact'));
            });

            // Track download clicks (jika ada tombol download)
            const downloadButtons = document.querySelectorAll('.btn-download');
            downloadButtons.forEach(button => {
                button.addEventListener('click', () => trackActivity('download'));
            });
        });
        // Perbaiki penutupan tag script
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>
