import './bootstrap';
import Alpine from 'alpinejs';
import './home';

window.Alpine = Alpine;

Alpine.start();

const CATEGORY_LABELS = {
    ru: {
        'heavenly-light': 'Небесный свет',
        'quiet-landscapes': 'Тихие ландшафты',
        'golden-textures': 'Золотые текстуры',
    },
    en: {
        'heavenly-light': 'Heavenly Light',
        'quiet-landscapes': 'Quiet Landscapes',
        'golden-textures': 'Golden Textures',
    },
};

const storageKeys = {
    currency: 'kartina:currency',
};

const state = {
    locale: 'ru',
    currency: 'rub',
};

const getCategoryLabel = (slug) => {
    if (!slug) {
        return '';
    }

    return CATEGORY_LABELS[state.locale]?.[slug] ?? slug;
};

const getCurrencyLabel = () => {
    if (state.currency === 'usd') {
        return '$ USD';
    }

    return state.locale === 'en' ? '₽ RUB' : '₽ RUB';
};

const formatPrice = (rubValue, usdValue) => {
    const candidates = {
        rub: rubValue !== '' && rubValue !== null && rubValue !== undefined ? Number(rubValue) : null,
        usd: usdValue !== '' && usdValue !== null && usdValue !== undefined ? Number(usdValue) : null,
    };

    const activeCurrency = candidates[state.currency] !== null
        ? state.currency
        : (candidates.rub !== null ? 'rub' : 'usd');
    const activeValue = candidates[activeCurrency];

    if (activeValue === null || Number.isNaN(activeValue)) {
        return '—';
    }

    const locale = state.locale === 'en' ? 'en-US' : 'ru-RU';
    const currencyCode = activeCurrency === 'usd' ? 'USD' : 'RUB';

    return new Intl.NumberFormat(locale, {
        style: 'currency',
        currency: currencyCode,
        maximumFractionDigits: 0,
    }).format(activeValue);
};

const formatCategoryYear = (slug, year) => {
    const parts = [getCategoryLabel(slug), year].filter(Boolean);
    return parts.join(' · ');
};

const formatPhotoCount = (count) => {
    return state.locale === 'en' ? `${count} photos` : `${count} фото`;
};

const applyTranslations = (root = document) => {
    root.querySelectorAll('[data-category-year]').forEach((element) => {
        element.textContent = formatCategoryYear(element.dataset.categorySlug, element.dataset.year);
    });

    root.querySelectorAll('[data-photo-count]').forEach((element) => {
        const count = Number(element.dataset.photoCount || 0);
        element.textContent = formatPhotoCount(count);
    });

    root.querySelectorAll('[data-price]').forEach((element) => {
        element.textContent = formatPrice(element.dataset.priceRub, element.dataset.priceUsd);
    });

    document.documentElement.lang = state.locale;

    document.querySelectorAll('[data-currency-label]').forEach((element) => {
        element.textContent = getCurrencyLabel();
    });
};

const persistPreferences = () => {
    localStorage.setItem(storageKeys.currency, state.currency);
};

const closePreferenceMenus = () => {
    document.querySelectorAll('[data-pref-dropdown].is-open').forEach((dropdown) => {
        dropdown.classList.remove('is-open');
        dropdown.querySelector('[data-pref-trigger]')?.setAttribute('aria-expanded', 'false');
    });
};

const initPreferenceMenus = () => {
    document.querySelectorAll('[data-pref-dropdown]').forEach((dropdown) => {
        const trigger = dropdown.querySelector('[data-pref-trigger]');

        trigger?.addEventListener('click', (event) => {
            event.preventDefault();
            const willOpen = !dropdown.classList.contains('is-open');
            closePreferenceMenus();

            if (willOpen) {
                dropdown.classList.add('is-open');
                trigger.setAttribute('aria-expanded', 'true');
            }
        });
    });

    document.querySelectorAll('[data-set-currency]').forEach((button) => {
        button.addEventListener('click', () => {
            state.currency = button.dataset.setCurrency === 'usd' ? 'usd' : 'rub';
            persistPreferences();
            applyTranslations();
            closePreferenceMenus();
        });
    });

    document.addEventListener('click', (event) => {
        if (!event.target.closest('[data-pref-dropdown]')) {
            closePreferenceMenus();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closePreferenceMenus();
        }
    });
};

const initCatalogModal = () => {
    const dataNode = document.getElementById('catalogPaintingsData');
    const modal = document.getElementById('catalogModal');

    if (!dataNode || !modal) {
        return;
    }

    const payload = JSON.parse(dataNode.textContent);
    const stage = document.getElementById('catalogModalStage');
    const thumbs = document.getElementById('catalogModalThumbs');
    const prevButton = document.getElementById('catalogModalPrev');
    const nextButton = document.getElementById('catalogModalNext');
    const title = document.getElementById('catalogModalTitle');
    const subtitle = document.getElementById('catalogModalSubtitle');
    const description = document.getElementById('catalogModalDescription');
    const size = document.getElementById('catalogModalSize');
    const year = document.getElementById('catalogModalYear');
    const price = document.getElementById('catalogModalPrice');
    const pageLink = document.getElementById('catalogModalPageLink');
    const paintingId = document.getElementById('catalogModalPaintingId');

    let currentPainting = null;
    let currentImageIndex = 0;

    const moveModalGallery = (direction) => {
        if (!currentPainting) {
            return;
        }

        const images = Array.isArray(currentPainting.images) ? currentPainting.images : [];

        if (images.length < 2) {
            return;
        }

        if (direction === 'next') {
            currentImageIndex = (currentImageIndex + 1) % images.length;
        } else {
            currentImageIndex = (currentImageIndex - 1 + images.length) % images.length;
        }

        renderModalImage();
    };

    const renderModalImage = () => {
        if (!currentPainting || !stage || !thumbs) {
            return;
        }

        const images = Array.isArray(currentPainting.images) ? currentPainting.images : [];
        const activeImage = images[currentImageIndex] ?? images[0] ?? '';
        const showNavigation = images.length > 1;

        stage.innerHTML = activeImage !== ''
            ? `<div class="catalog-modal-image" style="background-image:url('${activeImage}')"></div>`
            : '';

        if (prevButton) {
            prevButton.hidden = !showNavigation;
        }

        if (nextButton) {
            nextButton.hidden = !showNavigation;
        }

        thumbs.innerHTML = '';

        images.forEach((imageUrl, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `catalog-modal-thumb${index === currentImageIndex ? ' is-active' : ''}`;
            button.style.backgroundImage = `url('${imageUrl}')`;
            button.addEventListener('click', () => {
                currentImageIndex = index;
                renderModalImage();
            });
            thumbs.appendChild(button);
        });
    };

    const openModal = (id) => {
        currentPainting = payload[id];

        if (!currentPainting) {
            return;
        }

        currentImageIndex = 0;
        title.textContent = currentPainting.title ?? '';
        subtitle.dataset.categorySlug = currentPainting.categorySlug ?? '';
        subtitle.dataset.year = currentPainting.year ?? '';
        subtitle.setAttribute('data-category-year', 'true');
        description.textContent = currentPainting.fullDescription ?? currentPainting.shortDescription ?? '';
        size.textContent = currentPainting.size || '—';
        year.textContent = currentPainting.year || '—';
        price.dataset.priceRub = currentPainting.priceRub ?? '';
        price.dataset.priceUsd = currentPainting.priceUsd ?? '';
        pageLink.href = currentPainting.detailUrl ?? '#';
        paintingId.value = currentPainting.id ?? '';

        renderModalImage();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('has-overlay');
        applyTranslations(modal);
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('has-overlay');
    };

    document.querySelectorAll('[data-catalog-card]').forEach((card) => {
        const open = () => openModal(card.dataset.paintingId);

        card.addEventListener('click', (event) => {
            if (event.target.closest('[data-stop-modal], form, a, button, input, textarea, select')) {
                return;
            }

            open();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                open();
            }
        });
    });

    document.querySelectorAll('[data-open-catalog-modal]').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();
            openModal(button.dataset.paintingId);
        });
    });

    modal.querySelectorAll('[data-close-catalog-modal]').forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    prevButton?.addEventListener('click', () => moveModalGallery('prev'));
    nextButton?.addEventListener('click', () => moveModalGallery('next'));

    document.addEventListener('keydown', (event) => {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.target instanceof HTMLElement && event.target.matches('input, textarea, select')) {
            return;
        }

        if (event.key === 'Escape') {
            closeModal();
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            moveModalGallery('prev');
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            moveModalGallery('next');
        }
    });
};

document.addEventListener('DOMContentLoaded', () => {
    state.locale = document.body.dataset.defaultLocale || 'ru';
    state.currency = localStorage.getItem(storageKeys.currency)
        || document.body.dataset.defaultCurrency
        || 'rub';

    document.querySelectorAll('form[data-submit-once]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            if (form.dataset.isSubmitting === 'true') {
                event.preventDefault();
                return;
            }

            form.dataset.isSubmitting = 'true';

            form.querySelectorAll('button[type="submit"]').forEach((button) => {
                button.disabled = true;

                if (button.dataset.loadingLabel) {
                    button.dataset.originalLabel = button.textContent;
                    button.textContent = button.dataset.loadingLabel;
                }
            });
        });
    });

    const paintingGallery = document.querySelector('[data-painting-gallery]');

    if (paintingGallery) {
        const stage = paintingGallery.querySelector('[data-painting-stage]');
        const thumbs = Array.from(paintingGallery.querySelectorAll('[data-painting-thumb]'));
        const prevButton = paintingGallery.querySelector('[data-painting-prev]');
        const nextButton = paintingGallery.querySelector('[data-painting-next]');
        let currentIndex = thumbs.findIndex((thumb) => thumb.classList.contains('is-active'));

        if (currentIndex < 0) {
            currentIndex = 0;
        }

        const renderPaintingGallery = () => {
            if (!stage || thumbs.length === 0) {
                return;
            }

            const activeThumb = thumbs[currentIndex];

            if (!activeThumb) {
                return;
            }

            stage.style.backgroundImage = activeThumb.style.backgroundImage;

            thumbs.forEach((thumb, index) => {
                const isActive = index === currentIndex;

                thumb.classList.toggle('is-active', isActive);

                if (isActive) {
                    thumb.setAttribute('aria-current', 'true');
                } else {
                    thumb.removeAttribute('aria-current');
                }
            });
        };

        const movePaintingGallery = (direction) => {
            if (thumbs.length < 2) {
                return;
            }

            if (direction === 'next') {
                currentIndex = (currentIndex + 1) % thumbs.length;
            } else {
                currentIndex = (currentIndex - 1 + thumbs.length) % thumbs.length;
            }

            renderPaintingGallery();
        };

        thumbs.forEach((thumb, index) => {
            thumb.addEventListener('click', () => {
                currentIndex = index;
                renderPaintingGallery();
            });
        });

        prevButton?.addEventListener('click', () => movePaintingGallery('prev'));
        nextButton?.addEventListener('click', () => movePaintingGallery('next'));

        document.addEventListener('keydown', (event) => {
            if (!paintingGallery.isConnected || document.body.dataset.page !== 'painting') {
                return;
            }

            if (event.target instanceof HTMLElement && event.target.matches('input, textarea, select')) {
                return;
            }

            if (event.key === 'ArrowLeft') {
                movePaintingGallery('prev');
            }

            if (event.key === 'ArrowRight') {
                movePaintingGallery('next');
            }
        });

        renderPaintingGallery();
    }

    initPreferenceMenus();
    initCatalogModal();
    applyTranslations();
});
