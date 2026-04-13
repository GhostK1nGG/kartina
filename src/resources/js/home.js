const initHomePage = () => {
    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    const root = document.documentElement;

    window.addEventListener('pointermove', (event) => {
        const x = (event.clientX / window.innerWidth) * 100;
        const y = (event.clientY / window.innerHeight) * 100;
        root.style.setProperty('--mouse-x', `${x}%`);
        root.style.setProperty('--mouse-y', `${y}%`);
    });

    if (document.body.dataset.page !== 'home') {
        return;
    }

    const tiltCards = document.querySelectorAll('.tilt-card');

    if (!prefersReducedMotion) {
        tiltCards.forEach((card) => {
            card.addEventListener('mousemove', (event) => {
                if (card.classList.contains('is-flipped')) {
                    return;
                }

                const rect = card.getBoundingClientRect();
                const px = (event.clientX - rect.left) / rect.width;
                const py = (event.clientY - rect.top) / rect.height;
                const rotateY = (px - 0.5) * 10;
                const rotateX = (0.5 - py) * 8;

                card.style.transform = `perspective(1400px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) translateY(-6px)`;
            });

            card.addEventListener('mouseleave', () => {
                if (!card.classList.contains('is-flipped')) {
                    card.style.transform = '';
                }
            });
        });
    }

    const revealItems = document.querySelectorAll('.reveal');
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-visible');
                revealObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.14 });

    revealItems.forEach((item, index) => {
        item.style.transitionDelay = `${Math.min(index * 70, 320)}ms`;
        revealObserver.observe(item);
    });

    const heroStage = document.getElementById('heroStage');
    const floatingCards = heroStage ? heroStage.querySelectorAll('.floating-card, .glass-note') : [];

    if (heroStage && !prefersReducedMotion) {
        heroStage.addEventListener('mousemove', (event) => {
            const rect = heroStage.getBoundingClientRect();
            const x = (event.clientX - rect.left - rect.width / 2) / rect.width;
            const y = (event.clientY - rect.top - rect.height / 2) / rect.height;

            floatingCards.forEach((card, index) => {
                const factor = (index + 1) * 6;
                card.style.transform += ` translate(${x * factor}px, ${y * factor}px)`;
            });
        });

        heroStage.addEventListener('mouseleave', () => {
            floatingCards.forEach((card) => {
                card.style.transform = '';
            });
        });
    }

    document.querySelectorAll('.flip-toggle').forEach((card) => {
        const toggle = () => {
            card.classList.toggle('is-flipped');
            card.style.transform = '';
        };

        card.addEventListener('click', (event) => {
            const isInteractive = event.target.closest('a, button, input, textarea, select, label');

            if (isInteractive) {
                return;
            }

            toggle();
        });

        card.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggle();
            }
        });
    });

    const reviewsRail = document.querySelector('[data-reviews-rail]');
    const reviewsTrack = document.querySelector('[data-reviews-track]');
    const reviewsPrev = document.querySelector('.reviews-arrow.is-prev');
    const reviewsNext = document.querySelector('.reviews-arrow.is-next');

    if (reviewsRail && reviewsTrack) {
        const reviewSlides = Array.from(reviewsTrack.querySelectorAll('.review-slide'));
        let reviewIndex = 0;
        let reviewsIntervalId = null;

        const getVisibleSlides = () => {
            if (window.innerWidth <= 760) {
                return 1;
            }

            if (window.innerWidth <= 1180) {
                return 2;
            }

            return 3;
        };

        const renderReviews = () => {
            const firstSlide = reviewSlides[0];

            if (!firstSlide) {
                return;
            }

            const slideWidth = firstSlide.getBoundingClientRect().width;
            const gap = 18;
            const offset = (slideWidth + gap) * reviewIndex;
            reviewsTrack.style.transform = `translate3d(${-offset}px, 0, 0)`;
        };

        const moveReviews = (direction) => {
            const visibleSlides = getVisibleSlides();
            const maxIndex = Math.max(reviewSlides.length - visibleSlides, 0);

            if (direction === 'next') {
                reviewIndex = reviewIndex >= maxIndex ? 0 : reviewIndex + 1;
            } else {
                reviewIndex = reviewIndex <= 0 ? maxIndex : reviewIndex - 1;
            }

            renderReviews();
        };

        const startReviewsAutoplay = () => {
            if (reviewSlides.length <= 1 || prefersReducedMotion) {
                return;
            }

            clearInterval(reviewsIntervalId);
            reviewsIntervalId = window.setInterval(() => moveReviews('prev'), 4200);
        };

        reviewsPrev?.addEventListener('click', () => {
            moveReviews('prev');
            startReviewsAutoplay();
        });

        reviewsNext?.addEventListener('click', () => {
            moveReviews('next');
            startReviewsAutoplay();
        });

        reviewsRail.addEventListener('mouseenter', () => {
            clearInterval(reviewsIntervalId);
        });

        reviewsRail.addEventListener('mouseleave', startReviewsAutoplay);

        window.addEventListener('resize', renderReviews);

        renderReviews();
        startReviewsAutoplay();
    }

    const galleryModal = document.getElementById('galleryModal');
    const galleryModalTitle = document.getElementById('galleryModalTitle');
    const galleryModalChip = document.getElementById('galleryModalChip');
    const galleryModalDescription = document.getElementById('galleryModalDescription');
    const galleryModalLink = document.getElementById('galleryModalLink');
    const galleryModalStage = document.getElementById('galleryModalStage');
    const galleryModalThumbs = document.getElementById('galleryModalThumbs');
    const galleryPrev = galleryModal?.querySelector('.gallery-modal-nav.is-prev');
    const galleryNext = galleryModal?.querySelector('.gallery-modal-nav.is-next');
    let galleryImages = [];
    let galleryIndex = 0;

    const renderGalleryModal = () => {
        if (!galleryModalStage || !galleryModalThumbs) {
            return;
        }

        galleryModalStage.innerHTML = '';
        galleryModalThumbs.innerHTML = '';

        if (galleryImages.length === 0) {
            return;
        }

        const activeImage = document.createElement('div');
        activeImage.className = 'gallery-modal-image';
        activeImage.style.backgroundImage = `url('${galleryImages[galleryIndex]}')`;
        galleryModalStage.appendChild(activeImage);

        galleryImages.forEach((imageUrl, index) => {
            const thumb = document.createElement('button');
            thumb.type = 'button';
            thumb.className = `gallery-modal-thumb${index === galleryIndex ? ' is-active' : ''}`;
            thumb.style.backgroundImage = `url('${imageUrl}')`;
            thumb.addEventListener('click', () => {
                galleryIndex = index;
                renderGalleryModal();
            });
            galleryModalThumbs.appendChild(thumb);
        });

        const showNav = galleryImages.length > 1;

        if (galleryPrev) {
            galleryPrev.hidden = !showNav;
        }

        if (galleryNext) {
            galleryNext.hidden = !showNav;
        }
    };

    const openGalleryModal = (payload) => {
        if (!galleryModal || !payload) {
            return;
        }

        galleryImages = Array.isArray(payload.images) ? payload.images : [];
        galleryIndex = 0;

        if (galleryModalTitle) {
            galleryModalTitle.textContent = payload.title || '';
        }

        if (galleryModalChip) {
            galleryModalChip.textContent = payload.chip || '';
            galleryModalChip.hidden = !payload.chip;
        }

        if (galleryModalDescription) {
            galleryModalDescription.textContent = payload.description || '';
        }

        if (galleryModalLink) {
            galleryModalLink.href = payload.detailUrl || '#';
        }

        renderGalleryModal();
        galleryModal.classList.add('is-open');
        galleryModal.setAttribute('aria-hidden', 'false');
        document.body.classList.add('has-overlay');
    };

    const closeGalleryModal = () => {
        if (!galleryModal) {
            return;
        }

        galleryModal.classList.remove('is-open');
        galleryModal.setAttribute('aria-hidden', 'true');
        document.body.classList.remove('has-overlay');
    };

    document.querySelectorAll('.open-gallery-modal').forEach((button) => {
        button.addEventListener('click', (event) => {
            event.preventDefault();
            event.stopPropagation();

            const card = button.closest('.gallery-card');
            const payloadNode = card?.querySelector('.gallery-payload');

            if (!payloadNode) {
                return;
            }

            openGalleryModal(JSON.parse(payloadNode.textContent));
        });
    });

    galleryPrev?.addEventListener('click', () => {
        if (galleryImages.length < 2) {
            return;
        }

        galleryIndex = (galleryIndex - 1 + galleryImages.length) % galleryImages.length;
        renderGalleryModal();
    });

    galleryNext?.addEventListener('click', () => {
        if (galleryImages.length < 2) {
            return;
        }

        galleryIndex = (galleryIndex + 1) % galleryImages.length;
        renderGalleryModal();
    });

    galleryModal?.querySelectorAll('[data-close-gallery-modal]').forEach((element) => {
        element.addEventListener('click', closeGalleryModal);
    });

    document.addEventListener('keydown', (event) => {
        if (galleryModal?.classList.contains('is-open')) {
            if (event.key === 'Escape') {
                closeGalleryModal();
            }

            if (event.key === 'ArrowLeft' && galleryImages.length > 1) {
                galleryIndex = (galleryIndex - 1 + galleryImages.length) % galleryImages.length;
                renderGalleryModal();
            }

            if (event.key === 'ArrowRight' && galleryImages.length > 1) {
                galleryIndex = (galleryIndex + 1) % galleryImages.length;
                renderGalleryModal();
            }
        }
    });

    const contactFlip = document.querySelector('.contact-flip');
    const flipContactBtn = document.getElementById('flipContactBtn');

    if (contactFlip && flipContactBtn) {
        if (contactFlip.querySelector('.contact-back .status-banner, .contact-back .form-error')) {
            contactFlip.classList.add('is-flipped');
        }

        flipContactBtn.addEventListener('click', (event) => {
            event.preventDefault();
            contactFlip.classList.add('is-flipped');
            contactFlip.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });

        contactFlip.addEventListener('click', (event) => {
            const clickedButton = event.target.closest('button');
            const interactive = event.target.closest('input, textarea, select, label');

            if (clickedButton || interactive) {
                return;
            }

            if (!event.target.closest('.contact-front, .contact-back')) {
                return;
            }

            contactFlip.classList.toggle('is-flipped');
        });

        contactFlip.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                if (document.activeElement && document.activeElement.matches('input, textarea, button')) {
                    return;
                }

                event.preventDefault();
                contactFlip.classList.toggle('is-flipped');
            }
        });
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initHomePage, { once: true });
} else {
    initHomePage();
}
