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
?>

<!-- Hero Section - Reviews (Teal Theme) -->
<section class="reviews-hero">
    <div class="container">
        <div class="reviews-hero-content">
            <div class="reviews-hero-text">
                <span class="reviews-badge">
                    <i class="ri-star-line"></i>
                    <?php esc_html_e('Ulasan Mendalam', 'affos'); ?>
                </span>
                <h1>
                    <?php esc_html_e('Review Gadget', 'affos'); ?><br><span>
                        <?php esc_html_e('Jujur & Lengkap', 'affos'); ?>
                    </span>
                </h1>
                <p>
                    <?php esc_html_e('Tim ahli kami menguji setiap gadget secara menyeluruh untuk memberikan ulasan yang objektif, detail, dan membantu Anda membuat keputusan pembelian.', 'affos'); ?>
                </p>
                <div class="reviews-hero-stats">
                    <div class="stat-box">
                        <span class="stat-num">
                            <?php echo esc_html(wp_count_posts('review')->publish); ?>+
                        </span>
                        <span class="stat-label">
                            <?php esc_html_e('Review', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">5</span>
                        <span class="stat-label">
                            <?php esc_html_e('Reviewer', 'affos'); ?>
                        </span>
                    </div>
                    <div class="stat-box">
                        <span class="stat-num">4.8</span>
                        <span class="stat-label">
                            <?php esc_html_e('Rating', 'affos'); ?>
                        </span>
                    </div>
                </div>
            </div>
            <div class="reviews-hero-visual">
                <div class="review-cards-stack">
                    <?php
                    // Get top 3 reviews for visual
                    $top_reviews = get_posts(array(
                        'post_type' => 'review',
                        'posts_per_page' => 3,
                        'orderby' => 'meta_value_num',
                        'meta_key' => '_review_score',
                        'order' => 'DESC',
                    ));

                    $card_class = 1;
                    foreach ($top_reviews as $review) {
                        $score = get_post_meta($review->ID, '_review_score', true);
                        ?>
                        <div class="review-card-mini card-<?php echo $card_class; ?>">
                            <div class="mini-rating">
                                <i class="ri-star-fill"></i>
                                <span>
                                    <?php echo esc_html(number_format((float) $score, 1)); ?>
                                </span>
                            </div>
                            <div class="mini-title">
                                <?php echo esc_html(wp_trim_words($review->post_title, 3, '')); ?>
                            </div>
                        </div>
                        <?php
                        $card_class++;
                    }

                    // Fallback if no reviews
                    if (empty($top_reviews)) {
                        ?>
                        <div class="review-card-mini card-1">
                            <div class="mini-rating"><i class="ri-star-fill"></i><span>9.2</span></div>
                            <div class="mini-title">iPhone 15 Pro</div>
                        </div>
                        <div class="review-card-mini card-2">
                            <div class="mini-rating"><i class="ri-star-fill"></i><span>9.0</span></div>
                            <div class="mini-title">Galaxy S24</div>
                        </div>
                        <div class="review-card-mini card-3">
                            <div class="mini-rating"><i class="ri-star-fill"></i><span>8.8</span></div>
                            <div class="mini-title">Pixel 8 Pro</div>
                        </div>
                        <?php
                    }
                    ?>
                </div>
                <!-- Decorative elements -->
                <div class="review-ring ring-1"></div>
                <div class="review-ring ring-2"></div>
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="reviews-filter">
    <div class="container">
        <div class="filter-bar reviews-filter-bar">
            <button class="filter-btn active" data-filter="all">
                <i class="ri-apps-line"></i>
                <?php esc_html_e('Semua', 'affos'); ?>
            </button>
            <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                <?php foreach ($categories as $category):
                    $icon = 'ri-smartphone-line';
                    if ($category->slug === 'laptop') {
                        $icon = 'ri-macbook-line';
                    } elseif ($category->slug === 'tablet') {
                        $icon = 'ri-tablet-line';
                    } elseif ($category->slug === 'audio') {
                        $icon = 'ri-headphone-line';
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

<!-- Reviews Grid -->
<section class="reviews-archive">
    <div class="container">
        <div class="reviews-grid">
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
                    <p>
                        <?php esc_html_e('Belum ada ulasan.', 'affos'); ?>
                    </p>
                </div>
                <?php
            endif;
            ?>
        </div>

        <?php
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
