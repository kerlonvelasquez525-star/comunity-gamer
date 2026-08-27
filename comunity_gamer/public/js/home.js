const resetWelcomePosition = () => {
      window.scrollTo({ top: 0, left: 0, behavior: 'instant' });

      const slider = document.getElementById('slider');
      const btnUp = document.getElementById('slider-up');
      const btnDown = document.getElementById('slider-down');

      if (slider) {
        slider.scrollTo({ top: 0, left: 0, behavior: 'instant' });
      }

      if (slider && btnUp && btnDown) {
        const scrollAmount = () => slider.clientHeight * 0.9;

        btnUp.addEventListener('click', () => {
          slider.scrollBy({ top: -scrollAmount(), behavior: 'smooth' });
        });

        btnDown.addEventListener('click', () => {
          slider.scrollBy({ top: scrollAmount(), behavior: 'smooth' });
        });
      }
    };

    document.addEventListener('DOMContentLoaded', resetWelcomePosition);
    document.addEventListener('livewire:navigated', resetWelcomePosition);