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

$product_count = wp_count_posts('product')->publish;
?>

<main id="main-content">

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-row">
            <div>
                <h1><?php esc_html_e('Produk', 'affos'); ?></h1>
                <p><?php esc_html_e('Jelajahi koleksi gadget terbaru dengan spesifikasi lengkap dan harga terkini.', 'affos'); ?></p>
            </div>
            <span class="result-count">
                <?php printf(esc_html__('%s produk', 'affos'), esc_html($product_count)); ?>
            </span>
        </div>
    </div>
</section>

<!-- Filter Bar -->
<div class="container">
    <div class="filter-bar">
        <button class="filter-btn active" data-filter="all">
            <?php esc_html_e('Semua', 'affos'); ?>
        </button>
        <?php if (!empty($categories) && !is_wp_error($categories)): ?>
            <?php foreach ($categories as $category): ?>
                <button class="filter-btn" data-filter="<?php echo esc_attr($category->slug); ?>">
                    <?php echo esc_html($category->name); ?>
                </button>
            <?php endforeach; ?>
        <?php else: ?>
            <button class="filter-btn" data-filter="smartphone">
                <?php esc_html_e('Smartphone', 'affos'); ?>
            </button>
            <button class="filter-btn" data-filter="laptop">
                <?php esc_html_e('Laptop', 'affos'); ?>
            </button>
            <button class="filter-btn" data-filter="tablet">
                <?php esc_html_e('Tablet', 'affos'); ?>
            </button>
        <?php endif; ?>

        <div class="filter-search">
            <i class="ri-search-line" aria-hidden="true"></i>
            <input type="text" placeholder="<?php esc_attr_e('Cari produk...', 'affos'); ?>">
        </div>
    </div>
</div>

<!-- Products Grid -->
<section class="section">
    <div class="container">
        <div class="product-grid" id="products-grid">
            <?php if (have_posts()): ?>
                <?php while (have_posts()):
                    the_post(); ?>
                    <?php get_template_part('template-parts/card', 'product', array('product' => get_post())); ?>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="no-products">
                    <p><?php esc_html_e('Belum ada produk.', 'affos'); ?></p>
                </div>
            <?php endif; ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '<i class="ri-arrow-left-s-line"></i>',
            'next_text' => '<i class="ri-arrow-right-s-line"></i>',
        ));
        ?>
    </div>
</section>

</main>

<?php
get_footer();
