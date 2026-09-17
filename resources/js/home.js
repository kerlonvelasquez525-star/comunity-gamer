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

    const bindPublicComments = () => {
        document.querySelectorAll('.official-comment-form').forEach((form) => {
            if (form.dataset.bound === 'true') {
                return;
            }

            form.dataset.bound = 'true';
            form.addEventListener('submit', async (event) => {
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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            initSlider();
            bindPublicComments();
        }, { once: true });
    } else {
        initSlider();
        bindPublicComments();
    }

    document.addEventListener('livewire:navigated', () => {
        initSlider();
        bindPublicComments();
    });
})();