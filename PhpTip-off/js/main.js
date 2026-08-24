document.addEventListener('DOMContentLoaded', () => {
    const grid = document.getElementById('matches-grid');
    const btnNext = document.getElementById('btn-next');
    const btnPrev = document.getElementById('btn-prev');

    btnNext.addEventListener('click', () => {
        grid.scrollBy({ left: 230, behavior: 'smooth' });
    });

    btnPrev.addEventListener('click', () => {
        grid.scrollBy({ left: -230, behavior: 'smooth' });
    });
});