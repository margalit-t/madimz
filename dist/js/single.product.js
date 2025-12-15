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

document.addEventListener('click', function(e) {
    if (e.target.classList.contains('variation-box')) {

        const box = e.target;
        const attr = box.dataset.attr;
        const value = box.dataset.value;

        // ניקוי ACTIVE
        document.querySelectorAll('.variation-box[data-attr="'+attr+'"]').forEach(el => {
            el.classList.remove('active');
        });

        box.classList.add('active');

        // עדכון ה־select הסמוי
        select = document.querySelector('select[name="attribute_' + attr + '"]');
        if (select) {
            select.value = value;

            // חשוב מאוד — להפעיל את המנגנון של WooCommerce
            select.dispatchEvent(new Event('change', { bubbles: true }));
            jQuery(select).trigger('change');
        }
    }
});

// ===============================
// משתנה גלובלי למודאל
// ===============================
let currentCell = null;

// ===== Matrix Helpers =====
function getQuantity(varId) {
    const cell = document.getElementById(varId);
    const input = cell?.querySelector('input[type="number"]');
    return input ? parseInt(input.value, 10) || 0 : 0;
}

function getTheModal(varId) {
    const cell = document.getElementById(varId);
    return cell?.dataset.namesNumbers || null;
}

function getUniqueKey(varId) {
    const cell = document.getElementById(varId);
    return cell?.dataset.uniqueKey || 0;
}

// Init Modal & Checkboxes 
document.addEventListener('DOMContentLoaded', () => {
    initPrintCheckboxes();
    initMatrixModal();
});

// ===============================================
// 1. לוגיקת צ'קבוקסים – מה להדפיס
// ===============================================
function initPrintCheckboxes() {
    const container = document.querySelector('.single-product-variation-matrix');
    if (!container) return;

    const checkboxes = container.querySelectorAll('.checkbox');
    if (!checkboxes.length) return;

    // מצב התחלתי
    checkboxes.forEach(updateCheckboxTarget);

    // שינוי
    checkboxes.forEach(cb => {
        cb.addEventListener('change', () => {
            updateCheckboxTarget(cb);
        });
    });
}

function updateCheckboxTarget(sourceCheckbox) {
    if (!sourceCheckbox) return;

    const targetClass = sourceCheckbox.dataset.name; // לדוגמה "myBtn" או "print-logo"
    const targetSelector = sourceCheckbox.dataset.target;
    const isChecked = sourceCheckbox.checked;

    // טיפול ברמת קלאס (data-name)
    if (targetClass) {
        const targetElements = document.querySelectorAll('.' + targetClass);

        const show = () => targetElements.forEach(el => el.style.display = '');
        const hide = () => targetElements.forEach(el => el.style.display = 'none');

        if (isChecked) {
            show();
        } else {
            // טיפול מיוחד בעיפרון – myBtn
            if (targetClass === 'myBtn') {
                const numCheckbox  = document.querySelector('.checkbox-mum');
                const nameCheckbox = document.querySelector('.checkbox-name');
                const numOn        = !!(numCheckbox && numCheckbox.checked);
                const nameOn       = !!(nameCheckbox && nameCheckbox.checked);

                // אם לפחות אחד דולק – העיפרון נשאר
                if (numOn || nameOn) {
                    show();
                } else {
                    hide();
                }
            } else {
                hide();
            }
        }
    }

    // טיפול ביעד ישיר (data-target, למשל #upload-logo-area)
    if (targetSelector) {
        const targetEl = document.querySelector(targetSelector);
        /*if (targetEl) {
            targetEl.style.display = isChecked ? '' : 'none';
        }*/

        // אם זה הלוגו ונכבה – ננקה את התמונה והקובץ
        if (!isChecked && targetSelector === '#upload-logo-area') {
            const fileInput  = document.getElementById('file');
            const imgPreview = document.getElementById('image-file');
            if (fileInput) fileInput.value = '';
            if (imgPreview) {
                imgPreview.src = '';
                imgPreview.style.display = 'none';
                imgPreview.removeAttribute('data-attachment_id');
            }
        }
    }
}

// =========================================
// 2. מודאל המטריצה
// =========================================
function initMatrixModal() {
    const modal        = document.getElementById('myModal');
    const tableWrapper = document.getElementById('choose-size');

    if (!modal || !tableWrapper) return;

    const buttons = tableWrapper.querySelectorAll('.myBtn');
    buttons.forEach(btn => {
        btn.addEventListener('click', openMatrixModal);
    });

    const closeBtn = modal.querySelector('.close');
    if (closeBtn) {
        closeBtn.addEventListener('click', (event) => {
            event.preventDefault();
            closeModalAndSave();
        });
    }

    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModalAndSave();
        }
    });
}

function openMatrixModal(event) {
    event.preventDefault();

    const btn  = event.currentTarget;
    const cell = btn.closest('td');
    if (!cell) return;

    const qtyInput = cell.querySelector('input[type="number"]');
    if (!qtyInput) return;

    const quantity = parseInt(qtyInput.value, 10);
    if (!quantity || quantity <= 0) {
        alert('אופס, שכחת להקיש כמות');
        return;
    }

    currentCell = cell;

    const modal      = document.getElementById('myModal');
    const modalValue = document.getElementById('modal-value');
    const modalVarId = document.getElementById('modal-var-id');
    const modalColor = document.getElementById('modal-color');
    const modalSize  = document.getElementById('modal-size');

    if (modalValue) modalValue.textContent = quantity.toString();
    if (modalVarId) modalVarId.textContent = cell.id || '';
    if (modalColor) modalColor.textContent = cell.dataset.color || '';
    if (modalSize)  modalSize.textContent  = cell.dataset.size  || '';

    buildModalTable(quantity, cell);
    modal.style.display = 'block';
}

// בניית טבלה במודאל לפי הכמות + צ'קבוקסים
function buildModalTable(quantity, cell) {
    const table = document.getElementById('myTable');
    if (!table) return;

    let thead = table.querySelector('thead');
    let tbody = table.querySelector('tbody');

    if (!thead) {
        thead = document.createElement('thead');
        table.appendChild(thead);
    }
    if (!tbody) {
        tbody = document.createElement('tbody');
        table.appendChild(tbody);
    }

    thead.innerHTML = '';
    tbody.innerHTML = '';

    if (!quantity || quantity <= 0) return;

    // load saved
    let saved = [];
    const namesJson = cell.dataset.namesNumbers;
    if (namesJson) {
        try {
            const parsed = JSON.parse(namesJson);
            if (Array.isArray(parsed)) saved = parsed;
        } catch (e) {}
    }

    const nameCheckbox = document.querySelector('.checkbox-name');
    const numCheckbox  = document.querySelector('.checkbox-mum');
    const showName     = !!(nameCheckbox && nameCheckbox.checked);
    const showNum      = !!(numCheckbox && numCheckbox.checked);

    const headerRow = document.createElement('tr');

    if (showName) {
        const thName = document.createElement('th');
        thName.textContent = 'שם עובד';
        headerRow.appendChild(thName);
    }
    if (showNum) {
        const thNum = document.createElement('th');
        thNum.textContent = 'מספר עובד';
        headerRow.appendChild(thNum);
    }

    const thIndex = document.createElement('th');
    headerRow.appendChild(thIndex);
    thead.appendChild(headerRow);

    for (let i = 1; i <= quantity; i++) {
        const row = document.createElement('tr');
        const savedItem = saved[i - 1] || {};
        const savedName = typeof savedItem.name === 'string'   ? savedItem.name   : '';
        const savedNum  = typeof savedItem.number === 'string' ? savedItem.number : '';

        if (showName) {
            const tdName    = document.createElement('td');
            const nameInput = document.createElement('input');
            nameInput.type  = 'text';
            nameInput.className = 'input-name';
            if (savedName) nameInput.value = savedName;
            tdName.appendChild(nameInput);
            row.appendChild(tdName);
        }

        if (showNum) {
            const tdNum    = document.createElement('td');
            const numInput = document.createElement('input');
            numInput.type  = 'text';
            numInput.className = 'input-num';
            if (savedNum) numInput.value = savedNum;
            tdNum.appendChild(numInput);
            row.appendChild(tdNum);
        }

        const tdIndex = document.createElement('td');
        tdIndex.textContent = i.toString();
        row.appendChild(tdIndex);

        tbody.appendChild(row);
    }
}

// ===== Modal Save =====
function saveModalData() {
    const table = document.getElementById('myTable');
    if (!table || !currentCell) return;

    const tbody = table.querySelector('tbody');
    if (!tbody) return;

    const rows = Array.from(tbody.querySelectorAll('tr'));
    const data = rows.map(row => {
        const nameInput = row.querySelector('.input-name');
        const numInput  = row.querySelector('.input-num');

        return {
            name:   nameInput ? nameInput.value.trim() : '',
            number: numInput  ? numInput.value.trim()  : ''
        };
    });

    currentCell.dataset.namesNumbers = JSON.stringify(data);
}

function closeModalAndSave() {
    const modal = document.getElementById('myModal');
    if (!modal) return;

    saveModalData();
    modal.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => { 
    // החפת כפתורי החיצים של הכמות למספר
    
    // כפתור פלוס
    const plusBtn = document.querySelector('.qty-plus');
    const minusBtn = document.querySelector('.qty-minus');
    const qtyInput = document.querySelector('input.qty');

    if (plusBtn && qtyInput) {
        plusBtn.addEventListener('click', function () {
            let val = parseInt(qtyInput.value);
            let max = parseInt(qtyInput.getAttribute('max'));

            if (!isNaN(val)) {
                if (max && val >= max) return;
                qtyInput.value = val + 1;
                qtyInput.dispatchEvent(new Event('change'));
            }
        });
    }

    // כפתור מינוס
    if (minusBtn && qtyInput) {
        minusBtn.addEventListener('click', function () {
            let val = parseInt(qtyInput.value);
            let min = parseInt(qtyInput.getAttribute('min')) || 1;

            if (!isNaN(val) && val > min) {
                qtyInput.value = val - 1;
                qtyInput.dispatchEvent(new Event('change'));
            }
        });
    }


    // back to top
    console.log('back-to-top script loaded');
    function smoothScrollToTop() {
        let scrollStep = -window.scrollY / 30; 
        let scrollInterval = setInterval(function () {
            if (window.scrollY !== 0) {
                window.scrollBy(0, scrollStep);
            } else {
                clearInterval(scrollInterval);
            }
        }, 15);
    }

    const scrollTopBtn = document.getElementById('back-to-top');
    if (scrollTopBtn) {
        scrollTopBtn.addEventListener('click', function (e) {
            e.preventDefault();
            smoothScrollToTop();
        });
    }

    let complementaryPdtsSwiper;
    
    const complementarySwiper = document.querySelector('.complementary-products-swiper');
    if (complementarySwiper) {
        // complementary products slider
        complementaryPdtsSwiper = new Swiper(complementarySwiper, {
            // Optional parameters
            spaceBetween: 16,
            slidesPerView: 4,
            slidesPerGroup: 1,
            autoHeight: true,
            loop: true,
            speed: 1000,
            rtl: true,
            // autoplay: {
            //     delay: 3000,
            // },
            
            navigation: {
                nextEl: '.swiper-button-next',
                prevEl: '.swiper-button-prev',
            },
        });
    }
    
});

// Product filtering and mobile filtering via popup
document.addEventListener('DOMContentLoaded', function () {

    const sidebar = document.querySelector('.shop-sidebar');
    const toggleBtn = document.querySelector('.mobile-filter-toggle');

    // If there is no sidebar or button – exit
    if (!sidebar || !toggleBtn) return;

    // Create a modal
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

    const content = modal.querySelector('.filters-modal__content');
    const closeBtn = modal.querySelector('.filters-modal__close');

    // Move the sidebar into the modal
    content.appendChild(sidebar);

    // open
    toggleBtn.addEventListener('click', () => {
        modal.classList.add('is-open');
        // document.body.style.overflow = 'hidden';
    });

    // close
    const closeModal = () => {
        modal.classList.remove('is-open');
        // document.body.style.overflow = '';
    };

    closeBtn.addEventListener('click', closeModal);

    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeModal();
    });

});

