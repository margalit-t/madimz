document.addEventListener( 'click', async function (e) {
    const btn = e.target.closest( '.add-to-compare-list' );
    if( ! btn ) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append( 'action', 'madimz_product_compare_add_product' );
        formData.append( 'nonce', madimz_product_compare_ajax.nonce );
        formData.append( 'product_id', btn.getAttribute( 'data-id' ) );

        const response = await fetch( madimz_product_compare_ajax.ajaxurl, {
            method: 'POST',
            body: formData
        } );
        const json = await response.json();
        if( json.data ) {
            const notices = document.querySelector( '.woocommerce-notices-wrapper' );
            if( notices ) {
                notices.innerHTML = json.data;
            }
        }

        const fragmentRefreshEvent = new Event( 'wc_fragment_refresh' );
        document.body.dispatchEvent( fragmentRefreshEvent );
    } catch ( error ) {
    }
} );

document.addEventListener( 'click', async function (e) {
    const btn = e.target.closest( '.remove-from-compare-list' );
    if( ! btn ) {
        return;
    }

    try {
        const formData = new FormData();
        formData.append( 'action', 'madimz_product_compare_remove_product' );
        formData.append( 'nonce', madimz_product_compare_ajax.nonce );
        formData.append( 'product_id', btn.getAttribute( 'data-id' ) );

        const response = await fetch( madimz_product_compare_ajax.ajaxurl, {
            method: 'POST',
            body: formData
        } );
        const json = await response.json();

        jQuery( document.body ).one( 'wc_fragments_refreshed', async function (e) {
            window.location.reload();
        } );

        const fragmentRefreshEvent = new Event( 'wc_fragment_refresh' );
        document.body.dispatchEvent( fragmentRefreshEvent );
    } catch ( error ) {
    }
} );

jQuery(function($){
    $(document.body).trigger('wc_fragment_refresh');
});