document.addEventListener('DOMContentLoaded', () => {    
    const desktopSwiper = new Swiper('.desktop-swiper', {
        // Optional parameters
        spaceBetween: 0,
        slidesPerView: 1,
        centeredSlides: true,
        loop: true,
        speed: 1000,
        autoplay: {
            delay: 3000,
        },
        
    });
    
    const mobileSwiper = new Swiper('.mobile-swiper', {
        // Optional parameters
        spaceBetween: 0,
        slidesPerView: 1,
        centeredSlides: true,
        loop: true,
        speed: 1000,
        autoplay: {
            delay: 3000,
        },

    });

    // category carousel swiper
    const categorySwiper = new Swiper('.category-carousel', {
        // Optional parameters
        spaceBetween: 10,
        slidesPerView: 4,
        slidesPerGroup: 1,
        autoHeight: true,
        loop: true,
        pagination: {
            el: '.swiper-pagination',
            type: 'bullets',
            clickable: true,
            dynamicBullets: true,
            dynamicMainBullets: 1,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            1024: {
                slidesPerView: 6.5,
            }
        },
    });

    // favorite products slider
    const favoritePdtsSwiper = new Swiper('.swiper-slide-pdts', {
        // Optional parameters
        spaceBetween: 16,
        slidesPerView: 2,
        slidesPerGroup: 1,
        autoHeight: true,
        loop: true,
        speed: 1000,
        autoplay: {
            delay: 3000,
        },
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        breakpoints: {
            1024: {
                slidesPerView: 5,
                spaceBetween: 24,
            }
        },
    });
    
    // about-us images slider
    const aboutUsSwiper = new Swiper('.gallery-carousel-about', {
        // Optional parameters
        // spaceBetween: 0,
        slidesPerView: 1,
        slidesPerGroup: 1,
        autoHeight: true,
        speed: 1000,
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        rtl: false,
        loop: true,
    });
});

// מאזינים לכמה אירועים; חשוב לשים passive: false בשביל touchstart כדי שנוכל לעשות preventDefault()
['click', 'mousedown', 'touchstart'].forEach(evt => {
  document.addEventListener(evt, function(e) {
    // מצא את האלמנט .color-more גם אם לחצת על ילד שלו (למשל svg)
    const btn = e.target.closest && e.target.closest('.color-more');
    if (!btn) return;

    // עצור את הניווט וההתפשטות
    if (e.preventDefault) e.preventDefault();
    if (e.stopPropagation) e.stopPropagation();
    if (e.stopImmediatePropagation) e.stopImmediatePropagation();

    // בטיחות - מצא את המעטפת והחלף קלאס
    const wrapper = btn.closest('.product-color-variations');
    if (wrapper) {
      wrapper.classList.toggle('show-all');
      // עדכון טקסט הפלוס/מינוס
      btn.classList.toggle("rotate");
    }
  }, { passive: false });
});
