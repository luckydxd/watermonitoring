{{-- Section #download --}}
<section id="download" class="download-section my-5 py-5">
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="download-header mb-5">
                    <h2 class="section-title">{{ $data->title }}</h2>
                    <p class="section-paragraph">{{ $data->description }}</p>
                </div>
            </div>
        </div>
        <div class="row align-items-center">
            <div class="col-md-7">
                <div class="download-grid">
                    @if ($data->links->isNotEmpty())
                        @foreach ($data->links as $link)
                            {{-- Tambahkan kelas 'qr' secara dinamis jika platform kosong --}}
                            <a href="{{ $link->url ?? '#' }}"
                                class="download-card btn-download @if (empty($link->platform)) qr @endif"
                                target="_blank" rel="noopener noreferrer">

                                <div class="card-content-default">
                                    @if ($link->icon_path)
                                        {{-- Jika ini adalah QR Code (platform kosong), kita tidak perlu ukuran tetap --}}
                                        @if (empty($link->platform))
                                            <img src="{{ asset('storage/' . $link->icon_path) }}" alt="QR Code Download"
                                                style="width: 100%; height: auto; border-radius: 8px;">
                                        @else
                                            {{-- Jika ini link biasa, gunakan ukuran ikon standar --}}
                                            <img width="48" height="48"
                                                src="{{ asset('storage/' . $link->icon_path) }}"
                                                alt="{{ $link->platform }} icon" style="object-fit: contain;">
                                        @endif
                                    @else
                                        {{-- Gambar default --}}
                                        <img width="48" height="48"
                                            src="https://img.icons8.com/fluency/48/download.png" alt="download icon" />
                                    @endif

                                    @if ($link->platform)
                                        <span>{{ $link->platform }}</span>
                                    @endif
                                </div>

                                @if ($link->platform)
                                    <div class="card-button-hover">
                                        <div class="lottie-download-icon"></div>
                                        <span>Download</span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    @else
                        <p><em>Link download belum tersedia.</em></p>
                    @endif
                </div>
            </div>
            <div class="col-md-5">
                @if ($data->mockup_image_path)
                    <div class="phone-mockup-container">
                        <img src="{{ asset('storage/' . $data->mockup_image_path) }}" alt="App Mockup"
                            class="img-fluid">
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>
