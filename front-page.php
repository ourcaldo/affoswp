<?php
/**
 * Template Name: Home Page
 * Template for the front page
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

// Get latest products for hero carousel
$hero_products = get_posts(array(
    'post_type' => 'product',
    'posts_per_page' => 6,
    'orderby' => 'date',
    'order' => 'DESC',
));

// Get product count
$product_count = wp_count_posts('product')->publish;
$review_count = wp_count_posts('review')->publish;
?>

<!-- Hero Section - Two Column Layout -->
<section class="hero-v2">
    <div class="container">
        <div class="hero-grid">
            <div class="hero-text">
                <div class="hero-announcement">
                    <span class="badge badge-new">
                        <?php esc_html_e('Baru', 'affos'); ?>
                    </span>
                    <?php
                    // Get latest product for announcement
                    if (!empty($hero_products)) {
                        $latest = $hero_products[0];
                        printf('<span>%s %s</span>', esc_html($latest->post_title), esc_html__('Kini Tersedia!', 'affos'));
                    } else {
                        echo '<span>' . esc_html__('Produk Terbaru Tersedia!', 'affos') . '</span>';
                    }
                    ?>
                </div>

                <h1>
                    <?php esc_html_e('Temukan Gadget', 'affos'); ?><br>
                    <?php esc_html_e('Impian Terbaikmu', 'affos'); ?>
                </h1>
                <p>
                    <?php esc_html_e('Website ulasan dan perbandingan gadget terlengkap di Indonesia. Cek spesifikasi, bandingkan fitur, dan temukan harga termurah.', 'affos'); ?>
                </p>

                <div class="hero-cta">
                    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn btn-primary">
                        <?php esc_html_e('Jelajahi Gadget', 'affos'); ?>
                    </a>
                    <?php
                    $compare_page = get_option('affos_compare_page');
                    $compare_url = $compare_page ? get_permalink($compare_page) : home_url('/bandingkan/');
                    ?>
                    <a href="<?php echo esc_url($compare_url); ?>" class="btn btn-outline-dark">
                        <?php esc_html_e('Bandingkan', 'affos'); ?> <i class="ri-arrow-right-line"></i>
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <span class="stat-num">
                            <?php echo esc_html($product_count > 0 ? $product_count . '+' : '500+'); ?>
                        </span>
                        <span class="stat-text">
                            <?php esc_html_e('Gadget', 'affos'); ?>
                        </span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-num">50+</span>
                        <span class="stat-text">
                            <?php esc_html_e('Spesifikasi', 'affos'); ?>
                        </span>
                    </div>
                    <div class="hero-stat">
                        <span class="stat-num">10K+</span>
                        <span class="stat-text">
                            <?php esc_html_e('Pengguna', 'affos'); ?>
                        </span>
                    </div>
                </div>
            </div>

            <div class="hero-visual">
                <!-- Animated Product Carousel -->
                <div class="hero-carousel-container">
                    <div class="hero-carousel-track">
                        <?php
                        // Build hero cards array
                        $hero_cards = array();

                        // Static fallback cards
                        $fallback_cards = array(
                            array('slug' => 'iphone', 'title' => 'iPhone 15 Pro', 'price' => 'Rp 24.9jt', 'label' => 'Terlaris', 'icon_class' => 'ri-smartphone-line', 'card_class' => ''),
                            array('slug' => 'samsung', 'title' => 'Galaxy S24 Ultra', 'price' => 'Rp 21.9jt', 'label' => 'Flagship', 'icon_class' => 'ri-smartphone-line', 'card_class' => 'samsung'),
                            array('slug' => 'macbook', 'title' => 'MacBook Air M3', 'price' => 'Rp 22.9jt', 'label' => 'Best Value', 'icon_class' => 'ri-macbook-line', 'card_class' => 'laptop'),
                            array('slug' => 'pixel', 'title' => 'Google Pixel 8', 'price' => 'Rp 14.9jt', 'label' => 'AI Camera', 'icon_class' => 'ri-smartphone-line', 'card_class' => 'pixel'),
                            array('slug' => 'ipad', 'title' => 'iPad Pro M4', 'price' => 'Rp 18.9jt', 'label' => 'Pro Tablet', 'icon_class' => 'ri-tablet-line', 'card_class' => 'tablet'),
                            array('slug' => 'xiaomi', 'title' => 'Xiaomi 14 Ultra', 'price' => 'Rp 17.9jt', 'label' => 'Premium', 'icon_class' => 'ri-smartphone-line', 'card_class' => 'xiaomi'),
                        );

                        // Add dynamic products
                        if (!empty($hero_products)) {
                            foreach ($hero_products as $product) {
                                $price = get_post_meta($product->ID, '_misc_price', true);
                                $chipset = get_post_meta($product->ID, '_platform_chipset', true);
                                $category = '';
                                $terms = get_the_terms($product->ID, 'product_category');
                                if ($terms && !is_wp_error($terms)) {
                                    $category = $terms[0]->slug;
                                }

                                $icon_class = 'ri-smartphone-line';
                                $card_class = '';
                                if ($category === 'laptop') {
                                    $icon_class = 'ri-macbook-line';
                                    $card_class = 'laptop';
                                } elseif ($category === 'tablet') {
                                    $icon_class = 'ri-tablet-line';
                                    $card_class = 'tablet';
                                }

                                $labels = array('Terlaris', 'Flagship', 'Best Value', 'Premium', 'Pro', 'New');
                                $label = $chipset ? $chipset : $labels[array_rand($labels)];

                                $hero_cards[] = array(
                                    'slug' => $product->post_name,
                                    'title' => $product->post_title,
                                    'price' => $price ?: 'Lihat Harga',
                                    'label' => $label,
                                    'icon_class' => $icon_class,
                                    'card_class' => $card_class,
                                );
                            }
                        }

                        // Always pad to 6 cards using fallbacks
                        $fallback_index = 0;
                        while (count($hero_cards) < 6 && $fallback_index < count($fallback_cards)) {
                            $hero_cards[] = $fallback_cards[$fallback_index];
                            $fallback_index++;
                        }

                        // Output cards - just once, the animation keyframes handle the scroll
                        foreach ($hero_cards as $card) {
                            ?>
                            <div class="hero-card" data-product="<?php echo esc_attr($card['slug']); ?>">
                                <div class="hero-card-icon <?php echo esc_attr($card['card_class']); ?>">
                                    <i class="<?php echo esc_attr($card['icon_class']); ?>"></i>
                                </div>
                                <div class="hero-card-text">
                                    <span class="hc-label"><?php echo esc_html($card['label']); ?></span>
                                    <span class="hc-title"><?php echo esc_html($card['title']); ?></span>
                                </div>
                                <span class="hc-price"><?php echo esc_html($card['price']); ?></span>
                            </div>
                            <?php
                        }
                        ?>
                    </div>
                </div>

                <!-- 3D Product View -->
                <div class="hero-3d-view">
                    <div class="product-3d-wrapper">
                        <div class="product-3d-phone">
                            <i class="ri-smartphone-line"></i>
                        </div>
                        <div class="product-3d-specs">
                            <div class="spec-pill"><i class="ri-cpu-line"></i> A17 Pro</div>
                            <div class="spec-pill"><i class="ri-camera-lens-line"></i> 48MP</div>
                            <div class="spec-pill"><i class="ri-battery-line"></i> All Day</div>
                        </div>
                        <div class="product-3d-name">iPhone 15 Pro</div>
                    </div>
                </div>

                <!-- Click indicator -->
                <div class="click-cursor">
                    <i class="ri-cursor-fill"></i>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Latest Products Section -->
<section class="products-section" id="smartphones">
    <div class="container">
        <div class="section-header">
            <h2>
                <?php esc_html_e('Produk Terbaru', 'affos'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn btn-outline-dark btn-sm">
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
                    <p>
                        <?php esc_html_e('Belum ada produk. Tambahkan produk dari dashboard.', 'affos'); ?>
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Latest Reviews Section -->
<section class="reviews-section">
    <div class="container">
        <div class="section-header">
            <h2>
                <?php esc_html_e('Ulasan Terbaru', 'affos'); ?>
            </h2>
            <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="btn btn-outline-dark btn-sm">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="reviews-grid">
            <?php
            $reviews = get_posts(array(
                'post_type' => 'review',
                'posts_per_page' => 4,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (!empty($reviews)) {
                foreach ($reviews as $review) {
                    setup_postdata($review);
                    get_template_part('template-parts/card', 'review', array('review' => $review));
                }
                wp_reset_postdata();
            } else {
                ?>
                <div class="no-reviews">
                    <p>
                        <?php esc_html_e('Belum ada ulasan. Tambahkan ulasan dari dashboard.', 'affos'); ?>
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<!-- Latest Blog Posts Section -->
<section class="blog-section">
    <div class="container">
        <div class="section-header">
            <h2>
                <?php esc_html_e('Berita & Artikel', 'affos'); ?>
            </h2>
            <?php
            $blog_page = get_option('page_for_posts');
            $blog_url = $blog_page ? get_permalink($blog_page) : home_url('/blog/');
            ?>
            <a href="<?php echo esc_url($blog_url); ?>" class="btn btn-outline-dark btn-sm">
                <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
            </a>
        </div>

        <div class="blog-grid">
            <?php
            $posts = get_posts(array(
                'post_type' => 'post',
                'posts_per_page' => 4,
                'orderby' => 'date',
                'order' => 'DESC',
            ));

            if (!empty($posts)) {
                foreach ($posts as $post) {
                    setup_postdata($post);
                    get_template_part('template-parts/card', 'blog', array('post' => $post));
                }
                wp_reset_postdata();
            } else {
                ?>
                <div class="no-posts">
                    <p>
                        <?php esc_html_e('Belum ada artikel. Buat artikel dari dashboard.', 'affos'); ?>
                    </p>
                </div>
                <?php
            }
            ?>
        </div>
    </div>
</section>

<?php
get_footer();
