<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>FloWater</title>

    {{-- Google Fonts --}}
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700&display=swap" rel="stylesheet" />

    {{-- Custom CSS --}}
    <link rel="stylesheet" href="{{ asset('landing-page/css/styles.css') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>

<body>
    <header>
        <nav>
            <div class="logo">
                <a href="{{ asset('landing-page/images/logo.png') }}">
                    <img src="{{ asset('landing-page/images/logo.png') }}" alt="Logo FloWater" class="logo-img" />
                </a>

            </div>
            <ul class="nav-links">
                <li><a href="#home">Beranda</a></li>
                <li><a href="#about">Tentang</a></li>
                <li><a href="#features">Fitur</a></li>
                <li><a href="#contact" class="track-contact">Kontak</a></li>
                <li class="track-login" class="track-login"><a href="{{ route('login-user') }}">Login</a></li>
            </ul>
            <div class="dropdown">
                <button class="dropbtn">
                    <img src="{{ asset('landing-page/images/list.svg') }}" width="20" alt="Menu Icon"
                        class="menu-icon" />
                </button>
                <div class="dropdown-content">
                    <a href="#home">Beranda</a>
                    <a href="#about">Tentang</a>
                    <a href="#features">Fitur</a>
                    <a href="#contact" class="track-contact">Kontak</a>
                    <li><a href="{{ route('login-user') }}" class="track-login">Login</a></li>

                    {{-- <a href="#price">Harga</a> --}}
                </div>
            </div>
        </nav>

        <div class="jumbotron">
            <div class="jumbotron-content">
                <div class="text">
                    <h1>Water Monitoring</h1>
                    <p>Sistem Pemantauan Konsumsi Air Rumah Tangga</p>
                </div>
                <div class="image">
                    <img src="{{ asset('landing-page/images/vektor.jpg') }}" alt="Ilustrasi Air" />
                </div>
            </div>
        </div>
    </header>

    <main>
        <div id="content">
            {{-- Home --}}
            <section id="home">
                <article>
                    <h2>Pantau Konsumsi Air di Rumah Anda</h2>
                    <p>
                        FloWater membantu Anda memantau dan mengelola penggunaan air
                        <br />
                        rumah tangga secara efisien.
                    </p>
                </article>
                <button>Pelajari Lebih Lanjut</button>
                <button class="btn-download track-download">Download Panduan</button>
            </section>

            <br />

            {{-- About --}}
            <section id="about">
                <article>
                    <h2>Tentang Kami</h2>
                    <p>
                        Kami adalah startup yang berfokus pada solusi pemantauan konsumsi
                        <br />
                        air untuk rumah tangga.
                    </p>
                </article>
            </section>

            <br />

            {{-- Features --}}
            <section id="features">
                <h2>Fitur Kami</h2>
                <div class="slider">
                    <div class="slide">
                        <img src="{{ asset('landing-page/images/monitor.jpg') }}" width="350px" alt="Fitur 1" />
                        <p>Pantau penggunaan air secara real-time.</p>
                    </div>
                    <div class="slide">
                        <img src="{{ asset('landing-page/images/monthly.jpg') }}" width="350px" alt="Fitur 2" />
                        <p>Dapatkan laporan bulanan.</p>
                    </div>
                    <div class="slide">
                        <img src="{{ asset('landing-page/images/notification.jpg') }}" width="350px" alt="Fitur 3" />
                        <p>Notifikasi dini kebocoran air.</p>
                    </div>
                </div>
            </section>

            <br />

            {{-- Contact --}}
            <section id="contact">
                <article>
                    <h2>Kontak</h2>
                    <p>Kami selalu ada untuk Anda.<br /></p>
                </article>

                <div class="contact-container">
                    <div class="contact-card">
                        <div class="contact-icon">
                            <img src="{{ asset('landing-page/images/map.svg') }}" alt="Alamat Icon" />
                        </div>
                        <h3>Alamat</h3>
                        <p>Perumahan Perum Graha <br />Panyindangan No.A8</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <img src="{{ asset('landing-page/images/email.svg') }}" alt="Email Icon" />
                        </div>
                        <h3>Email</h3>
                        <p>flowater@polindra.ac.id</p>
                    </div>

                    <div class="contact-card">
                        <div class="contact-icon">
                            <img src="{{ asset('landing-page/images/phone.svg') }}" alt="Telepon Icon" />
                        </div>
                        <h3>Telepon</h3>
                        <p>0895345990299</p>
                    </div>
                </div>
            </section>

            <br />

            {{-- Pricing --}}
            {{-- <section id="price">
          <article>
            <h2>Harga</h2>
            <p>Pilihan terbaik untuk Anda.<br /></p>
          </article>

          <section class="pricing">
            <div class="pricing-card">
              <h2>Family</h2>
              <p class="subtitle">Pemakaian Keluarga</p>
              <div class="price-circle">
                <span class="price"><sup>Rp</sup>500K<span>/bln</span></span>
              </div>
              <ul class="features">
                <li>4 Orang Pengguna</li>
                <li>Fitur Pemantauan Dasar</li>
                <li>Notifikasi Kebocoran</li>
                <li class="disabled">Analitik Mendalam</li>
                <li class="disabled">24/7 Support</li>
              </ul>
              <button class="btn-buy">Berlangganan</button>
            </div>

            <div class="pricing-card">
              <h2>Cluster</h2>
              <p class="subtitle">Pemakaian Tinggi</p>
              <div class="price-circle">
                <span class="price"><sup>Rp</sup>1,5jt<span>/bln</span></span>
              </div>
              <ul class="features">
                <li>15 Orang Pengguna</li>
                <li>Fitur Pemantauan Dasar</li>
                <li>Notifikasi Kebocoran</li>
                <li>Analitik Mendalam</li>
                <li>24/7 Support</li>
              </ul>
              <button class="btn-buy">Berlangganan</button>
            </div>
          </section>
        </section>
      </div> --}}

    </main>

    <footer>
        <p>&copy; 2025. All rights reserved.</p>
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
            const loginLinks = document.querySelectorAll('a[href="{{ route('login-user') }}"]');
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
    </script>
</body>

</html>
