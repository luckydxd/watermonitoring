{{-- Section #about (Hero) --}}
<section id="about">
    <h2>{{ $data->headline }}</h2>
    <p>{{ $data->description }}</p>
    @if ($data->image_path)
        <img width="750" height="400" src="{{ asset('storage/' . $data->image_path) }}" alt="{{ $data->headline }}">
    @endif
</section>
