<?php
/**
 * Single Review Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

while (have_posts()):
    the_post();
    $review_id = get_the_ID();

    // Get meta data
    $score = get_post_meta($review_id, '_review_score', true);
    $verdict = get_post_meta($review_id, '_review_verdict', true);
    $product_id = get_post_meta($review_id, '_review_product_id', true);
    $pros = get_post_meta($review_id, '_review_pros', true);
    $cons = get_post_meta($review_id, '_review_cons', true);

    // Rating categories
    $ratings = array(
        'design' => array('label' => __('Desain', 'affos'), 'value' => get_post_meta($review_id, '_review_rating_design', true)),
        'display' => array('label' => __('Layar', 'affos'), 'value' => get_post_meta($review_id, '_review_rating_display', true)),
        'performance' => array('label' => __('Performa', 'affos'), 'value' => get_post_meta($review_id, '_review_rating_performance', true)),
        'camera' => array('label' => __('Kamera', 'affos'), 'value' => get_post_meta($review_id, '_review_rating_camera', true)),
        'battery' => array('label' => __('Baterai', 'affos'), 'value' => get_post_meta($review_id, '_review_rating_battery', true)),
    );

    // Get category
    $categories = get_the_terms($review_id, 'review_category');
    $category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';

    // Get author info
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name');
    $author_bio = get_the_author_meta('description');

    // Reading time
    $content = get_the_content();
    $word_count = str_word_count(strip_tags($content));
    $reading_time = max(1, ceil($word_count / 200));

    // Get product info if linked
    $product_price = '';
    $product_specs = array();
    $buy_links = array();
    if ($product_id) {
        $product_price = get_post_meta($product_id, '_misc_price', true);
        $buy_links = get_post_meta($product_id, '_product_buy_links', true);
        $product_specs = array(
            __('Layar', 'affos') => get_post_meta($product_id, '_display_size', true),
            __('Prosesor', 'affos') => get_post_meta($product_id, '_platform_chipset', true),
            __('RAM', 'affos') => get_post_meta($product_id, '_memory_internal', true),
            __('Kamera', 'affos') => get_post_meta($product_id, '_camera_main_specs', true),
            __('Baterai', 'affos') => get_post_meta($product_id, '_battery_type', true),
        );
    }
    ?>

    <!-- Review Header -->
    <section class="review-header">
        <div class="container">
            <div class="review-breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php esc_html_e('Beranda', 'affos'); ?>
                </a>
                <i class="ri-arrow-right-s-line"></i>
                <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>">
                    <?php esc_html_e('Ulasan', 'affos'); ?>
                </a>
                <i class="ri-arrow-right-s-line"></i>
                <span>
                    <?php the_title(); ?>
                </span>
            </div>

            <div class="review-header-content">
                <div class="review-header-text">
                    <div class="review-header-meta">
                        <?php if ($category_name): ?>
                            <span class="review-cat-badge">
                                <?php echo esc_html($category_name); ?>
                            </span>
                        <?php endif; ?>
                        <span class="review-date-badge"><i class="ri-calendar-line"></i>
                            <?php echo get_the_date(); ?>
                        </span>
                        <span class="review-read-time"><i class="ri-time-line"></i>
                            <?php printf(__('%d min read', 'affos'), $reading_time); ?>
                        </span>
                    </div>
                    <h1>
                        <?php the_title(); ?>
                    </h1>
                    <?php if (has_excerpt()): ?>
                        <p class="review-header-excerpt">
                            <?php echo esc_html(get_the_excerpt()); ?>
                        </p>
                    <?php endif; ?>
                    <div class="review-header-author">
                        <div class="author-avatar-lg">
                            <?php echo get_avatar($author_id, 48); ?>
                        </div>
                        <div class="author-details">
                            <span class="author-name">
                                <?php echo esc_html($author_name); ?>
                            </span>
                            <span class="author-role">
                                <?php esc_html_e('Senior Reviewer • Tech Enthusiast', 'affos'); ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php if ($score): ?>
                    <div class="review-header-score">
                        <div class="score-circle">
                            <span class="score-value">
                                <?php echo esc_html(number_format((float) $score, 1)); ?>
                            </span>
                            <span class="score-label">
                                <?php echo esc_html($verdict ?: 'Good'); ?>
                            </span>
                        </div>
                        <?php if ($verdict === 'Excellent'): ?>
                            <div class="score-badge">
                                <i class="ri-award-fill"></i>
                                <?php esc_html_e("Editor's Choice", 'affos'); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Review Hero Image -->
    <section class="review-hero-image">
        <div class="container">
            <div class="hero-image-wrapper">
                <?php if (has_post_thumbnail()): ?>
                    <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
                <?php else: ?>
                    <div class="hero-image-placeholder">
                        <i class="ri-smartphone-line"></i>
                        <span>
                            <?php the_title(); ?>
                        </span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Review Content -->
    <section class="review-content-section">
        <div class="container">
            <div class="review-layout">
                <!-- Sidebar -->
                <aside class="review-sidebar">
                    <?php if ($product_price || !empty($buy_links)): ?>
                        <div class="sidebar-card buy-card">
                            <h4>
                                <?php esc_html_e('Beli Sekarang', 'affos'); ?>
                            </h4>
                            <?php if ($product_price): ?>
                                <p class="buy-price">
                                    <?php echo esc_html($product_price); ?>
                                </p>
                            <?php endif; ?>
                            <?php if (!empty($buy_links) && is_array($buy_links)): ?>
                                <div class="buy-links">
                                    <?php foreach ($buy_links as $link):
                                        if (empty($link['store_url']))
                                            continue;
                                        $store_info = affos_get_store_info($link['store_name']);
                                        ?>
                                        <a href="<?php echo esc_url($link['store_url']); ?>" class="buy-link" target="_blank"
                                            rel="noopener">
                                            <?php if (!empty($store_info['logo_url'])): ?>
                                                <img src="<?php echo esc_url($store_info['logo_url']); ?>"
                                                    alt="<?php echo esc_attr($store_info['name']); ?>" style="width: 20px; height: auto;">
                                            <?php else: ?>
                                                <i class="<?php echo esc_attr($store_info['icon']); ?>"></i>
                                            <?php endif; ?>
                                            <?php echo esc_html($store_info['name']); ?>
                                            <i class="ri-external-link-line"></i>
                                        </a>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty(array_filter($product_specs))): ?>
                        <div class="sidebar-card specs-card">
                            <h4>
                                <?php esc_html_e('Spesifikasi Utama', 'affos'); ?>
                            </h4>
                            <ul class="specs-list">
                                <?php foreach ($product_specs as $label => $value):
                                    if (!$value)
                                        continue;
                                    ?>
                                    <li><span>
                                            <?php echo esc_html($label); ?>
                                        </span><strong>
                                            <?php echo esc_html(wp_trim_words($value, 3, '')); ?>
                                        </strong></li>
                                <?php endforeach; ?>
                            </ul>
                            <?php if ($product_id): ?>
                                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="btn btn-outline btn-sm">
                                    <?php esc_html_e('Lihat Detail Lengkap', 'affos'); ?>
                                    <i class="ri-arrow-right-line"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty(array_filter(array_column($ratings, 'value')))): ?>
                        <div class="sidebar-card rating-card">
                            <h4>
                                <?php esc_html_e('Rating Detail', 'affos'); ?>
                            </h4>
                            <div class="rating-bars">
                                <?php foreach ($ratings as $key => $rating):
                                    if (!$rating['value'])
                                        continue;
                                    $percentage = ($rating['value'] / 10) * 100;
                                    ?>
                                    <div class="rating-item">
                                        <span class="rating-label">
                                            <?php echo esc_html($rating['label']); ?>
                                        </span>
                                        <div class="rating-bar">
                                            <div class="rating-fill" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                        </div>
                                        <span class="rating-score">
                                            <?php echo esc_html(number_format((float) $rating['value'], 1)); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ($pros || $cons): ?>
                        <div class="sidebar-card verdict-card">
                            <h4>
                                <?php esc_html_e('Verdict', 'affos'); ?>
                            </h4>
                            <div class="pros-cons">
                                <?php if ($pros):
                                    $pros_list = array_filter(explode("\n", $pros));
                                    ?>
                                    <div class="pros">
                                        <h5><i class="ri-thumb-up-fill"></i>
                                            <?php esc_html_e('Kelebihan', 'affos'); ?>
                                        </h5>
                                        <ul>
                                            <?php foreach ($pros_list as $pro): ?>
                                                <li>
                                                    <?php echo esc_html(trim($pro)); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                                <?php if ($cons):
                                    $cons_list = array_filter(explode("\n", $cons));
                                    ?>
                                    <div class="cons">
                                        <h5><i class="ri-thumb-down-fill"></i>
                                            <?php esc_html_e('Kekurangan', 'affos'); ?>
                                        </h5>
                                        <ul>
                                            <?php foreach ($cons_list as $con): ?>
                                                <li>
                                                    <?php echo esc_html(trim($con)); ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </aside>

                <!-- Main Article -->
                <article class="review-article">
                    <div class="article-content">
                        <?php the_content(); ?>
                    </div>

                    <!-- Share -->
                    <div class="article-share">
                        <span>
                            <?php esc_html_e('Bagikan:', 'affos'); ?>
                        </span>
                        <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode(get_permalink()); ?>&text=<?php echo urlencode(get_the_title()); ?>"
                            target="_blank" rel="noopener" class="share-btn">
                            <i class="ri-twitter-x-line"></i>
                        </a>
                        <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode(get_permalink()); ?>"
                            target="_blank" rel="noopener" class="share-btn">
                            <i class="ri-facebook-fill"></i>
                        </a>
                        <a href="https://wa.me/?text=<?php echo urlencode(get_the_title() . ' ' . get_permalink()); ?>"
                            target="_blank" rel="noopener" class="share-btn">
                            <i class="ri-whatsapp-line"></i>
                        </a>
                    </div>
                </article>
            </div>
        </div>
    </section>

    <!-- Related Reviews -->
    <section class="related-reviews">
        <div class="container">
            <h2 class="section-title">
                <?php esc_html_e('Ulasan Terkait', 'affos'); ?>
            </h2>
            <div class="reviews-grid">
                <?php
                $related = get_posts(array(
                    'post_type' => 'review',
                    'posts_per_page' => 3,
                    'post__not_in' => array($review_id),
                    'tax_query' => $categories ? array(
                        array(
                            'taxonomy' => 'review_category',
                            'field' => 'term_id',
                            'terms' => wp_list_pluck($categories, 'term_id'),
                        ),
                    ) : array(),
                ));

                foreach ($related as $review) {
                    get_template_part('template-parts/card', 'review', array('review' => $review));
                }
                wp_reset_postdata();
                ?>
            </div>
        </div>
    </section>

    <?php
endwhile;
get_footer();
