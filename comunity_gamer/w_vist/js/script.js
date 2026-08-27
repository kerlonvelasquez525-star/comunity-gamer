const slider = document.querySelector('#slider');
const cards = slider ? [...slider.querySelectorAll('.game-card')] : [];
const controls = document.querySelectorAll('.control-btn');

controls.forEach((control) => {
  control.addEventListener('click', () => {
    if (!slider || cards.length === 0) return;

    const direction = control.dataset.direction === 'next' ? 1 : -1;
    const cardWidth = cards[0].getBoundingClientRect().width;
    slider.scrollBy({ left: direction * (cardWidth + 20), behavior: 'smooth' });
  });
});
