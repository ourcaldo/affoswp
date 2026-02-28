<?php
/**
 * Single Review Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();
?>

<main id="main-content">

<?php
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
    $category_slug = ($categories && !is_wp_error($categories)) ? $categories[0]->slug : '';

    // Get author info
    $author_id = get_the_author_meta('ID');
    $author_name = get_the_author_meta('display_name');

    // Reading time
    $content = get_the_content();
    $word_count = str_word_count(strip_tags(strip_shortcodes($content)));
    $reading_time = max(1, ceil($word_count / 200));

    // Score level class
    $score_num = (float) $score;
    $score_class = '';
    if ($score_num >= 9.0) $score_class = 'excellent';
    elseif ($score_num >= 7.0) $score_class = 'good';
    elseif ($score_num >= 5.0) $score_class = 'average';
    else $score_class = 'poor';

    // Category icon
    $cat_icon = 'ri-smartphone-line';
    if ($category_slug === 'laptop') $cat_icon = 'ri-macbook-line';
    elseif ($category_slug === 'tablet') $cat_icon = 'ri-tablet-line';
    elseif ($category_slug === 'audio') $cat_icon = 'ri-headphone-line';

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

    <!-- Breadcrumb -->
    <div class="container">
        <nav class="breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'affos'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Beranda', 'affos'); ?></a>
            <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
            <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>"><?php esc_html_e('Ulasan', 'affos'); ?></a>
            <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
            <span class="current-crumb" aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
        </nav>
    </div>

    <!-- Review Header -->
    <section class="review-header">
        <div class="container">
            <div class="review-header-inner">
                <div class="review-header-content">
                    <?php if ($category_name): ?>
                        <p class="category-label"><?php echo esc_html($category_name); ?></p>
                    <?php endif; ?>
                    <div class="post-meta">
                        <span><?php echo esc_html(get_the_date('j M Y')); ?></span>
                        <span>&middot;</span>
                        <span><?php printf(esc_html__('%d menit baca', 'affos'), $reading_time); ?></span>
                    </div>
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <?php if (has_excerpt()): ?>
                        <p class="excerpt"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php endif; ?>
                    <div class="review-author">
                        <div class="avatar">
                            <?php echo get_avatar($author_id, 48); ?>
                        </div>
                        <div class="author-info">
                            <div class="name"><?php echo esc_html($author_name); ?></div>
                            <div class="role"><?php esc_html_e('Senior Reviewer', 'affos'); ?></div>
                        </div>
                    </div>
                </div>

                <?php if ($score): ?>
                    <div class="score-circle <?php echo esc_attr($score_class); ?>">
                        <span class="score-num"><?php echo esc_html(number_format($score_num, 1)); ?></span>
                        <span class="score-verdict"><?php echo esc_html($verdict ?: __('Good', 'affos')); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <!-- Review Hero Image -->
    <div class="container">
        <div class="review-hero-img cat-<?php echo esc_attr($category_slug ?: 'smartphone'); ?>">
            <?php if (has_post_thumbnail()): ?>
                <?php the_post_thumbnail('full', array('class' => 'hero-image')); ?>
            <?php else: ?>
                <i class="<?php echo esc_attr($cat_icon); ?>" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
    </div>

    <!-- Review Layout -->
    <div class="container">
        <div class="review-layout">

            <!-- Sidebar -->
            <aside class="sidebar">

                <?php if ($product_price || !empty($buy_links)): ?>
                    <!-- Buy Card -->
                    <div class="sidebar-card">
                        <h4><?php esc_html_e('Beli Produk', 'affos'); ?></h4>
                        <?php if ($product_price): ?>
                            <div class="price-section">
                                <div class="price-label"><?php esc_html_e('Harga Mulai', 'affos'); ?></div>
                                <div class="price-value"><?php echo esc_html($product_price); ?></div>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($buy_links) && is_array($buy_links)): ?>
                            <div class="buy-link-list">
                                <?php foreach ($buy_links as $link):
                                    if (empty($link['store_url']))
                                        continue;
                                    $store_info = affos_get_store_info($link['store_name']);
                                    ?>
                                    <a href="<?php echo esc_url($link['store_url']); ?>" class="buy-link" target="_blank"
                                        rel="noopener">
                                        <span><?php echo esc_html($store_info['name']); ?></span>
                                        <span class="bl-price"><?php echo esc_html($link['store_price']); ?></span>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty(array_filter($product_specs))): ?>
                    <!-- Key Specs Card -->
                    <div class="sidebar-card">
                        <h4><?php esc_html_e('Spesifikasi Utama', 'affos'); ?></h4>
                        <div class="key-spec-list">
                            <?php foreach ($product_specs as $label => $value):
                                if (!$value)
                                    continue;
                                ?>
                                <div class="key-spec">
                                    <span class="k-label"><?php echo esc_html($label); ?></span>
                                    <span class="k-value"><?php echo esc_html(wp_trim_words($value, 3, '')); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <?php if ($product_id): ?>
                            <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="sidebar-detail-link">
                                <?php esc_html_e('Lihat Detail Lengkap', 'affos'); ?> &rarr;
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty(array_filter(array_column($ratings, 'value')))): ?>
                    <!-- Ratings Card -->
                    <div class="sidebar-card">
                        <h4><?php esc_html_e('Rating Detail', 'affos'); ?></h4>
                        <?php foreach ($ratings as $key => $rating):
                            if (!$rating['value'])
                                continue;
                            $percentage = ($rating['value'] / 10) * 100;
                            $rating_val = (float) $rating['value'];
                            $fill_class = $rating_val >= 9.0 ? 'excellent' : ($rating_val >= 7.0 ? 'good' : 'average');
                            ?>
                            <div class="rating-row">
                                <span class="rating-label"><?php echo esc_html($rating['label']); ?></span>
                                <div class="rating-bar">
                                    <div class="rating-fill <?php echo esc_attr($fill_class); ?>" style="width: <?php echo esc_attr($percentage); ?>%"></div>
                                </div>
                                <span class="rating-num"><?php echo esc_html(number_format($rating_val, 1)); ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if ($pros || $cons): ?>
                    <!-- Verdict Card -->
                    <div class="sidebar-card">
                        <h4><?php esc_html_e('Verdict', 'affos'); ?></h4>
                        <div class="pros-cons">
                            <?php if ($pros):
                                $pros_list = array_filter(explode("\n", $pros));
                                ?>
                                <div class="pros">
                                    <h5><?php esc_html_e('Kelebihan', 'affos'); ?></h5>
                                    <ul>
                                        <?php foreach ($pros_list as $pro): ?>
                                            <li><?php echo esc_html(trim($pro)); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                            <?php if ($cons):
                                $cons_list = array_filter(explode("\n", $cons));
                                ?>
                                <div class="cons">
                                    <h5><?php esc_html_e('Kekurangan', 'affos'); ?></h5>
                                    <ul>
                                        <?php foreach ($cons_list as $con): ?>
                                            <li><?php echo esc_html(trim($con)); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>

            </aside>

            <!-- Article Content -->
            <article class="article-content">
                <?php the_content(); ?>

                <!-- Share Row -->
                <div class="share-row">
                    <a href="<?php echo esc_url('https://twitter.com/intent/tweet?url=' . rawurlencode(get_permalink()) . '&text=' . rawurlencode(get_the_title())); ?>"
                        target="_blank" rel="noopener" class="share-btn">
                        <i class="ri-twitter-x-line" aria-hidden="true"></i>
                        <?php esc_html_e('Twitter', 'affos'); ?>
                    </a>
                    <a href="<?php echo esc_url('https://www.facebook.com/sharer/sharer.php?u=' . rawurlencode(get_permalink())); ?>"
                        target="_blank" rel="noopener" class="share-btn">
                        <i class="ri-facebook-line" aria-hidden="true"></i>
                        <?php esc_html_e('Facebook', 'affos'); ?>
                    </a>
                    <a href="<?php echo esc_url('https://wa.me/?text=' . rawurlencode(get_the_title() . ' ' . get_permalink())); ?>"
                        target="_blank" rel="noopener" class="share-btn">
                        <i class="ri-whatsapp-line" aria-hidden="true"></i>
                        <?php esc_html_e('WhatsApp', 'affos'); ?>
                    </a>
                </div>
            </article>

        </div>
    </div>

    <!-- Related Reviews -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <h2 class="section-title"><?php esc_html_e('Ulasan Terkait', 'affos'); ?></h2>
                <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="see-all">
                    <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line" aria-hidden="true"></i>
                </a>
            </div>
            <div class="review-grid">
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
?>

</main>

<?php
get_footer();
