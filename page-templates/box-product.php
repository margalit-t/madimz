<?php 
    global $product; 
    $pdt_name = $product->get_name();       
    $pdt_permalink = get_permalink( $product->get_id() );

    if ( $product->is_type( 'variable' ) ) {
        $regular_price = $product->get_variation_regular_price();
        $sale_price = ($regular_price != $product->get_variation_sale_price())? $product->get_variation_sale_price() : '';
    }
    else{
        $regular_price = $product->get_regular_price();
        $sale_price = $product->get_sale_price();
    }
?>

            
<div class="box_product flex card-product <?php echo !empty($image_hover_url) ? 'kare-lazy' :'' ?>" id="<?php echo $product->get_id();?>">
    <div  class="product_img_wrapper">
        <div class="image_with_tags">
            <div class="image-pdts thumbnail <?php echo !empty($image_hover_url) ? 'has-hover' : ''; ?>">
                <a href="<?php echo esc_url($pdt_permalink); ?>" title="<?php echo esc_attr($pdt_name); ?>">
                    <?php
                    $thumbnail_id = $product->get_image_id();
                    if ( $thumbnail_id ) {
                        if ( is_archive() || is_singular( 'inspiration' )) {
                            // $full_img_url = wp_get_attachment_url( $thumbnail_id );
                            // if ( $full_img_url ) {
                            //     echo '<img src="' . esc_url( $full_img_url ) . '" loading="lazy" alt="' . esc_attr( $product->get_title() ) . '">';
                            // }
                            $large_img = wp_get_attachment_image_src( $thumbnail_id, 'medium_large' );
                            if ( $large_img ) {
                                echo '<img src="' . esc_url( $large_img[0] ) . '" loading="lazy" alt="' . esc_attr( $product->get_title() ) . '">';
                            }
                        } else {
                            $medium_img = wp_get_attachment_image_src( $thumbnail_id, 'medium' );
                            if ( $medium_img ) {
                                echo '<img src="' . esc_url( $medium_img[0] ) . '" loading="lazy" alt="' . esc_attr( $product->get_title() ) . '">';
                            }
        
                        }
                    }
                    ?>
                </a>
            </div>
            <?php if(!empty($image_hover_url)): ?>
                <div class="image-pdts thumbnail-hover">
                    <a href="<?php echo $pdt_permalink; ?>" title="<?php echo $pdt_name;?>">
                        <img data-src="<?php echo  $image_hover_url; ?>" loading="lazy" alt="<?php echo $product->get_title(); ?>">
                    </a>
                </div> 
            <?php endif; ?>

            <?php if(!empty($sale_price)){ ?>
                <div class="sale_tag">
                    <span class="text_sale_percent"> <?php echo '-'.$percent.'%'; ?></span>
                </div>
            <?php } ?>

            <?php 
            $product_tags = get_the_terms( $product->get_id(), 'product_tag' );

            if ( $product_tags && !is_wp_error( $product_tags ) ) { ?>
                <div class="wc_tag">
                    <?php foreach ( $product_tags as $tag ) { ?>
                        <div class="text_tag"> <?php echo esc_html( $tag->name ); ?> </div>
                    <?php } ?>
                </div>
            <?php } ?>    
        </div>
    </div>
    <div class="product_details_wrapper">
        <a class="product_details" href="<?php echo $pdt_permalink?>" title="<?php echo $pdt_name;?>">
            <p class="name-product"><?php echo $pdt_name;?></p>  
            <div class="pdt_price_wrapper">
                <?php if(!empty($sale_price)): ?>
                    <p class="sale_price"> 
                        <!-- <span>RRP*: </span> -->
                        <span> <?php echo wc_price($sale_price); ?> </span>
                    </p>
                    <p class="regular_price <?php  echo (!empty($sale_price)) ? 'line-through' : '' ?>"><?php echo wc_price($regular_price); ?></p>
                <?php else : ?>
                    <p class="regular_price <?php  echo (!empty($sale_price)) ? 'line-through' : '' ?>"><?php echo wc_price($regular_price); ?></p>
                    <p class="transparent">&nbsp;</p>
                <?php endif; ?> 
            </div>
    
        </a>
    </div>
</div>
