<?php
/**
 * Reviews Archive Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

// Get review categories for filter
$categories = get_terms(array(
    'taxonomy' => 'review_category',
    'hide_empty' => false,
));

$review_count = wp_count_posts('review')->publish;
?>

<main id="main-content">

<!-- Page Header -->
<section class="page-header">
    <div class="container">
        <div class="page-header-row">
            <div>
                <h1><?php esc_html_e('Ulasan', 'affos'); ?></h1>
                <p><?php esc_html_e('Review mendalam dan jujur dari tim editorial kami untuk membantu Anda memilih gadget terbaik.', 'affos'); ?></p>
            </div>
            <span class="result-count">
                <?php printf(esc_html__('%s ulasan', 'affos'), esc_html($review_count)); ?>
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
            <input type="text" placeholder="<?php esc_attr_e('Cari ulasan...', 'affos'); ?>" aria-label="<?php esc_attr_e('Cari ulasan', 'affos'); ?>">
        </div>
    </div>
</div>

<!-- Reviews Grid -->
<section class="section">
    <div class="container">
        <div class="review-grid">
            <?php
            $first = true;
            if (have_posts()):
                while (have_posts()):
                    the_post();
                    get_template_part('template-parts/card', 'review', array(
                        'review' => get_post(),
                        'featured' => $first,
                    ));
                    $first = false;
                endwhile;
            else:
                ?>
                <div class="no-reviews">
                    <p><?php esc_html_e('Belum ada ulasan.', 'affos'); ?></p>
                </div>
                <?php
            endif;
            ?>
        </div>

        <?php
        the_posts_pagination(array(
            'mid_size' => 2,
            'prev_text' => '<span class="screen-reader-text">' . __('Sebelumnya', 'affos') . '</span><i class="ri-arrow-left-s-line" aria-hidden="true"></i>',
            'next_text' => '<span class="screen-reader-text">' . __('Selanjutnya', 'affos') . '</span><i class="ri-arrow-right-s-line" aria-hidden="true"></i>',
        ));
        ?>
    </div>
</section>

</main>

<?php
get_footer();
