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
    <link rel="stylesheet" href="{{ asset('landing-page/css/style-v2.css') }}" />
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
            {{-- About --}}
            <section id="about">
                <h2>Pantau Konsumsi Air di Rumah Anda</h2>
                <p>
                    Fokus kami di konservasi air, Pantau & kelola penggunaan air rumah tangga Anda lebih efisien.
                </p>
                {{-- <button class="btn-download track-download">Download Aplikasi</button> --}}
                <img width="750" height="400" src="{{ asset('landing-page/images/vektor-2.png') }}">
            </section>

            <section id="download" class="download-section my-5 py-5">
                <div class="container">

                    <div class="row">
                        <div class="col-12">
                            <div class="download-header mb-5">
                                <h2 class="section-title">Download Aplikasi</h2>
                                <p class="section-paragraph">
                                    Siap untuk monitoring konsumsi air dirumah Anda? Download aplikasi kami sekarang
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="download-grid">
                                <a href="#" class="download-card">
                                    <!-- Konten yang terlihat secara default -->
                                    <div class="card-content-default">
                                        <img width="48" height="48"
                                            src="https://img.icons8.com/fluency/48/apple-app-store.png"
                                            alt="apple-app-store" />
                                        <span>iPhone</span>
                                    </div>
                                    <div class="card-button-hover">
                                        <div class="lottie-download-icon"></div>
                                        <span>Download</span>
                                    </div>
                                </a>

                                <a href="#" class="download-card">
                                    <div class="card-content-default">
                                        <img width="100" height="100"
                                            src="https://img.icons8.com/bubbles/100/google-play.png"
                                            alt="google-play" />
                                        <span>Android</span>
                                    </div>
                                    <div class="card-button-hover">
                                        <div class="lottie-download-icon"></div>
                                        <span>Download</span>
                                    </div>
                                </a>

                                <a href="#" class="download-card qr">
                                    <!-- Untuk QR Code, kita tidak perlu efek hover ini, jadi biarkan simpel -->
                                    <img src="{{ asset('landing-page/images/qr-app.png') }}" alt="QR Code">
                                </a>

                                <div class="download-card-placeholder"></div>
                            </div>
                        </div>

                        <div class="col-md-5">
                            <div class="phone-mockup-container">
                                <img src="{{ asset('landing-page/images/phone-side.png') }}"
                                    alt="App Mockup on Phone" class="img-fluid">
                            </div>
                        </div>

                    </div>
                </div>
            </section>



            <section id="features-1" class="stay-productive-section">
                <div class="stay-productive-container">
                    <div class="text-content">
                        <span class="features-tag">FITUR</span>
                        <h2>Stay organized, stay productive</h2>
                        <p>
                            Keep all your tasks in one place and effortlessly manage your daily schedule. Our task
                            manager helps you stay on top of what's important.
                        </p>
                        <ul class="feature-list">
                            <li>
                                <i class="ti ti-folder"></i>
                                <span>Categorize tasks into lists for easy access.</span>
                            </li>
                            <li>
                                <i class="ti ti-calendar-due"></i>
                                <span>Set deadlines and priorities to keep focused.</span>
                            </li>
                            <li>
                                <i class="ti ti-circle-plus"></i>
                                <span>Quickly add, edit, and mark tasks as complete.</span>
                            </li>
                        </ul>
                        <a href="#" class="learn-more-btn">Learn More</a>
                    </div>
                    <div class="image-content">
                        <img src="{{ asset('landing-page/images/phone-side.png') }}" alt="Productive App"
                            class="productive-image">
                    </div>
                </div>
            </section>


            <section class="video-section-wrapper my-5 py-5">
                <div class="container">

                    <div class="video-container">

                        <img src="{{ asset('landing-page/images/upside.png') }}" alt="Video thumbnail"
                            class="video-thumbnail">
                        <button type="button" class="play-button" aria-label="Play Video">
                            <i class="ti ti-player-play-filled"></i>
                            <span>Watch</span>
                        </button>

                        <video class="video-player" controls preload="metadata">
                            <source src="{{ asset('landing-page/videos/video.mkv') }}" type="video/mp4">
                            Browser Anda tidak mendukung tag video.
                        </video>
                    </div>
                </div>
            </section>

            <section class="faq-section">
                <div class="container">
                    <div class="faq-container-inner">
                        <div class="row">
                            <div class="col-lg-4">
                                <div class="faq-title">
                                    <h2>Pertanyaan <br>yang Sering Diajukan</h2>
                                </div>
                            </div>

                            <div class="col-lg-8">
                                <div class="faq-accordion">

                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>What is Lovi™?</span>
                                            <span class="faq-icon"></span>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Lovi stands as a 100% independent project. We prioritize skincare
                                                science, offering recommendations based on product composition rather
                                                than brand hype.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>Is it safe & secure?</span>
                                            <span class="faq-icon"></span>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Absolutely. We do not store any personal data, and all interactions are
                                                securely encrypted. Your privacy and security are our top priorities.
                                            </p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>Are you brand-affiliated?</span>
                                            <span class="faq-icon"></span>
                                        </button>
                                        <div class="faq-answer">
                                            <p>No, we are not affiliated with any brands. Our recommendations are purely
                                                based on scientific evidence and ingredient analysis to ensure unbiased
                                                and trustworthy advice.</p>
                                        </div>
                                    </div>

                                    <div class="faq-item">
                                        <button class="faq-question">
                                            <span>How are you science-backed exactly?</span>
                                            <span class="faq-icon"></span>
                                        </button>
                                        <div class="faq-answer">
                                            <p>Our team consists of researchers and skincare experts who analyze
                                                peer-reviewed studies and clinical trials to evaluate the efficacy of
                                                ingredients and formulations.</p>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
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

                    {{-- Anda bisa menambahkan untuk Discord dengan cara yang sama jika datanya ada --}}
                    {{-- 
        @if ($appSettings->discord)
        <li>
            <a href="{{ $appSettings->discord }}" target="_blank" style="display: flex; align-items: center; text-decoration: none; color: inherit;">
                <i class="ti ti-brand-discord-filled"></i> 
                <span>Discord</span>
            </a>
        </li>
        @endif 
        --}}
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.12.2/lottie.min.js"></script>


</body>

</html>
