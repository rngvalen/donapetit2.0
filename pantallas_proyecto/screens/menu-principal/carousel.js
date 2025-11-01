const carousel = document.querySelector('.carousel');
const indicators = document.querySelectorAll('.indicator');
let currentSlide = 0;
const totalSlides = 4;

function showSlide(index) {
    carousel.style.transform = `translateX(-${index * 100}%)`;
    indicators.forEach((ind, i) => {
        ind.classList.toggle('active', i === index);
    });
}

function nextSlide() {
    currentSlide = (currentSlide + 1) % totalSlides;
    showSlide(currentSlide);
}

function prevSlide() {
    currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
    showSlide(currentSlide);
}

function goToSlide(index) {
    currentSlide = index;
    showSlide(index);
}


setInterval(nextSlide, 4000);


showSlide(0);
