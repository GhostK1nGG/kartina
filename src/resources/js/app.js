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

const syncOverlayState = () => {
    const hasOpenOverlay = document.querySelector(
        '.catalog-modal.is-open, .about-contact-modal.is-open, .gallery-modal.is-open, .image-lightbox.is-open',
    );

    document.body.classList.toggle('has-overlay', Boolean(hasOpenOverlay));
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

const initMobileNav = () => {
    const navShell = document.querySelector('[data-nav-shell]');
    const toggle = navShell?.querySelector('[data-nav-toggle]');
    const panel = navShell?.querySelector('[data-nav-panel]');

    if (!navShell || !toggle || !panel) {
        return;
    }

    const mobileBreakpoint = window.matchMedia('(max-width: 820px)');

    const closeNav = () => {
        navShell.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
    };

    const toggleNav = () => {
        const willOpen = !navShell.classList.contains('is-open');

        navShell.classList.toggle('is-open', willOpen);
        toggle.setAttribute('aria-expanded', willOpen ? 'true' : 'false');
    };

    toggle.addEventListener('click', () => {
        if (!mobileBreakpoint.matches) {
            return;
        }

        toggleNav();
    });

    panel.querySelectorAll('a').forEach((link) => {
        link.addEventListener('click', () => {
            if (mobileBreakpoint.matches) {
                closeNav();
            }
        });
    });

    document.addEventListener('click', (event) => {
        if (!mobileBreakpoint.matches) {
            return;
        }

        if (!event.target.closest('[data-nav-shell]')) {
            closeNav();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            closeNav();
        }
    });

    mobileBreakpoint.addEventListener('change', (event) => {
        if (!event.matches) {
            closeNav();
        }
    });
};

const initCatalogModal = (imageLightbox = null) => {
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
        syncOverlayState();
        applyTranslations(modal);
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        syncOverlayState();
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
    stage?.addEventListener('click', () => {
        if (!currentPainting) {
            return;
        }

        imageLightbox?.open(currentPainting.images, currentImageIndex, currentPainting.title ?? '');
    });

    document.addEventListener('keydown', (event) => {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (document.getElementById('imageLightbox')?.classList.contains('is-open')) {
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

const initImageLightbox = () => {
    const modal = document.getElementById('imageLightbox');

    if (!modal) {
        return null;
    }

    const stage = document.getElementById('imageLightboxStage');
    const title = document.getElementById('imageLightboxTitle');
    const counter = document.getElementById('imageLightboxCounter');
    const thumbs = document.getElementById('imageLightboxThumbs');
    const prevButton = document.getElementById('imageLightboxPrev');
    const nextButton = document.getElementById('imageLightboxNext');

    let images = [];
    let activeIndex = 0;
    let currentTitle = '';
    let touchStartX = null;

    const render = () => {
        const activeImage = images[activeIndex] ?? '';
        const showNavigation = images.length > 1;

        if (stage) {
            stage.innerHTML = '';

            if (activeImage !== '') {
                const image = document.createElement('img');
                image.className = 'image-lightbox-image';
                image.src = activeImage;
                image.alt = currentTitle;
                stage.appendChild(image);
            }
        }

        if (title) {
            title.textContent = currentTitle;
        }

        if (counter) {
            counter.textContent = images.length > 0
                ? `${String(activeIndex + 1).padStart(2, '0')} / ${String(images.length).padStart(2, '0')}`
                : '';
        }

        if (prevButton) {
            prevButton.hidden = !showNavigation;
        }

        if (nextButton) {
            nextButton.hidden = !showNavigation;
        }

        if (!thumbs) {
            return;
        }

        thumbs.innerHTML = '';

        images.forEach((imageUrl, index) => {
            const button = document.createElement('button');
            button.type = 'button';
            button.className = `image-lightbox-thumb${index === activeIndex ? ' is-active' : ''}`;
            button.style.backgroundImage = `url('${imageUrl}')`;
            button.addEventListener('click', () => {
                activeIndex = index;
                render();
            });
            thumbs.appendChild(button);
        });
    };

    const move = (direction) => {
        if (images.length < 2) {
            return;
        }

        if (direction === 'next') {
            activeIndex = (activeIndex + 1) % images.length;
        } else {
            activeIndex = (activeIndex - 1 + images.length) % images.length;
        }

        render();
    };

    const open = (nextImages = [], startIndex = 0, nextTitle = '') => {
        images = Array.isArray(nextImages) ? nextImages.filter(Boolean) : [];

        if (images.length === 0) {
            return;
        }

        activeIndex = Math.min(Math.max(startIndex, 0), images.length - 1);
        currentTitle = nextTitle || '';
        render();
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        syncOverlayState();
    };

    const close = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        syncOverlayState();
    };

    prevButton?.addEventListener('click', () => move('prev'));
    nextButton?.addEventListener('click', () => move('next'));

    modal.querySelectorAll('[data-close-image-lightbox]').forEach((element) => {
        element.addEventListener('click', close);
    });

    stage?.addEventListener('touchstart', (event) => {
        touchStartX = event.changedTouches[0]?.clientX ?? null;
    }, { passive: true });

    stage?.addEventListener('touchend', (event) => {
        if (touchStartX === null) {
            return;
        }

        const touchEndX = event.changedTouches[0]?.clientX ?? touchStartX;
        const deltaX = touchEndX - touchStartX;
        touchStartX = null;

        if (Math.abs(deltaX) < 40) {
            return;
        }

        move(deltaX < 0 ? 'next' : 'prev');
    });

    document.addEventListener('keydown', (event) => {
        if (!modal.classList.contains('is-open')) {
            return;
        }

        if (event.target instanceof HTMLElement && event.target.matches('input, textarea, select')) {
            return;
        }

        if (event.key === 'Escape') {
            close();
            return;
        }

        if (event.key === 'ArrowLeft') {
            event.preventDefault();
            move('prev');
        }

        if (event.key === 'ArrowRight') {
            event.preventDefault();
            move('next');
        }
    });

    return { open, close };
};

const initAboutContactsModal = () => {
    const modal = document.getElementById('aboutContactModal');

    if (!modal) {
        return;
    }

    const openModal = () => {
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        syncOverlayState();
    };

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        syncOverlayState();
    };

    document.querySelectorAll('[data-open-about-contacts]').forEach((button) => {
        button.addEventListener('click', openModal);
    });

    modal.querySelectorAll('[data-close-about-contacts]').forEach((element) => {
        element.addEventListener('click', closeModal);
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
};

const initRelatedPaintingCards = () => {
    document.querySelectorAll('[data-related-painting-card]').forEach((card) => {
        const open = () => {
            const targetUrl = card.dataset.relatedUrl;

            if (!targetUrl) {
                return;
            }

            window.location.href = targetUrl;
        };

        card.addEventListener('click', (event) => {
            if (event.target.closest('form, a, button, input, textarea, select')) {
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
};

const initHeroPhoneLayout = () => {
    const heroDevice = document.querySelector('.hero-device');
    const heroPhone = heroDevice?.querySelector('.hero-phone');

    if (!heroDevice || !heroPhone) {
        return;
    }

    let frameId = null;

    const syncHeroPhoneLayout = () => {
        frameId = null;

        const viewportWidth = window.innerWidth || document.documentElement.clientWidth || 0;

        if (viewportWidth > 820) {
            heroDevice.style.removeProperty('--hero-phone-mobile-width');
            return;
        }

        const deviceWidth = Math.max(heroDevice.clientWidth, 0);
        const safeSideSpace = viewportWidth <= 560 ? 30 : 42;
        const sideButtonAllowance = 12;
        const maxWidth = viewportWidth <= 560 ? 316 : 342;
        const minWidth = viewportWidth <= 560 ? 248 : 280;
        const calculatedWidth = deviceWidth - safeSideSpace - sideButtonAllowance;
        const nextWidth = Math.max(minWidth, Math.min(maxWidth, calculatedWidth));

        heroDevice.style.setProperty('--hero-phone-mobile-width', `${Math.round(nextWidth)}px`);
    };

    const requestSyncHeroPhoneLayout = () => {
        if (frameId !== null) {
            cancelAnimationFrame(frameId);
        }

        frameId = window.requestAnimationFrame(syncHeroPhoneLayout);
    };

    requestSyncHeroPhoneLayout();

    window.addEventListener('resize', requestSyncHeroPhoneLayout, { passive: true });
    window.visualViewport?.addEventListener('resize', requestSyncHeroPhoneLayout, { passive: true });

    if (typeof ResizeObserver !== 'undefined') {
        const resizeObserver = new ResizeObserver(() => {
            requestSyncHeroPhoneLayout();
        });

        resizeObserver.observe(heroDevice);
    }
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

    const imageLightbox = initImageLightbox();
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
        stage?.addEventListener('click', (event) => {
            if (event.target.closest('[data-painting-prev], [data-painting-next]')) {
                return;
            }

            const images = thumbs.length > 0
                ? thumbs.map((thumb) => thumb.dataset.imageUrl).filter(Boolean)
                : [stage.dataset.imageUrl].filter(Boolean);

            imageLightbox?.open(images, currentIndex, stage.getAttribute('aria-label') || '');
        });

        document.addEventListener('keydown', (event) => {
            if (!paintingGallery.isConnected || document.body.dataset.page !== 'painting' || document.getElementById('imageLightbox')?.classList.contains('is-open')) {
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
    initMobileNav();
    initCatalogModal(imageLightbox);
    initAboutContactsModal();
    initRelatedPaintingCards();
    initHeroPhoneLayout();
    applyTranslations();
});
