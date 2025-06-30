{{-- Section Video --}}
<section class="video-section-wrapper my-5 py-5">
    <div class="container">
        <div class="video-container">
            @if ($data->thumbnail_path)
                <img src="{{ asset('storage/' . $data->thumbnail_path) }}" alt="Video thumbnail" class="video-thumbnail">
            @endif
            <button type="button" class="play-button" aria-label="Play Video">
                <i class="ti ti-player-play-filled"></i>
                <span>Watch</span>
            </button>
            <video class="video-player" controls preload="metadata">
                <source src="{{ asset('storage/' . $data->video_path) }}" type="video/mp4">
                Browser Anda tidak mendukung tag video.
            </video>
        </div>
    </div>
</section>
