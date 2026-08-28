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

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initSlider, { once: true });
    } else {
        initSlider();
    }

    document.addEventListener('livewire:navigated', initSlider);
})();