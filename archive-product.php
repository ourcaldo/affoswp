<?php
/**
 * Products Archive Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

// Get product categories for filter
$categories = get_terms(array(
    'taxonomy' => 'product_category',
    'hide_empty' => false,
));
?>

<!-- Hero Section - Products (Purple Theme) -->
<section class="archive-hero">
    <div class="container">
        <div class="archive-hero-content">
            <div class="archive-hero-text">
                <span class="archive-badge">
                    <i class="ri-scales-3-line"></i>
                    <?php esc_html_e('Fitur Perbandingan', 'affos'); ?>
                </span>
                <h1>
                    <?php esc_html_e('Bandingkan Gadget', 'affos'); ?><br><span>
                        <?php esc_html_e('Secara Head-to-Head', 'affos'); ?>
                    </span>
                </h1>
                <p>
                    <?php esc_html_e('Pilih hingga 3 gadget dan bandingkan spesifikasi, fitur, dan harga secara langsung untuk membuat keputusan terbaik.', 'affos'); ?>
                </p>
                <div class="archive-hero-stats">
                    <div class="stat-box">
                        <span class="stat-num">
                            <?php echo esc_html(wp_count_posts('product')->publish); ?>+
                        </span>
                        <span class="stat-label">
                            <?php esc_html_e('Gadget', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">50+</span>
                        <span class="stat-label">
                            <?php esc_html_e('Spesifikasi', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">100%</span>
                        <span class="stat-label">
                            <?php esc_html_e('Gratis', 'affos'); ?>
                        </span>
                    </div>
                </div>
                <?php
                $compare_page = get_option('affos_compare_page');
                $compare_url = $compare_page ? get_permalink($compare_page) : home_url('/bandingkan/');
                ?>
                <a href="<?php echo esc_url($compare_url); ?>" class="btn btn-light archive-cta">
                    <?php esc_html_e('Mulai Bandingkan', 'affos'); ?>
                    <i class="ri-arrow-right-line"></i>
                </a>
            </div>
            <div class="archive-hero-visual">
                <div class="compare-phones">
                    <div class="phone-mock phone-left">
                        <div class="phone-screen">
                            <div class="spec-row"></div>
                            <div class="spec-row short"></div>
                            <div class="spec-row"></div>
                        </div>
                        <span class="phone-name">iPhone 15</span>
                    </div>
                    <div class="compare-vs">VS</div>
                    <div class="phone-mock phone-right">
                        <div class="phone-screen">
                            <div class="spec-row"></div>
                            <div class="spec-row short"></div>
                            <div class="spec-row"></div>
                        </div>
                        <span class="phone-name">Galaxy S24</span>
                    </div>
                </div>
                <!-- Decorative elements -->
                <div class="hero-ring ring-1"></div>
                <div class="hero-ring ring-2"></div>
                <div class="hero-dots"></div>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="archive-filter">
    <div class="container">
        <div class="filter-bar">
            <button class="filter-btn active" data-filter="all">
                <i class="ri-apps-line"></i>
                <?php esc_html_e('Semua', 'affos'); ?>
            </button>
            <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                <?php foreach ($categories as $category):
                    // Determine icon based on category slug
                    $icon = 'ri-smartphone-line';
                    if ($category->slug === 'laptop') {
                        $icon = 'ri-macbook-line';
                    } elseif ($category->slug === 'tablet') {
                        $icon = 'ri-tablet-line';
                    } elseif ($category->slug === 'audio') {
                        $icon = 'ri-headphone-line';
                    } elseif (in_array($category->slug, array('wearable', 'smartwatch'))) {
                        $icon = 'ri-time-line';
                    }
                    ?>
                    <button class="filter-btn" data-filter="<?php echo esc_attr($category->slug); ?>">
                        <i class="<?php echo esc_attr($icon); ?>"></i>
                        <?php echo esc_html($category->name); ?>
                    </button>
                <?php endforeach; ?>
            <?php else: ?>
                <button class="filter-btn" data-filter="smartphone">
                    <i class="ri-smartphone-line"></i>
                    <?php esc_html_e('Smartphone', 'affos'); ?>
                </button>
                <button class="filter-btn" data-filter="laptop">
                    <i class="ri-macbook-line"></i>
                    <?php esc_html_e('Laptop', 'affos'); ?>
                </button>
                <button class="filter-btn" data-filter="tablet">
                    <i class="ri-tablet-line"></i>
                    <?php esc_html_e('Tablet', 'affos'); ?>
                </button>
            <?php endif; ?>
        </div>
    </div>
</section>

<!-- Products Grid -->
<section class="archive-products">
    <div class="container">
        <div class="product-grid" id="products-grid">
            <?php if (have_posts()): ?>
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/card', 'product', array('product' => get_post())); ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <p>
                        <?php esc_html_e('Belum ada produk.', 'affos'); ?>
                    </p>
                </div>
            <?php endif; ?>
        </div>

        <?php
        // Pagination
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '<i class="ri-arrow-left-line"></i> ' . __('Sebelumnya', 'affos'),
            'next_text' => __('Selanjutnya', 'affos') . ' <i class="ri-arrow-right-line"></i>',
        ));
        ?>
    </div>
</section>

<?php
get_footer();
