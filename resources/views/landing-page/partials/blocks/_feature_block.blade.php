{{-- Section #features-1 --}}
<section id="features-{{ $data->id }}" class="stay-productive-section">
    <div class="stay-productive-container">
        <div class="text-content">
            @if ($data->tag)
                <span class="features-tag">{{ $data->tag }}</span>
            @endif
            <h2>{{ $data->title }}</h2>
            <p>{{ $data->description }}</p>
            <ul class="feature-list">
                @foreach ($data->items as $item)
                    <li>
                        <i class="{{ $item->icon_class }}"></i>
                        <span>{{ $item->text }}</span>
                    </li>
                @endforeach
            </ul>
            @if ($data->button_text && $data->button_url)
                <a href="{{ $data->button_url }}" class="learn-more-btn">{{ $data->button_text }}</a>
            @endif
        </div>
        <div class="image-content">
            @if ($data->image_path)
                <img src="{{ asset('storage/' . $data->image_path) }}" alt="{{ $data->title }}"
                    class="productive-image">
            @endif
        </div>
    </div>
</section>
