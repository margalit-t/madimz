<?php
    /*
    Template Name: Contact
    */

    get_header();
?>

<!-- //Add return-to-product link -->
<?php if ( isset($_GET['product_id']) ) : 
    $pid = intval($_GET['product_id']);
?>
    <p><a href="<?php echo get_permalink($pid); ?>"><?php esc_html_e( 'חזרה למוצר', 'madimz' ); ?></a></p>
<?php endif; ?>
