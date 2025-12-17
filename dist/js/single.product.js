// MADIMZ – PRODUCT & CATEGORY SCRIPTS (FINAL)

let currentCell = null;
let activePhotoSwipe = null;


//COLOR VARIATIONS – SHOW MORE
['click', 'mousedown', 'touchstart'].forEach(type => {
  document.addEventListener(type, e => {
    const btn = e.target.closest?.('.color-more');
    if (!btn) return;

    e.preventDefault();
    e.stopPropagation();
    e.stopImmediatePropagation();

    const wrapper = btn.closest('.product-color-variations');
    if (!wrapper) return;

    wrapper.classList.toggle('show-all');
    btn.classList.toggle('rotate');
  }, { passive: false });
});


// VARIATION BOX → WC SELECT SYNC
document.addEventListener('click', e => {
  const box = e.target.closest('.variation-box');
  if (!box) return;

  const { attr, value } = box.dataset;
  if (!attr || !value) return;

  document
    .querySelectorAll(`.variation-box[data-attr="${attr}"]`)
    .forEach(el => el.classList.remove('active'));

  box.classList.add('active');

  const select = document.querySelector(`select[name="attribute_${attr}"]`);
  if (!select) return;

  select.value = value;
  select.dispatchEvent(new Event('change', { bubbles: true }));
});

//  DOM READY INIT
document.addEventListener('DOMContentLoaded', () => {
  initPrintCheckboxes();
  initMatrixModal();
  initQuantityButtons();
  initBackToTop();
  initComplementarySlider();
  initProductGallery();
  initMobileFilters();
});

//  MATRIX – PRINT CHECKBOXES
function initPrintCheckboxes() {
  const container = document.querySelector('.single-product-variation-matrix');
  if (!container) return;

  const checkboxes = container.querySelectorAll('.checkbox');
  if (!checkboxes.length) return;

  checkboxes.forEach(updateCheckboxTarget);
  checkboxes.forEach(cb =>
    cb.addEventListener('change', () => updateCheckboxTarget(cb))
  );
}

function updateCheckboxTarget(cb) {
  const targetClass = cb.dataset.name;
  const targetSelector = cb.dataset.target;
  const isChecked = cb.checked;

  if (targetClass) {
    document.querySelectorAll(`.${targetClass}`).forEach(el => {
      el.style.display = isChecked ? '' : 'none';
    });
  }

  if (!isChecked && targetSelector === '#upload-logo-area') {
    const file = document.getElementById('file');
    const img = document.getElementById('image-file');
    if (file) file.value = '';
    if (img) {
      img.src = '';
      img.style.display = 'none';
      img.removeAttribute('data-attachment_id');
    }
  }
}

//  MATRIX MODAL
function initMatrixModal() {
  const modal = document.getElementById('myModal');
  const tableWrap = document.getElementById('choose-size');
  if (!modal || !tableWrap) return;

  tableWrap.querySelectorAll('.myBtn').forEach(btn =>
    btn.addEventListener('click', openMatrixModal)
  );

  modal.querySelector('.close')?.addEventListener('click', e => {
    e.preventDefault();
    closeModalAndSave();
  });

  window.addEventListener('click', e => {
    if (e.target === modal) closeModalAndSave();
  });
}

function openMatrixModal(e) {
  e.preventDefault();

  const cell = e.currentTarget.closest('td');
  const qtyInput = cell?.querySelector('input[type="number"]');
  const qty = parseInt(qtyInput?.value, 10);

  if (!qty || qty <= 0) {
    alert('Please enter quantity first');
    return;
  }

  currentCell = cell;
  buildModalTable(qty, cell);
  document.getElementById('myModal').style.display = 'block';
}

function buildModalTable(qty, cell) {
  const table = document.getElementById('myTable');
  if (!table) return;

  table.innerHTML = '<thead></thead><tbody></tbody>';
  const thead = table.querySelector('thead');
  const tbody = table.querySelector('tbody');

  thead.innerHTML = '<tr><th>Name</th><th>Number</th><th>#</th></tr>';

  let saved = [];
  try {
    saved = JSON.parse(cell.dataset.namesNumbers || '[]');
  } catch {}

  for (let i = 1; i <= qty; i++) {
    const s = saved[i - 1] || {};
    const row = document.createElement('tr');
    row.innerHTML = `
      <td><input class="input-name" value="${s.name || ''}"></td>
      <td><input class="input-num" value="${s.number || ''}"></td>
      <td>${i}</td>
    `;
    tbody.appendChild(row);
  }
}

function closeModalAndSave() {
  const modal = document.getElementById('myModal');
  const table = document.getElementById('myTable');
  if (!modal || !table || !currentCell) return;

  const data = Array.from(table.querySelectorAll('tbody tr')).map(r => ({
    name: r.querySelector('.input-name')?.value || '',
    number: r.querySelector('.input-num')?.value || ''
  }));

  currentCell.dataset.namesNumbers = JSON.stringify(data);
  modal.style.display = 'none';
}

//  QUANTITY BUTTONS
function initQuantityButtons() {
  const input = document.querySelector('input.qty');
  if (!input) return;

  document.querySelector('.qty-plus')?.addEventListener('click', () => {
    input.value = parseInt(input.value || 0, 10) + 1;
    input.dispatchEvent(new Event('change'));
  });

  document.querySelector('.qty-minus')?.addEventListener('click', () => {
    const val = parseInt(input.value || 0, 10);
    if (val > 1) {
      input.value = val - 1;
      input.dispatchEvent(new Event('change'));
    }
  });
}

//  BACK TO TOP
function initBackToTop() {
  const btn = document.getElementById('back-to-top');
  if (!btn) return;

  btn.addEventListener('click', e => {
    e.preventDefault();
    window.scrollTo({ top: 0, behavior: 'smooth' });
  });
}

// COMPLEMENTARY PRODUCTS SLIDER
function initComplementarySlider() {
  const el = document.querySelector('.complementary-products-swiper');
  if (!el || typeof Swiper === 'undefined') return;

  new Swiper(el, {
    slidesPerView: 1,
    spaceBetween: 4,
    loop: true,
    rtl: true,
    speed: 1000,
    autoplay: {
      delay: 3000,
    },
    navigation: {
      nextEl: '.swiper-button-next',
      prevEl: '.swiper-button-prev'
    },
    breakpoints: {
      1024: {
        slidesPerView: 4,
        spaceBetween: 16,
      }
    },
  });
}

//  PRODUCT GALLERY INIT
function initProductGallery() {
  if (typeof Swiper === 'undefined') return;

  document.querySelectorAll('.product-gallery').forEach(wrapper => {
    const mainEl   = wrapper.querySelector('.swiper-pdt-container');
    const thumbsEl = wrapper.querySelector('.swiper-thumbs');
    if (!mainEl || !thumbsEl) return;

    const thumbsSwiper = new Swiper(thumbsEl, {
      slidesPerView: 5,
      spaceBetween: 5
    });

    const mainSwiper = new Swiper(mainEl, {
      slidesPerView: 1,
      rtl: true,
      navigation: {
        nextEl: wrapper.querySelector('.swiper-main-next'),
        prevEl: wrapper.querySelector('.swiper-main-prev')
      },
      thumbs: { swiper: thumbsSwiper }
    });

    /* Build images array from DOM */
    const images = getGalleryImagesFromDOM(wrapper);

    /* Init PhotoSwipe */
    if (images.length) {
      setupLightbox(wrapper, images, mainSwiper);
    }

    /* Variation image switch */
    initVariationImageSwitch(wrapper, mainSwiper);
  });
}

//  BUILD IMAGES ARRAY FROM DOM
function getGalleryImagesFromDOM(wrapper) {
  return Array.from(
    wrapper.querySelectorAll('.swiper-pdt-container .swiper-slide a')
  ).map(link => ({
    src: link.getAttribute('data-large_image') || link.href,
    w: parseInt(link.getAttribute('data-large_image_width'), 10) || 1200,
    h: parseInt(link.getAttribute('data-large_image_height'), 10) || 1200,
    title: link.querySelector('img')?.getAttribute('alt') || ''
  }));
}

/***
 * PHOTOSWIPE – SYNCED TO ACTIVE SWIPER SLIDE
 * - Prevents double open
 */
function setupLightbox(wrapper, images, swiper) {
  if (
    typeof PhotoSwipe === 'undefined' ||
    typeof PhotoSwipeUI_Default === 'undefined'
  ) return;

  const pswp = document.querySelector('.pswp');
  if (!pswp) return;

  wrapper
    .querySelectorAll('.swiper-pdt-container .swiper-slide a')
    .forEach((link, index) => {
      link.addEventListener('click', e => {
        e.preventDefault();
        if (activePhotoSwipe) return;

        const startIndex = swiper.activeIndex ?? index;

        activePhotoSwipe = new PhotoSwipe(
          pswp,
          PhotoSwipeUI_Default,
          images,
          {
            index: startIndex,
            history: false,
            focus: false,
            preload: [1, 1] // lazy preload next + prev
          }
        );

        /* Sync Swiper on slide change */
        activePhotoSwipe.listen('afterChange', () => {
          const psIndex = activePhotoSwipe.getCurrentIndex();
          swiper.slideTo(psIndex);
        });

        /* Cleanup on close */
        activePhotoSwipe.listen('close', () => {
          activePhotoSwipe = null;
        });

        activePhotoSwipe.init();
      });
    });
}

/***
 * VARIATION IMAGE SWITCH
 * Requires variation element with:
 * data-image-id OR data-image-index
 */
function initVariationImageSwitch(wrapper, swiper) {
  document.addEventListener('click', e => {
    const variation = e.target.closest('.variation-box');
    if (!variation) return;

    const imageIndex = variation.dataset.imageIndex;
    const imageId    = variation.dataset.imageId;

    if (imageIndex !== undefined) {
      swiper.slideTo(parseInt(imageIndex, 10));
    }

    if (imageId) {
      const slides = wrapper.querySelectorAll('.swiper-slide');
      slides.forEach((slide, i) => {
        if (slide.querySelector(`img[data-attachment-id="${imageId}"]`)) {
          swiper.slideTo(i);
        }
      });
    }
  });
}

// CATEGORY – MOBILE FILTERS MODAL
function initMobileFilters() {
  const sidebar = document.querySelector('.shop-sidebar');
  const toggle = document.querySelector('.mobile-filter-toggle');

  if (!sidebar || !toggle) return;

  const originalParent = sidebar.parentNode;
  const originalNextSibling = sidebar.nextElementSibling;

  const modal = document.createElement('div');
  modal.className = 'filters-modal';

  modal.innerHTML = `
    <div class="filters-modal__content">
      <div class="filters-modal__header">
        <h3>סינונים</h3>
        <button class="filters-modal__close" aria-label="Close">×</button>
      </div>
    </div>
  `;
  document.body.appendChild(modal);

  const modalContent = modal.querySelector('.filters-modal__content');

  function updateSidebarLocation() {
    if (isMobile()) {
      if (!modalContent.contains(sidebar)) {
        modalContent.appendChild(sidebar);
      }
    } else {
      if (originalNextSibling) {
        originalParent.insertBefore(sidebar, originalNextSibling);
      } else {
        originalParent.appendChild(sidebar);
      }
      modal.classList.remove('is-open');
    }
  }

  updateSidebarLocation();

  window.addEventListener('resize', updateSidebarLocation);

  toggle.addEventListener('click', () => modal.classList.add('is-open'));

  modal.querySelector('.filters-modal__close')
    .addEventListener('click', () => modal.classList.remove('is-open'));

  modal.addEventListener('click', e => {
    if (e.target === modal) modal.classList.remove('is-open');
  });
}

function isMobile() {
  return window.matchMedia('(max-width: 767px)').matches;
}
