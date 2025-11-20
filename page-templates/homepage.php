<?php
    /*
    Template Name: Homepage
    */

    get_header();
?>
<section class="section-wrap design-banner-wrapper">
    <?php if ( have_rows( 'homepage_header_gallery' ) ) : ?>
        
        <!-- Desktop Swiper -->
        <div class="header-top desktop-swiper swiper">
            <div class="swiper-wrapper">

                <?php while ( have_rows( 'homepage_header_gallery' ) ): the_row();
                    $img_desk = get_sub_field( 'top_banner_image_desk' );
                    $url = get_sub_field( 'url_image' );
                ?>

                    <?php if ( $img_desk ): ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url($url['url']); ?>" target="<?php echo esc_attr($url['target']) ?: ''; ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr($url['title']) ?: 'Main header image'; ?>">
                                <img class="img-banner-desk" src="<?php echo esc_url( $img_desk['url'] ); ?>" alt="<?php echo esc_attr( $img_desk['alt'] ?: 'Image Banner' ); ?>" />
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>

        <!-- Mobile Swiper -->
        <div class="header-top mobile-swiper swiper">
            <div class="swiper-wrapper">
                <?php while ( have_rows( 'homepage_header_gallery' ) ): the_row(); 
                    $img_mobile = get_sub_field( 'top_banner_image_mobile' );
                    $url = get_sub_field( 'url_image' );
                ?>
                    <?php if ( $img_mobile ): ?>
                        <div class="swiper-slide">
                            <a href="<?php echo esc_url($url['url']); ?>" target="<?php echo esc_attr($url['target']) ?: ''; ?>" rel="noopener noreferrer" aria-label="<?php echo esc_attr($url['title']) ?: 'Main header image'; ?>">
                                <img class="img-banner-mobile" src="<?php echo esc_url( $img_mobile['url'] ); ?>" alt="<?php echo esc_attr( $img_mobile['alt'] ?: 'Image Banner' ); ?>"/>
                            </a>
                        </div>                        
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>
        </div>
    <?php endif; ?>
</section>

<section class="section-wrap carousel-category-wrapper">
     <?php  
        $categories = get_field('choose_category_carousel');
    ?>
    <div class="category-carousel swiper">
        <div class="swiper-carousel-cat swiper-wrapper">
            <?php foreach($categories as $key => $cat) : 
                $cat_name = $cat->name; 
                $cat_link = get_term_link( $cat->term_id );
                $thumbnail_id = get_term_meta( $cat->term_id, 'thumbnail_id', true );
                $cat_image = wp_get_attachment_image_url( $thumbnail_id, 'product_slider' );
            ?>   
                <div class="swiper-slide">
                    <a href="<?php echo esc_url( $cat_link  ); ?>" class="slide-category" title="<?php echo esc_html( $cat_name );?>" aria-lable="link to category">
                        <div class="bg-circle"></div>
                        <img src="<?php echo esc_url( $cat_image ); ?>" alt="<?php echo esc_html( $cat_name );?>" width="300" height="300" loading="lazy" />
                        <p class="cat-name"><?php echo esc_html( $cat_name );?></p>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
        <!-- dropping points-->
        <div class="swiper-pagination"></div>
        
        <!-- arrows -->
        <div class="swiper-main-nav">
            <button class="swiper-button-prev">
                <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
            </button>
            <button class="swiper-button-next">
                <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
            </button>
        </div>
    </div>

</section>

<section class="section-wrap profession-info-wrapper">
    <?php
        $profession_title = get_field( 'profession_title' );
    ?>

    <?php if ( $profession_title ) : ?>
        <h2 class="title"><?php echo esc_html( $profession_title ); ?></h2>
    <?php endif; ?>

    <?php if ( have_rows( 'profession_info' ) ) : ?>
        <div class="cards-profession-info">

            <?php while ( have_rows( 'profession_info' ) ): the_row(); ?>

                <?php
                    $title = get_sub_field( 'title' );
                    $description = get_sub_field( 'description' );
                    $image = get_sub_field( 'image' );
                    $image_url = wp_get_attachment_image_url( $image, 'medium' ); 
                    $btn_text = get_sub_field( 'button_text' );
                    $url_btn = get_sub_field( 'url_button' );
                ?>

                <?php if ( $title && $description && $image ): ?>
                    <div class="card-profession">
                        <img class="img-card-profession" src="<?php echo esc_url( $image ); ?>" alt="<?php echo esc_attr( $title ?: 'Image Card Profession' ); ?>" />
                        <div class="card-content">
                            <h5><?php echo esc_html( $title ); ?></h5>
                            <p><?php echo wp_kses_post( $description ); ?></p>
                            <a class="red-btn" href="<?php echo esc_url( $url_btn ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_url( $btn_text ) ?: 'Products button'; ?>">
                                <?php echo esc_html( $btn_text ); ?>
                            </a>
                        </div>

                    </div>
                <?php endif; ?>                   
            <?php endwhile; ?>
        </div>
	<?php endif; ?>

</section>

<section class="section-wrap favorite-pdts-wrapper">
    <div class="container">
        <?php $favorite_pdts_title = get_field( 'products_title' ); ?>
        <?php if ( $favorite_pdts_title ) : ?>
            <h2 class="title"><?php echo esc_html( $favorite_pdts_title ); ?></h2>
        <?php endif; ?>
        
        <?php $featured_pdts = get_field( 'choose_products' ); ?>
        <?php if ( $featured_pdts ) : ?>
            <div class="swiper-slide-pdts swiper">
                <?php
                // Query for similar products sharing the same attribute term
                $args = array(
                    'post_type'      => 'product',
                    'post__in'       => $featured_pdts,
                    'posts_per_page' => -1,
                    'orderby'        => 'post__in',
                );
                $favorite_products = new WP_Query( $args );

                if ( $favorite_products->have_posts() ) : ?>
                    <ul class="products swiper-wrapper slider-products">
                        <?php while ( $favorite_products->have_posts() ) : ?>
                            <!-- // setup_postdata( $product ); -->
                            <?php $favorite_products->the_post(); ?>
                            <div  class="swiper-slide">
                                <?php wc_get_template_part( 'content', 'product' ); ?>
                                <!-- get_template_part('page-templates/box-product');  -->
                            </div >
                        <?php endwhile; ?>
                    </ul>
                    <?php wp_reset_postdata(); ?>
                <?php endif; ?>

                <!-- arrows -->
                <div class="swiper-main-nav">
                    <button class="swiper-button-prev">
                        <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
                    </button>
                    <button class="swiper-button-next">
                        <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</section>

<section  class="section-wrap about-us-wrapper">
    <div class="right-content">
        <?php
            $about_title = get_field( 'about_us_title' );
            $about_desc = get_field( 'about_us_description' );
            $about_btn_text = get_field( 'about_button_name' );
            $about_link_btn = get_field( 'about_button_link' );
        ?>

        <p class="about-title"><?php echo esc_html( $about_title ) ?: ''; ?></p>

        <div class="about-content">
            <span class="description">
                <?php echo $about_desc ? wp_kses_post( $about_desc ) : ''; ?>
            </span>

            <a href="<?php echo esc_url( $about_link_btn ); ?>" class="about-link" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_url( $about_btn_text ) ?: 'about button - read more'; ?>">
                <?php echo $about_btn_text ? esc_html( $about_btn_text ) : ''; ?>
            </a>
        </div>
    </div>

    <div class="left-content">
        <div class="red-bg" style="background-color: <?php echo get_field( 'bg_color' ) ?>;"></div>
        <div class="swiper gallery-carousel-about">
            <?php if ( have_rows( 'about_gallery_image' ) ) : ?>
                <div class="swiper-wrapper">

                    <?php while ( have_rows( 'about_gallery_image' ) ): the_row(); ?>

                        <?php
                            $about_img = get_sub_field( 'gallery_image' );
                            $about_img_url = wp_get_attachment_image_url( $about_img, 'medium' ); 
                        ?>

                        <?php if ( $about_img ): ?>
                            <div class="swiper-slide">
                                <img class="img-banner-desk" src="<?php echo esc_url( $about_img ); ?>" alt="about us gallery" />
                            </div>
                        <?php endif; ?>                            
                    <?php endwhile; ?>
                </div>
            <?php endif; ?>

            <!-- arrows -->
            <div class="swiper-main-nav">
                <button class="swiper-button-prev">
                    <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
                </button>
                <button class="swiper-button-next">
                    <?php inline_svg_with_class('swiper-arrow.svg', '') ?>
                </button>
            </div>
        </div>
    </div>

</section>

<?php get_template_part('template-parts/form', 'footer'); ?>
<?php get_footer();  ?>