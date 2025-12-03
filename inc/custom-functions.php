<?php

//Disable Gutenberg editor
add_filter('use_block_editor_for_post', '__return_false', 10);

//Disable show admin bar
add_filter('show_admin_bar', '__return_false');

/**
 * Allow SVG uploads.
 */
function add_svg_file_type_to_uploads( $file_types ) {
	$new_filetypes = array();
	$new_filetypes[ 'svg' ] = 'image/svg+xml';
	$file_types = array_merge( $file_types, $new_filetypes );
	return $file_types;
}
add_filter( 'upload_mimes', 'add_svg_file_type_to_uploads' );

/***
 * Embedded svg files by name and class
 */ 
function inline_svg_with_class($filename, $class = '') {
    $transient_key = 'inline_svg_' . md5($filename);
    $svg = get_transient($transient_key);

    if ($svg === false) {
        $path = get_template_directory() . '/dist/images/' . $filename;

        if (!file_exists($path)) {
            return '<!-- SVG file not found: ' . esc_html($filename) . ' -->';
        }

        $svg = file_get_contents($path);

        // Optional: Minify SVG content before storing
        $svg = preg_replace('/\s+/', ' ', $svg); // crude minification

        set_transient($transient_key, $svg, DAY_IN_SECONDS); // cache for 1 day
    }

    // Add class if provided
    if ($class) {
        if (strpos($svg, 'class="') === false) {
            $svg = preg_replace('/<svg/', '<svg class="' . esc_attr($class) . '"', $svg, 1);
        } else {
            $svg = preg_replace('/class="([^"]*)"/', 'class="$1 ' . esc_attr($class) . '"', $svg, 1);
        }
    }

    return $svg;
}

/***
 * BreadCrumb
 */ 
//Change the separator to < with spaces
add_filter( 'woocommerce_breadcrumb_defaults', 'madimz_breadcrumb_separator' );
function madimz_breadcrumb_separator( $defaults ) {
    $defaults['delimiter'] = '<span class="sep"> > </span>';
    return $defaults;
}

/***
 * Contact Page
 */

//Change Page Title Dynamically
// add_filter('the_title', function($title){
//     if ( is_page('contact') && isset($_GET['product_title']) ) {
//         $product_title = sanitize_text_field($_GET['product_title']);
//         return "Customer Service – Interested in $product_title";
//     }
//     return $title;
// });

// Fill Contact form Message Body


// CF7 Dynamic value for product content field
add_filter('wpcf7_posted_data', function($data) {

    if (!empty($_POST['product_id'])) {
        $pid = intval($_POST['product_id']);
        $product = wc_get_product($pid);

        if ($product) {
            $data['your-message'] =
                esc_html_e('מעוניין לקבל פרטים לגבי ', 'madimz') . $product->get_name() . "\n\n" .
                $data['your-message'];
        }
    }

    return $data;
});

