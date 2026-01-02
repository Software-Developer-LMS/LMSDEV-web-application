document.addEventListener('DOMContentLoaded', function() {
    // Add fade-in animation to main content
    const main = document.querySelector('main');
    main.style.opacity = '0';
    main.style.transition = 'opacity 0.5s ease-in-out';
    setTimeout(() => {
        main.style.opacity = '1';
    }, 100);

    // Add hover effects to glass cards
    const cards = document.querySelectorAll('.glass');
    cards.forEach(card => {
        card.addEventListener('mouseenter', () => {
            card.style.transform = 'translateY(-5px)';
            card.style.transition = 'transform 0.3s ease';
        });
        card.addEventListener('mouseleave', () => {
            card.style.transform = 'translateY(0)';
        });
    });
});
