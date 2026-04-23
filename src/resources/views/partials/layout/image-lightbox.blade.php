<div class="image-lightbox" id="imageLightbox" aria-hidden="true">
    <div class="image-lightbox-backdrop" data-close-image-lightbox></div>

    <div class="image-lightbox-dialog" role="dialog" aria-modal="true" aria-labelledby="imageLightboxTitle">
        <button class="image-lightbox-close" type="button" aria-label="{{ __('site.catalog.close') }}" data-close-image-lightbox>&times;</button>

        <div class="image-lightbox-head">
            <strong class="image-lightbox-title" id="imageLightboxTitle"></strong>
            <span class="image-lightbox-counter" id="imageLightboxCounter"></span>
        </div>

        <div class="image-lightbox-stage" id="imageLightboxStage"></div>

        <button class="image-lightbox-nav is-prev" type="button" id="imageLightboxPrev" aria-label="{{ __('site.catalog.photo_prev') }}">
            &larr;
        </button>
        <button class="image-lightbox-nav is-next" type="button" id="imageLightboxNext" aria-label="{{ __('site.catalog.photo_next') }}">
            &rarr;
        </button>

        <div class="image-lightbox-thumbs" id="imageLightboxThumbs"></div>
    </div>
</div>
