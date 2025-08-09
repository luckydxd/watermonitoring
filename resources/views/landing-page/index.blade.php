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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('landing-page/css/style-2.0.css') }}" />
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
                <ul class="nav-links">
                    <div class="logo">
                        {{-- Arahkan link logo ke halaman utama --}}
                        <a href="{{ url('/') }}">
                            {{-- Cek apakah logo sudah di-upload di pengaturan --}}
                            @if ($appSettings && $appSettings->logo)
                                {{-- Jika ada, tampilkan logo dari database --}}
                                <img src="{{ asset('storage/' . $appSettings->logo) }}"
                                    alt="Logo {{ $appSettings->name_app }}" class="logo-img" />
                            @else
                                {{-- Jika tidak ada, tampilkan logo default --}}
                                <img src="{{ asset('landing-page/images/logo.png') }}" alt="Logo FloWater"
                                    class="logo-img" />
                            @endif
                        </a>
                    </div>
                    <li><a href="#home">Beranda</a></li>
                    <li><a href="#about">Tentang</a></li>
                    <li><a href="#features-1">Fitur</a></li>
                    <li><a href="#contact" class="track-contact">Kontak</a></li>

                    {{-- Logika pengkondisian tombol Login/Dashboard --}}
                    <li class="track-login">
                        @auth
                            @php
                                $dashboardUrl = '/user/dashboard';
                                if (Auth::user()->hasRole('admin')) {
                                    $dashboardUrl = '/admin/dashboard';
                                } elseif (Auth::user()->hasRole('teknisi')) {
                                    $dashboardUrl = '/teknisi/dashboard';
                                }
                            @endphp
                            <a href="{{ $dashboardUrl }}" class="login-btn">Dashboard</a>
                        @else
                            {{-- Jika pengguna belum login --}}
                            <a href="{{ route('login.user') }}" class="login-btn">Masuk</a>
                        @endauth
                    </li>
                </ul>

                {{-- Dropdown untuk mobile --}}
                <div class="dropdown">
                    <button class="dropbtn" onclick="toggleDropdown()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                            stroke-linejoin="round">
                            <path d="M4 6l16 0" />
                            <path d="M4 12l16 0" />
                            <path d="M4 18l16 0" />
                        </svg>
                    </button>
                    <div class="dropdown-content" id="dropdownContent">
                        <a href="#home">Beranda</a>
                        <a href="#about">Tentang</a>
                        <a href="#features-1">Fitur</a>
                        <a href="#contact" class="track-contact">Kontak</a>

                        @auth
                            @php
                                $dashboardUrl = '/user/dashboard';
                                if (Auth::user()->hasRole('admin')) {
                                    $dashboardUrl = '/admin/dashboard';
                                } elseif (Auth::user()->hasRole('teknisi')) {
                                    $dashboardUrl = '/teknisi/dashboard';
                                }
                            @endphp
                            <a href="{{ $dashboardUrl }}" class="login-btn">Dashboard</a>
                        @else
                            <a href="{{ route('login.user') }}" class="login-btn track-login">Masuk</a>
                        @endauth
                    </div>
                </div>
            </nav>

            <div class="jumbotron">
                <div class="jumbotron-content">
                    <div class="text">
                        <h1>{{ $appSettings->name_app ?? 'Water Monitoring' }}</h1>

                        <p>{{ $appSettings->desc ?? 'Sistem Pemantauan Konsumsi Air Rumah Tangga' }}</p>
                    </div>
                    <div class="image">
                        @if ($appSettings && $appSettings->app_mockup)
                            <img src="{{ asset('storage/' . $appSettings->app_mockup) }}"
                                alt="App Mockup {{ $appSettings->name_app }}" />
                        @else
                            <img src="{{ asset('landing-page/images/phone.png') }}" alt="Phone display" />
                        @endif
                    </div>
                </div>
            </div>
        </section>

    </header>

    <main>
        <div id="content">
            {{-- Perulangan untuk merender setiap blok konten secara dinamis --}}
            @foreach ($page->content_blocks as $block)
                @php
                    // Ambil nama model tanpa namespace, e.g., 'HeroBlock'
                    $blockType = class_basename($block->blockable_type);
                @endphp

                @switch($blockType)
                    @case('HeroBlock')
                        @include('landing-page.partials.blocks._hero_block', ['data' => $block->blockable])
                    @break

                    @case('DownloadBlock')
                        @include('landing-page.partials.blocks._download_block', [
                            'data' => $block->blockable,
                        ])
                    @break

                    @case('FeatureBlock')
                        @include('landing-page.partials.blocks._feature_block', [
                            'data' => $block->blockable,
                        ])
                    @break

                    @case('VideoBlock')
                        @include('landing-page.partials.blocks._video_block', [
                            'data' => $block->blockable,
                        ])
                    @break

                    @case('FaqBlock')
                        @include('landing-page.partials.blocks._faq_block', ['data' => $block->blockable])
                    @break

                    @default
                        {{-- Tampilkan komentar jika tipe blok tidak dikenal --}}
                @endswitch
            @endforeach
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
                    <li class="track-login">Login</li>
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
                    <li>
                        <i class="ti ti-map-pin-filled"></i>
                        <span>&nbsp;{{ $appSettings->address ?? 'Alamat tidak tersedia' }}</span>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="mailto:{{ $appSettings->email ?? '' }}"
                            style="display: flex; align-items: center; text-decoration: none; gap: 10px; color: inherit;">
                            <i class="ti ti-mail-filled"></i>
                            <span
                                class="margin-legt: 0.5rem;">&nbsp;{{ $appSettings->email ?? 'Email tidak tersedia' }}</span>
                        </a>
                    </li>
                </ul>
                <ul>
                    <li>
                        <a href="tel:{{ $appSettings->phone ?? '' }}"
                            style="display: flex; align-items: center; text-decoration: none; gap: 10px; color: inherit;">
                            <i class="ti ti-phone-filled"></i>
                            <span>{{ $appSettings->phone ?? 'Telepon tidak tersedia' }}</span>
                        </a>
                    </li>
                </ul>
            </section>

            <section class="social-links">
                <h3>Sosial Media</h3>
                <ul>
                    @if ($appSettings->whatsapp)
                        <li>
                            <a href="https://wa.me/{{ $appSettings->whatsapp }}" target="_blank"
                                style="display: flex; align-items: center; text-decoration: none; gap: 10px; color: inherit;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor"
                                    class="icon icon-tabler icons-tabler-filled icon-tabler-brand-whatsapp">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M18.497 4.409a10 10 0 0 1 -10.36 16.828l-.223 -.098l-4.759 .849l-.11 .011a1 1 0 0 1 -.11 0l-.102 -.013l-.108 -.024l-.105 -.037l-.099 -.047l-.093 -.058l-.014 -.011l-.012 -.007l-.086 -.073l-.077 -.08l-.067 -.088l-.056 -.094l-.034 -.07l-.04 -.108l-.028 -.128l-.012 -.102a1 1 0 0 1 0 -.125l.012 -.1l.024 -.11l.045 -.122l1.433 -3.304l-.009 -.014a10 10 0 0 1 1.549 -12.454l.215 -.203a10 10 0 0 1 13.226 -.217m-8.997 3.09a1.5 1.5 0 0 0 -1.5 1.5v1a6 6 0 0 0 6 6h1a1.5 1.5 0 0 0 0 -3h-1l-.144 .007a1.5 1.5 0 0 0 -1.128 .697l-.042 .074l-.022 -.007a4.01 4.01 0 0 1 -2.435 -2.435l-.008 -.023l.075 -.041a1.5 1.5 0 0 0 .704 -1.272v-1a1.5 1.5 0 0 0 -1.5 -1.5" />
                                </svg>
                                <span>Whatsapp</span>
                            </a>
                        </li>
                    @endif

                    @if ($appSettings->instagram)
                        <li>
                            <a href="https://instagram.com/{{ $appSettings->instagram }}" target="_blank"
                                style="display: flex; align-items: center; text-decoration: none; gap: 10px; color: inherit;">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="currentColor"
                                    class="icon icon-tabler icons-tabler-filled icon-tabler-brand-instagram">
                                    <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                                    <path
                                        d="M16 3a5 5 0 0 1 5 5v8a5 5 0 0 1 -5 5h-8a5 5 0 0 1 -5 -5v-8a5 5 0 0 1 5 -5zm-4 5a4 4 0 0 0 -3.995 3.8l-.005 .2a4 4 0 1 0 4 -4m4.5 -1.5a1 1 0 0 0 -.993 .883l-.007 .127a1 1 0 0 0 1.993 .117l.007 -.127a1 1 0 0 0 -1 -1" />
                                </svg>
                                <span>Instagram</span>
                            </a>
                        </li>
                    @endif

                    @if ($appSettings->youtube)
                        <li>
                            <a href="{{ $appSettings->youtube }}" target="_blank"
                                style="display: flex; align-items: center; text-decoration: none; gap: 10px; color: inherit;">
                                <i class="ti ti-brand-youtube-filled"></i>
                                <span>Youtube</span>
                            </a>
                        </li>
                    @endif
                </ul>
            </section>

        </div>
    </footer>

    {{-- JS --}}
    <script src="{{ asset('landing-page/js/script.js') }}"></script>

    <script>
        function trackActivity(type) {
            fetch(`/track-activity/${type}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                credentials: 'same-origin'
            }).catch(error => console.error('Error:', error));
        }

        document.addEventListener('DOMContentLoaded', function() {
            trackActivity('visit');

            document.body.addEventListener('click', function(e) {
                if (e.target.closest('.track-login') || e.target.closest('a[href*="login"]')) {
                    trackActivity('login');
                }

                if (e.target.closest('.track-contact') || e.target.closest('a[href="#contact"]')) {
                    trackActivity('contact');
                }

                if (e.target.closest('.btn-download') || e.target.closest('a[href*="download"]')) {
                    trackActivity('download');
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const faqItems = document.querySelectorAll('.faq-item');

            faqItems.forEach(item => {
                const questionButton = item.querySelector('.faq-question');

                questionButton.addEventListener('click', () => {
                    // Cek apakah item ini sudah aktif
                    const isActive = item.classList.contains('active');

                    // Optional: Tutup semua item lain jika sudah terbuka
                    // faqItems.forEach(otherItem => {
                    //     otherItem.classList.remove('active');
                    // });

                    // Buka atau tutup item yang diklik
                    if (!isActive) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                });
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Cari semua video container di halaman
            const videoContainers = document.querySelectorAll('.video-container');

            videoContainers.forEach(container => {
                // Temukan tombol dan elemen video di dalam setiap container
                const playButton = container.querySelector('.play-button');
                const videoPlayer = container.querySelector('.video-player');

                if (playButton && videoPlayer) {
                    playButton.addEventListener('click', () => {
                        // Tambahkan class 'video-is-playing' ke container
                        container.classList.add('video-is-playing');
                        // Putar video
                        videoPlayer.play();
                    });
                }
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Cari semua elemen kartu download di halaman
            const downloadCards = document.querySelectorAll('.download-card');

            // Lakukan ini untuk setiap kartu
            downloadCards.forEach(card => {
                // Temukan wadah lottie di dalam kartu tersebut
                const iconContainer = card.querySelector('.lottie-download-icon');

                // Jika kartu tidak punya wadah lottie (seperti kartu QR), lewati saja
                if (!iconContainer) return;

                // Muat animasi ke dalam wadah
                const animation = lottie.loadAnimation({
                    container: iconContainer, // Wadah tempat animasi muncul
                    renderer: 'svg',
                    loop: false, // Jangan diulang-ulang
                    autoplay: false, // Jangan langsung berputar
                    path: '{{ asset('icons/download.json') }}' // Path ke file animasi Anda
                });

                // Saat kursor masuk ke area kartu, putar animasi
                card.addEventListener('click', (event) => {
                    event.preventDefault(); // Mencegah link berpindah halaman
                    animation.goToAndPlay(0); // Putar animasi saat diklik
                });
            });
        });
    </script>

    <script>
        function toggleDropdown() {
            const dropdown = document.getElementById('dropdownContent');
            dropdown.classList.toggle('show');
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropbtn') && !event.target.closest('.dropbtn')) {
                const dropdown = document.getElementById('dropdownContent');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        }

        // Close dropdown when clicking on a link
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownLinks = document.querySelectorAll('.dropdown-content a');
            dropdownLinks.forEach(link => {
                link.addEventListener('click', function() {
                    const dropdown = document.getElementById('dropdownContent');
                    if (dropdown) {
                        dropdown.classList.remove('show');
                    }
                });
            });
        });

        // Optional: Close dropdown when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdown = document.getElementById('dropdownContent');
                if (dropdown && dropdown.classList.contains('show')) {
                    dropdown.classList.remove('show');
                }
            }
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>


</body>

</html>
