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

    const refreshOfficialComments = async () => {
      try {
        const response = await fetch('/noticias-oficiales/refresh', {
          headers: { Accept: 'application/json' },
        });

        if (!response.ok) {
          return;
        }

        const data = await response.json();

        data.forEach((item) => {
          const total = document.querySelector(`[data-comments-total="${item.id}"]`);
          const list = document.querySelector(`[data-comments-list="${item.id}"]`);

          if (total) {
            total.textContent = item.comments_count;
          }

          if (list) {
            if (!item.comments.length) {
              list.innerHTML = '<p class="official-comment-empty">Sé el primero en comentar.</p>';
              return;
            }

            list.innerHTML = item.comments.map((comment) => `
              <p class="official-comment"><strong>${comment.author}:</strong> ${comment.content}</p>
            `).join('');
          }
        });
      } catch (error) {
        console.warn('No se pudieron refrescar los comentarios de noticias.', error);
      }
    };

    document.addEventListener('DOMContentLoaded', resetWelcomePosition);
    document.addEventListener('livewire:navigated', resetWelcomePosition);
    document.addEventListener('DOMContentLoaded', () => {
      refreshOfficialComments();
      window.setInterval(refreshOfficialComments, 15000);
    });