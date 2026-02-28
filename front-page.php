<?php
/**
 * Template Name: Home Page
 * Template for the front page
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

// Get latest product for hero spotlight
$hero_products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => 5,
    'orderby' => 'date',
    'order' => 'DESC',
));

$spotlight = !empty($hero_products) ? $hero_products[0] : null;
?>

<main id="main-content">

<!-- Hero Section -->
<section class="hero">
    <div class="container hero-inner">
        <div class="hero-content">
            <p class="hero-tagline"><?php esc_html_e('Platform Gadget #1', 'affos'); ?></p>
            <h1><?php esc_html_e('Temukan Gadget', 'affos'); ?> <em><?php esc_html_e('Terbaik', 'affos'); ?></em> <?php esc_html_e('untuk Anda', 'affos'); ?></h1>
            <p class="hero-desc"><?php esc_html_e('Bandingkan spesifikasi, baca ulasan mendalam, dan temukan harga terbaik untuk smartphone, laptop, dan gadget lainnya.', 'affos'); ?></p>
            <form class="hero-search" role="search" action="<?php echo esc_url(home_url('/')); ?>" method="get">
                <i class="ri-search-line" aria-hidden="true"></i>
                <input type="text" name="s" placeholder="<?php esc_attr_e('Cari produk, ulasan, atau artikel...', 'affos'); ?>" aria-label="<?php esc_attr_e('Cari produk', 'affos'); ?>">
            </form>
        </div>

        <div class="hero-spotlight">
            <?php if ($spotlight):
                $s_id = $spotlight->ID;
                $s_price = get_post_meta($s_id, '_misc_price', true);
                $s_chipset = get_post_meta($s_id, '_platform_chipset', true);
                $s_camera = get_post_meta($s_id, '_camera_main_specs', true);
                $s_battery = get_post_meta($s_id, '_battery_type', true);
                $s_cats = get_the_terms($s_id, 'product_category');
                $s_cat_name = ($s_cats && !is_wp_error($s_cats)) ? $s_cats[0]->name : '';
                $s_cat_slug = ($s_cats && !is_wp_error($s_cats)) ? $s_cats[0]->slug : 'smartphone';

                $s_icon = 'ri-smartphone-line';
                if ($s_cat_slug === 'laptop') $s_icon = 'ri-laptop-line';
                elseif ($s_cat_slug === 'tablet') $s_icon = 'ri-tablet-line';

                $s_camera_short = '';
                if ($s_camera) {
                    preg_match('/(\d+)\s*MP/i', $s_camera, $m);
                    $s_camera_short = isset($m[0]) ? $m[0] : '';
                }
                $s_battery_short = '';
                if ($s_battery) {
                    preg_match('/(\d+)\s*mAh/i', $s_battery, $m);
                    $s_battery_short = isset($m[0]) ? $m[0] : '';
                }
            ?>
                <a href="<?php echo esc_url(get_permalink($s_id)); ?>" class="hero-spotlight-img cat-<?php echo esc_attr($s_cat_slug); ?>">
                    <span class="badge"><?php esc_html_e('Produk Unggulan', 'affos'); ?></span>
                    <?php if (has_post_thumbnail($s_id)): ?>
                        <?php echo get_the_post_thumbnail($s_id, 'medium_large'); ?>
                    <?php else: ?>
                        <i class="<?php echo esc_attr($s_icon); ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                </a>
                <div class="hero-spotlight-body">
                    <p class="overline"><?php echo esc_html($s_cat_name); ?></p>
                    <h3><?php echo esc_html($spotlight->post_title); ?></h3>
                    <?php if ($s_price): ?>
                        <p class="price"><?php echo esc_html($s_price); ?></p>
                    <?php endif; ?>
                    <div class="specs-row">
                        <?php if ($s_chipset): ?>
                            <span class="spec-chip"><i class="ri-cpu-line" aria-hidden="true"></i> <?php echo esc_html($s_chipset); ?></span>
                        <?php endif; ?>
                        <?php if ($s_camera_short): ?>
                            <span class="spec-chip"><i class="ri-camera-line" aria-hidden="true"></i> <?php echo esc_html($s_camera_short); ?></span>
                        <?php endif; ?>
                        <?php if ($s_battery_short): ?>
                            <span class="spec-chip"><i class="ri-battery-2-charge-line" aria-hidden="true"></i> <?php echo esc_html($s_battery_short); ?></span>
                        <?php endif; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="hero-spotlight-img cat-smartphone">
                    <span class="badge"><?php esc_html_e('Produk Unggulan', 'affos'); ?></span>
                    <i class="ri-smartphone-line" aria-hidden="true"></i>
                </div>
                <div class="hero-spotlight-body">
                    <p class="overline"><?php esc_html_e('Smartphone', 'affos'); ?></p>
                    <h3><?php esc_html_e('Samsung Galaxy S24 Ultra', 'affos'); ?></h3>
                    <p class="price">Rp 19.999.000</p>
                    <div class="specs-row">
                        <span class="spec-chip"><i class="ri-cpu-line" aria-hidden="true"></i> Snapdragon 8 Gen 3</span>
                        <span class="spec-chip"><i class="ri-camera-line" aria-hidden="true"></i> 200MP</span>
                        <span class="spec-chip"><i class="ri-battery-2-charge-line" aria-hidden="true"></i> 5000mAh</span>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Trending Strip -->
<?php if (!empty($hero_products)): ?>
<section class="trending-strip">
    <div class="container trending-inner">
        <span class="trending-label"><?php esc_html_e('Trending', 'affos'); ?></span>
        <div class="trending-list">
            <?php
            $rank = 1;
            foreach ($hero_products as $tp) {
                printf(
                    '<a href="%s" class="trending-item"><span class="rank">%d</span><span class="name">%s</span></a>',
                    esc_url(get_permalink($tp->ID)),
                    $rank,
                    esc_html(wp_trim_words($tp->post_title, 4, ''))
                );
                $rank++;
            }
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Latest Products Section -->
<section class="section" id="smartphones">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Produk Terbaru', 'affos'); ?></h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="see-all">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="product-grid">
            <?php
            $products = get_posts(array(
                'post_type' => 'product',
                'posts_per_page' => 8,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (!empty($products)) {
                foreach ($products as $product) {
                    setup_postdata($product);
                    get_template_part('template-parts/card', 'product', array('product' => $product));
                }
                wp_reset_postdata();
            } else {
                ?>
                <div class="no-products">
                    <p><?php esc_html_e('Belum ada produk. Tambahkan produk dari dashboard.', 'affos'); ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Latest Reviews Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Ulasan Terbaru', 'affos'); ?></h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="see-all">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="review-grid">
            <?php
            $reviews = get_posts(array(
                'post_type' => 'review',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (!empty($reviews)) {
                $first = true;
                foreach ($reviews as $review) {
                    setup_postdata($review);
                    get_template_part('template-parts/card', 'review', array(
                        'review' => $review,
                        'featured' => $first,
                    ));
                    $first = false;
                }
                wp_reset_postdata();
            } else {
                ?>
                <div class="no-reviews">
                    <p><?php esc_html_e('Belum ada ulasan. Tambahkan ulasan dari dashboard.', 'affos'); ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Latest Blog Posts Section -->
<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title"><?php esc_html_e('Dari Blog', 'affos'); ?></h2>
            <?php
            $blog_page = get_option('page_for_posts');
            $blog_url = $blog_page ? get_permalink($blog_page) : home_url('/blog/');
            ?>
            <a href="<?php echo esc_url($blog_url); ?>" class="see-all">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="blog-grid">
            <?php
            $posts = get_posts(array(
                'post_type' => 'post',
                'posts_per_page' => 3,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (!empty($posts)) {
                foreach ($posts as $blog_post) {
                    setup_postdata($blog_post);
                    get_template_part('template-parts/card', 'blog', array('post' => $blog_post));
                }
                wp_reset_postdata();
            } else {
                ?>
                <div class="no-posts">
                    <p><?php esc_html_e('Belum ada artikel. Buat artikel dari dashboard.', 'affos'); ?></p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Newsletter -->
<section class="section">
    <div class="container">
        <div class="newsletter">
            <h2><?php esc_html_e('Dapatkan Update Terbaru', 'affos'); ?></h2>
            <p><?php esc_html_e('Langganan newsletter kami untuk mendapatkan ulasan, perbandingan, dan tips gadget terbaru langsung di inbox Anda.', 'affos'); ?></p>
            <form class="newsletter-form" action="#" method="post">
                <input type="email" placeholder="<?php esc_attr_e('Alamat email Anda', 'affos'); ?>" required aria-label="<?php esc_attr_e('Alamat email', 'affos'); ?>">
                <button type="submit" class="btn-primary"><?php esc_html_e('Langganan', 'affos'); ?></button>
            </form>
        </div>
    </div>
</section>

</main>

<?php
get_footer();
