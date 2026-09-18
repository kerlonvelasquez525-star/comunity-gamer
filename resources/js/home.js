/**
 * Slider vertical de la landing (nexus_community.blade.php).
 *
 * Con Livewire los eventos 'livewire:navigated' se disparan en cada navegacion.
 * Si se hiciera addEventListener sin control, cada visita añadiria un listener
 * mas a los botones y un solo clic desplazaria el doble, el triple, etc.
 * Por eso marcamos los botones ya enlazados con dataset.sliderBound.
 */
(() => {
    'use strict';

    const SCROLL_RATIO = 0.9;

    const scrollSlider = (slider, direction) => {
        slider.scrollBy({
            top: direction * slider.clientHeight * SCROLL_RATIO,
            behavior: 'smooth',
        });
    };

    const bindButton = (button, slider, direction) => {
        if (!button || button.dataset.sliderBound === 'true') {
            return;
        }

        button.dataset.sliderBound = 'true';
        button.addEventListener('click', () => scrollSlider(slider, direction));
    };

    const initSlider = () => {
        window.scrollTo({ top: 0, left: 0, behavior: 'instant' });

        const slider = document.getElementById('slider');

        if (!slider) {
            return;
        }

        slider.scrollTo({ top: 0, left: 0, behavior: 'instant' });

        bindButton(document.getElementById('slider-up'), slider, -1);
        bindButton(document.getElementById('slider-down'), slider, 1);
    };

    const bindNewsCarousel = () => {
        document.querySelectorAll('[data-news-carousel]').forEach((carousel) => {
            if (carousel.dataset.bound === 'true') {
                return;
            }

            const slides = [...carousel.querySelectorAll('.carousel-stage [data-carousel-slide]')];
            const dots = [...carousel.querySelectorAll('[data-carousel-dot]')];
            const prevButton = carousel.querySelector('[data-carousel-prev]');
            const nextButton = carousel.querySelector('[data-carousel-next]');

            if (slides.length < 2) {
                if (slides[0]) {
                    slides[0].classList.add('is-active');
                }
                carousel.dataset.bound = 'true';
                return;
            }

            let currentIndex = 0;
            let intervalId;

            const showSlide = (index) => {
                currentIndex = (index + slides.length) % slides.length;
                slides.forEach((slide, slideIndex) => {
                    slide.classList.toggle('is-active', slideIndex === currentIndex);
                });

                dots.forEach((dot, dotIndex) => {
                    const active = dotIndex === currentIndex;
                    dot.classList.toggle('is-active', active);
                    dot.setAttribute('aria-current', active ? 'true' : 'false');
                });
            };

            const restartAutoPlay = () => {
                window.clearInterval(intervalId);
                intervalId = window.setInterval(() => showSlide(currentIndex + 1), 6000);
            };

            prevButton?.addEventListener('click', () => {
                showSlide(currentIndex - 1);
                restartAutoPlay();
            });

            nextButton?.addEventListener('click', () => {
                showSlide(currentIndex + 1);
                restartAutoPlay();
            });

            dots.forEach((dot) => {
                dot.addEventListener('click', () => {
                    showSlide(Number(dot.dataset.carouselDot));
                    restartAutoPlay();
                });
            });

            carousel.addEventListener('mouseenter', () => window.clearInterval(intervalId));
            carousel.addEventListener('mouseleave', restartAutoPlay);
            carousel.dataset.bound = 'true';
            showSlide(0);
            restartAutoPlay();
        });
    };

    const bindPublicComments = () => {
        document.querySelectorAll('.official-comment-form').forEach((form) => {
            if (form.dataset.bound === 'true') {
                return;
            }

            form.dataset.bound = 'true';
            form.addEventListener('submit', async (event) => {
                if (form.dataset.authenticated !== 'true') {
                    return;
                }

                event.preventDefault();

                const input = form.querySelector('input[name="contenido"]');
                const list = form.closest('.official-comments')?.querySelector('[data-comments-list]');
                const total = form.closest('.official-comments')?.querySelector('[data-comments-total]');

                if (!input || !list || !total) {
                    form.submit();
                    return;
                }

                const value = input.value.trim();
                if (!value) {
                    input.focus();
                    return;
                }

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrfToken || '',
                    },
                    body: new URLSearchParams(new FormData(form)).toString(),
                });

                if (!response.ok) {
                    form.submit();
                    return;
                }

                const payload = await response.json();
                const comment = payload.comment || {
                    author: 'Usuario',
                    contenido: value,
                };

                const emptyState = list.querySelector('.official-comment-empty');
                if (emptyState) {
                    emptyState.remove();
                }

                const newComment = document.createElement('p');
                newComment.className = 'official-comment';
                newComment.innerHTML = `<strong>${comment.author}:</strong> ${comment.contenido}`;
                list.prepend(newComment);

                const count = Number(total.textContent || '0') + 1;
                total.textContent = String(count);

                form.reset();
            });
        });
    };

    const bindPublicCommentGate = () => {
        const modal = document.querySelector('[data-public-auth-modal]');

        if (!modal || modal.dataset.bound === 'true') {
            return;
        }

        const closeModal = () => {
            modal.hidden = true;
            document.body.classList.remove('public-auth-modal-open');
        };

        const openModal = () => {
            modal.hidden = false;
            document.body.classList.add('public-auth-modal-open');
            modal.querySelector('[data-public-auth-close]')?.focus();
        };

        document.querySelectorAll('[data-public-comment-form]').forEach((form) => {
            if (form.dataset.gateBound === 'true') {
                return;
            }

            form.dataset.gateBound = 'true';
            form.addEventListener('submit', (event) => {
                if (form.dataset.authenticated === 'true') {
                    return;
                }

                event.preventDefault();
                openModal();
            });
        });

        modal.querySelectorAll('[data-public-auth-close]').forEach((element) => {
            element.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', (event) => {
            if (event.key === 'Escape' && !modal.hidden) {
                closeModal();
            }
        });

        modal.dataset.bound = 'true';
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initSlider();
            bindNewsCarousel();
            bindPublicComments();
            bindPublicCommentGate();
        }, { once: true });
    } else {
        initSlider();
        bindNewsCarousel();
        bindPublicComments();
        bindPublicCommentGate();
    }

    document.addEventListener('livewire:navigated', () => {
        initSlider();
        bindNewsCarousel();
        bindPublicComments();
        bindPublicCommentGate();
    });
})();