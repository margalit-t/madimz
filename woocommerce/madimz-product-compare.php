<?php
use Automattic\WooCommerce\Enums\ProductType;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

woocommerce_output_all_notices();

if( empty( $data['product_data'] ) ) {
    return;
}

// Output all product names to an array
$names = array_map(function($item) {
    return esc_html($item['name']);
}, $data['product_data']);

$names_text = implode(' , ', $names);

?>
<div class="compare">
    <h2 class="sub-title-compare"><?php echo __( 'השוואה בין המוצרים: ', 'madimz' ) . $names_text;; ?></h2>
    <table class="table-comparison">
        <thead>
            <tr>
                <th scope="row" class="cell label remove"><span class="screen-reader-text"><?php echo __( 'Remove product', 'madimz' ); ?></span></th>
                <?php foreach( $data['product_data'] as $product_data ): ?>
                    <td class="cell remove product hidden-print">
                        <button class="action delete remove-from-compare-list" data-id="<?php echo esc_attr( $product_data['product']->get_id() ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'Remove %s from comparison list', 'madimz'), $product_data['product']->get_title() ) ); ?>">
                            <span class="remove"><?php echo __( 'הסר מוצר', 'madimz' ); ?></span>
                        </button>
                    </td>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <tr>
                <th scope="row" class="cell label img-product"><span class="screen-reader-text"><?php echo __( 'Product', 'madimz' ); ?></span></th>
                <?php foreach( $data['product_data'] as $product_data ): ?>
                    <td data-th="<?php echo esc_attr( __( 'Image Product', 'madimz') ); ?>" class="cell img-product info">
                        <a href="<?php echo esc_attr( get_permalink( $product_data['product']->get_id() ) ); ?>">
                            <?php echo $product_data['image']; ?>
                        </a>
                    </td>
                <?php endforeach; ?>
            </tr>
            
            <tr>
                <th scope="row" class="cell label product-name"><span class="screen-reader-text"><?php echo __( 'Product', 'madimz' ); ?></span></th>
                <?php foreach( $data['product_data'] as $product_data ): ?>
                    <td data-th="<?php echo esc_attr( __( 'Name Product', 'madimz') ); ?>" class="cell product-name info">
                        <a href="<?php echo esc_attr( get_permalink( $product_data['product']->get_id() ) ); ?>">
                            <h2 class="<?php echo esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title' ) ); ?>"><?php echo esc_html( $product_data['product']->get_title() ); ?></h2>
                        </a>
                    </td>
                <?php endforeach; ?>
            </tr>
    
            <tr>
                <th scope="row" class="cell label product-price"><span class="screen-reader-text"><?php echo __( 'Product', 'madimz' ); ?></span></th>
                <?php foreach( $data['product_data'] as $product_data ): ?>
                    <td data-th="<?php echo esc_attr( __( 'Price Product', 'madimz') ); ?>" class="cell product-price info">
      
                        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>"><?php echo $product_data['product']->get_price_html(); ?></p>
    
                        <?php echo wc_get_stock_html( $product_data['product'] ); ?>
                    </td>
                <?php endforeach; ?>
            </tr>
        </tbody>
    </table>
</div>

