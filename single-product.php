<?php
/**
 * Single Product Template
 *
 * @package Affos
 * @since 1.0.0
 */

get_header();

while (have_posts()):
    the_post();
    $product_id = get_the_ID();

    // Get spec sections from centralized definition
    $all_specs = affos_get_product_spec_sections();

    // Build frontend-friendly spec array (key => label only, selected sections)
    $frontend_sections = array('network', 'body', 'display', 'platform', 'memory', 'main_camera', 'selfie_camera', 'battery', 'comms', 'sound', 'features');
    $specs = array();
    foreach ($frontend_sections as $section_id) {
        if (!isset($all_specs[$section_id])) continue;
        $section = $all_specs[$section_id];
        $fields = array();
        foreach ($section['fields'] as $field_key => $field) {
            $fields[$field_key] = $field['label'];
        }
        $specs[$section_id] = array(
            'title' => $section['title'],
            'icon' => $section['icon'],
            'fields' => $fields,
        );
    }

    // Get quick specs
    $chipset = get_post_meta($product_id, '_platform_chipset', true);
    $camera = get_post_meta($product_id, '_camera_main_specs', true);
    $battery = get_post_meta($product_id, '_battery_type', true);
    $display = get_post_meta($product_id, '_display_size', true);
    $price = get_post_meta($product_id, '_misc_price', true);
    $buy_links = get_post_meta($product_id, '_product_buy_links', true);
    $status = get_post_meta($product_id, '_launch_status', true);

    // Get terms
    $categories = get_the_terms($product_id, 'product_category');
    $brands = get_the_terms($product_id, 'product_brand');
    $brand_name = ($brands && !is_wp_error($brands)) ? $brands[0]->name : '';
    $category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';
    $category_slug = ($categories && !is_wp_error($categories)) ? $categories[0]->slug : '';

    // Extract camera MP
    $camera_display = '';
    if ($camera) {
        preg_match('/(\d+)\s*MP/i', $camera, $matches);
        $camera_display = isset($matches[0]) ? $matches[0] : '';
    }

    // Extract battery mAh
    $battery_display = '';
    if ($battery) {
        preg_match('/(\d+)\s*mAh/i', $battery, $matches);
        $battery_display = isset($matches[0]) ? $matches[0] : '';
    }

    // Map category to icon
    $cat_icon = 'ri-smartphone-line';
    if ($category_slug === 'laptop') $cat_icon = 'ri-laptop-line';
    elseif ($category_slug === 'tablet') $cat_icon = 'ri-tablet-line';
    ?>

    <main id="main-content">
        <!-- Breadcrumb -->
        <div class="container">
            <nav class="breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'affos'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Beranda', 'affos'); ?></a>
                <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
                <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>"><?php esc_html_e('Produk', 'affos'); ?></a>
                <?php if ($category_name): ?>
                    <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
                    <a href="<?php echo esc_url(get_term_link($categories[0])); ?>"><?php echo esc_html($category_name); ?></a>
                <?php endif; ?>
                <span class="sep"><i class="ri-arrow-right-s-line" aria-hidden="true"></i></span>
                <span class="current-crumb" aria-current="page"><?php echo esc_html(get_the_title()); ?></span>
            </nav>
        </div>

        <!-- Product Hero -->
        <div class="container">
            <div class="product-hero">
                <div class="product-gallery cat-<?php echo esc_attr($category_slug ?: 'smartphone'); ?>">
                    <?php if (has_post_thumbnail()): ?>
                        <?php the_post_thumbnail('large', array('class' => 'gallery-main-img')); ?>
                    <?php else: ?>
                        <i class="<?php echo esc_attr($cat_icon); ?>" aria-hidden="true"></i>
                    <?php endif; ?>
                </div>

                <div class="product-info">
                    <div class="badge-row">
                        <?php if (strpos(strtolower($status), 'available') !== false): ?>
                            <span class="badge"><?php esc_html_e('Terbaru', 'affos'); ?></span>
                        <?php endif; ?>
                        <?php if ($brand_name): ?>
                            <span class="badge"><?php echo esc_html($brand_name); ?></span>
                        <?php endif; ?>
                    </div>
                    <h1><?php echo esc_html(get_the_title()); ?></h1>
                    <?php if (has_excerpt()): ?>
                        <p class="tagline"><?php echo esc_html(get_the_excerpt()); ?></p>
                    <?php endif; ?>

                    <div class="quick-specs">
                        <?php if ($chipset): ?>
                            <div class="quick-spec-item">
                                <i class="ri-cpu-line" aria-hidden="true"></i>
                                <div class="spec-detail">
                                    <span class="spec-label"><?php esc_html_e('Chipset', 'affos'); ?></span>
                                    <span class="spec-value"><?php echo esc_html($chipset); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($camera_display): ?>
                            <div class="quick-spec-item">
                                <i class="ri-camera-line" aria-hidden="true"></i>
                                <div class="spec-detail">
                                    <span class="spec-label"><?php esc_html_e('Camera', 'affos'); ?></span>
                                    <span class="spec-value"><?php echo esc_html($camera_display); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($battery_display): ?>
                            <div class="quick-spec-item">
                                <i class="ri-battery-2-charge-line" aria-hidden="true"></i>
                                <div class="spec-detail">
                                    <span class="spec-label"><?php esc_html_e('Battery', 'affos'); ?></span>
                                    <span class="spec-value"><?php echo esc_html($battery_display); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($display): ?>
                            <div class="quick-spec-item">
                                <i class="ri-smartphone-line" aria-hidden="true"></i>
                                <div class="spec-detail">
                                    <span class="spec-label"><?php esc_html_e('Display', 'affos'); ?></span>
                                    <span class="spec-value"><?php echo esc_html($display); ?></span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="price-section">
                        <p class="price-label"><?php esc_html_e('Harga Mulai', 'affos'); ?></p>
                        <p class="price-value"><?php echo esc_html($price ?: __('Hubungi Toko', 'affos')); ?></p>
                    </div>

                    <div class="product-actions">
                        <button class="btn-primary add-to-compare"
                            data-compare-id="<?php echo esc_attr($product_id); ?>">
                            <i class="ri-repeat-line" aria-hidden="true"></i>
                            <?php esc_html_e('Bandingkan', 'affos'); ?>
                        </button>
                        <button class="btn-secondary" aria-label="<?php esc_attr_e('Simpan', 'affos'); ?>">
                            <i class="ri-heart-line" aria-hidden="true"></i>
                        </button>
                        <button class="btn-ghost" aria-label="<?php esc_attr_e('Bagikan', 'affos'); ?>">
                            <i class="ri-share-line" aria-hidden="true"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Buy Section -->
        <?php if (!empty($buy_links) && is_array($buy_links)): ?>
            <section class="buy-section">
                <div class="container">
                    <h2 class="section-title"><?php esc_html_e('Tempat Beli', 'affos'); ?></h2>
                    <div class="buy-grid">
                        <?php foreach ($buy_links as $link):
                            if (empty($link['store_url']))
                                continue;
                            $store_info = affos_get_store_info($link['store_name']);
                            ?>
                            <a href="<?php echo esc_url($link['store_url']); ?>" class="buy-card" target="_blank"
                                rel="noopener noreferrer">
                                <div class="store-icon">
                                    <?php if (!empty($store_info['logo_url'])): ?>
                                        <img src="<?php echo esc_url($store_info['logo_url']); ?>"
                                            alt="<?php echo esc_attr($store_info['name']); ?>">
                                    <?php else: ?>
                                        <i class="<?php echo esc_attr($store_info['icon']); ?>" aria-hidden="true"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="store-info">
                                    <div class="store-name"><?php echo esc_html($store_info['name']); ?></div>
                                    <div class="store-price"><?php echo esc_html($link['store_price']); ?></div>
                                </div>
                                <i class="ri-arrow-right-s-line buy-arrow" aria-hidden="true"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Spec Section -->
        <section class="spec-section">
            <div class="container">
                <h2 class="section-title"><?php esc_html_e('Spesifikasi Lengkap', 'affos'); ?></h2>
                <div class="spec-table">
                    <?php foreach ($specs as $section_id => $section):
                        $has_values = false;
                        foreach ($section['fields'] as $field_id => $field_label) {
                            if (get_post_meta($product_id, $field_id, true)) {
                                $has_values = true;
                                break;
                            }
                        }
                        if (!$has_values)
                            continue;
                        ?>
                        <div class="spec-group">
                            <div class="spec-group-header">
                                <i class="<?php echo esc_attr($section['icon']); ?>" aria-hidden="true"></i>
                                <?php echo esc_html($section['title']); ?>
                            </div>
                            <?php foreach ($section['fields'] as $field_id => $field_label):
                                $value = get_post_meta($product_id, $field_id, true);
                                if (!$value)
                                    continue;
                                ?>
                                <div class="spec-row">
                                    <span class="spec-label"><?php echo esc_html($field_label); ?></span>
                                    <span class="spec-val"><?php echo esc_html($value); ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Related Reviews -->
        <?php
        $related_reviews = get_posts(array(
            'post_type' => 'review',
            'posts_per_page' => 3,
            'meta_query' => array(
                array(
                    'key' => '_review_product_id',
                    'value' => $product_id,
                    'compare' => '=',
                ),
            ),
        ));

        if (!empty($related_reviews)):
            ?>
            <section class="section">
                <div class="container">
                    <div class="section-header">
                        <h2 class="section-title"><?php esc_html_e('Ulasan Terkait', 'affos'); ?></h2>
                        <a href="<?php echo esc_url(get_post_type_archive_link('review')); ?>" class="see-all">
                            <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
                        </a>
                    </div>
                    <div class="review-grid">
                        <?php
                        foreach ($related_reviews as $review) {
                            get_template_part('template-parts/card', 'review', array('review' => $review));
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </section>
        <?php endif; ?>

        <!-- Related Products -->
        <section class="section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title"><?php esc_html_e('Produk Terkait', 'affos'); ?></h2>
                    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="see-all">
                        <?php esc_html_e('Lihat Semua', 'affos'); ?> <i class="ri-arrow-right-line"></i>
                    </a>
                </div>
                <div class="product-grid">
                    <?php
                    $related = get_posts(array(
                        'post_type' => 'product',
                        'posts_per_page' => 4,
                        'post__not_in' => array($product_id),
                        'tax_query' => $categories ? array(
                            array(
                                'taxonomy' => 'product_category',
                                'field' => 'term_id',
                                'terms' => wp_list_pluck($categories, 'term_id'),
                            ),
                        ) : array(),
                    ));

                    foreach ($related as $product) {
                        get_template_part('template-parts/card', 'product', array('product' => $product));
                    }
                    wp_reset_postdata();
                    ?>
                </div>
            </div>
        </section>
    </main>

    <?php
endwhile;
get_footer();
