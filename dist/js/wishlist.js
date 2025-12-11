document.addEventListener( 'click', async function (e) {
    const btn = e.target.closest( '.button-wishlist' );
    if( ! btn ) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append( 'action', btn.classList.contains('remove') ? 'madimz_wishlist_remove_product' : 'madimz_wishlist_add_product' );
        formData.append( 'nonce', madimz_wishlist_ajax.nonce );
        formData.append( 'product_id', btn.getAttribute( 'data-id' ) );

        const response = await fetch( madimz_wishlist_ajax.ajaxurl, {
            method: 'POST',
            body: formData
        } );
        const json = await response.json();
        if( json.data.notice ) {
            const notices = document.querySelector( '.woocommerce-notices-wrapper' );
            if( notices ) {
                notices.innerHTML = json.data.notice;
            }
        }
        if( json.data.button ) {
            btn.outerHTML = json.data.button;
        }

        // Update wishlist counter in header
        if( json.data.count !== undefined ) {
            const counter = document.querySelector('.wishlist-float .counter');
            if ( counter ) {
                counter.textContent = json.data.count;
            }
        }
    } catch ( error ) {
    }
} );

document.addEventListener('click', async function (e) {
    const clearBtn = e.target.closest('.wishlist-clear');
    if (!clearBtn) return;

    e.preventDefault();

    const formData = new FormData();
    formData.append('action', 'madimz_wishlist_clear_list');
    formData.append('nonce', madimz_wishlist_ajax.nonce);

    try {
        const response = await fetch(madimz_wishlist_ajax.ajaxurl, {
            method: 'POST',
            body: formData
        });

        const json = await response.json();

        // Update notices
        if (json.data.notice) {
            const notices = document.querySelector('.woocommerce-notices-wrapper');
            if (notices) {
                notices.innerHTML = json.data.notice;
            }
        }

        // Update header counter
        if (json.data.count !== undefined) {
            const counter = document.querySelector('.wishlist-float .counter');
            if (counter) {
                counter.textContent = json.data.count;
            }
        }

        // Optionally reload wishlist page
        location.reload();

    } catch (error) {
        console.error(error);
    }
});