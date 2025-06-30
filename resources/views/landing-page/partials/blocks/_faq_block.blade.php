{{-- Section FAQ --}}
<section class="faq-section">
    <div class="container">
        <div class="faq-container-inner">
            <div class="row">
                <div class="col-lg-4">
                    <div class="faq-title">
                        <h2>{!! nl2br(e($data->title)) !!}</h2>
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="faq-accordion">
                        @foreach ($data->items as $item)
                            <div class="faq-item">
                                <button class="faq-question">
                                    <span>{{ $item->question }}</span>
                                    <span class="faq-icon"></span>
                                </button>
                                <div class="faq-answer">
                                    <p>{{ $item->answer }}</p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
