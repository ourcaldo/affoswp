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

    // Get all meta data
    $specs = array(
        'network' => array(
            'title' => __('Network', 'affos'),
            'icon' => 'ri-signal-tower-line',
            'fields' => array(
                '_network_technology' => __('Technology', 'affos'),
                '_network_2g_bands' => __('2G Bands', 'affos'),
                '_network_3g_bands' => __('3G Bands', 'affos'),
                '_network_4g_bands' => __('4G Bands', 'affos'),
                '_network_5g_bands' => __('5G Bands', 'affos'),
                '_network_speed' => __('Speed', 'affos'),
            ),
        ),
        'body' => array(
            'title' => __('Body', 'affos'),
            'icon' => 'ri-smartphone-line',
            'fields' => array(
                '_body_dimensions' => __('Dimensions', 'affos'),
                '_body_weight' => __('Weight', 'affos'),
                '_body_sim' => __('SIM', 'affos'),
                '_body_other' => __('Build', 'affos'),
            ),
        ),
        'display' => array(
            'title' => __('Display', 'affos'),
            'icon' => 'ri-artboard-line',
            'fields' => array(
                '_display_type' => __('Type', 'affos'),
                '_display_size' => __('Size', 'affos'),
                '_display_resolution' => __('Resolution', 'affos'),
                '_display_protection' => __('Protection', 'affos'),
            ),
        ),
        'platform' => array(
            'title' => __('Platform', 'affos'),
            'icon' => 'ri-cpu-line',
            'fields' => array(
                '_platform_os' => __('OS', 'affos'),
                '_platform_chipset' => __('Chipset', 'affos'),
                '_platform_cpu' => __('CPU', 'affos'),
                '_platform_gpu' => __('GPU', 'affos'),
            ),
        ),
        'memory' => array(
            'title' => __('Memory', 'affos'),
            'icon' => 'ri-hard-drive-line',
            'fields' => array(
                '_memory_card_slot' => __('Card Slot', 'affos'),
                '_memory_internal' => __('Internal', 'affos'),
            ),
        ),
        'camera' => array(
            'title' => __('Camera', 'affos'),
            'icon' => 'ri-camera-lens-line',
            'fields' => array(
                '_camera_main_specs' => __('Main', 'affos'),
                '_camera_main_features' => __('Features', 'affos'),
                '_camera_main_video' => __('Video', 'affos'),
                '_camera_selfie_specs' => __('Selfie', 'affos'),
            ),
        ),
        'battery' => array(
            'title' => __('Battery', 'affos'),
            'icon' => 'ri-battery-charge-line',
            'fields' => array(
                '_battery_type' => __('Type', 'affos'),
                '_battery_charging' => __('Charging', 'affos'),
            ),
        ),
        'comms' => array(
            'title' => __('Comms', 'affos'),
            'icon' => 'ri-wifi-line',
            'fields' => array(
                '_comms_wlan' => __('WLAN', 'affos'),
                '_comms_bluetooth' => __('Bluetooth', 'affos'),
                '_comms_positioning' => __('Positioning', 'affos'),
                '_comms_nfc' => __('NFC', 'affos'),
                '_comms_usb' => __('USB', 'affos'),
            ),
        ),
    );

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
    ?>

    <main class="product-page" id="main-content">
        <div class="container">
            <!-- Breadcrumb -->
            <nav class="breadcrumb" aria-label="<?php esc_attr_e('Breadcrumb', 'affos'); ?>">
                <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>">
                    <?php esc_html_e('Produk', 'affos'); ?>
                </a>
                <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                <?php if ($category_name): ?>
                    <a href="<?php echo esc_url(get_term_link($categories[0])); ?>">
                        <?php echo esc_html($category_name); ?>
                    </a>
                    <i class="ri-arrow-right-s-line" aria-hidden="true"></i>
                <?php endif; ?>
                <span aria-current="page">
                    <?php the_title(); ?>
                </span>
            </nav>

            <!-- Product Header -->
            <div class="product-hero">
                <div class="product-gallery">
                    <div class="gallery-main">
                        <?php if (has_post_thumbnail()): ?>
                            <?php the_post_thumbnail('large', array('class' => 'gallery-main-img')); ?>
                        <?php else: ?>
                            <div class="gallery-main-placeholder">
                                <i class="ri-smartphone-line" aria-hidden="true"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="product-info">
                    <div class="product-badges">
                        <?php if (strpos(strtolower($status), 'available') !== false): ?>
                            <span class="badge badge-new">
                                <?php esc_html_e('Terbaru', 'affos'); ?>
                            </span>
                        <?php endif; ?>
                        <?php if ($brand_name): ?>
                            <span class="badge badge-brand">
                                <?php echo esc_html($brand_name); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h1 class="product-title">
                        <?php the_title(); ?>
                    </h1>
                    <?php if (has_excerpt()): ?>
                        <p class="product-tagline">
                            <?php echo esc_html(get_the_excerpt()); ?>
                        </p>
                    <?php endif; ?>

                    <div class="product-quick-specs">
                        <?php if ($chipset): ?>
                            <div class="quick-spec">
                                <i class="ri-cpu-line"></i>
                                <div>
                                    <span class="spec-label">
                                        <?php esc_html_e('Chipset', 'affos'); ?>
                                    </span>
                                    <span class="spec-value">
                                        <?php echo esc_html($chipset); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($camera_display): ?>
                            <div class="quick-spec">
                                <i class="ri-camera-lens-line"></i>
                                <div>
                                    <span class="spec-label">
                                        <?php esc_html_e('Kamera', 'affos'); ?>
                                    </span>
                                    <span class="spec-value">
                                        <?php echo esc_html($camera_display); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($battery_display): ?>
                            <div class="quick-spec">
                                <i class="ri-battery-charge-line"></i>
                                <div>
                                    <span class="spec-label">
                                        <?php esc_html_e('Baterai', 'affos'); ?>
                                    </span>
                                    <span class="spec-value">
                                        <?php echo esc_html($battery_display); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                        <?php if ($display): ?>
                            <div class="quick-spec">
                                <i class="ri-artboard-line"></i>
                                <div>
                                    <span class="spec-label">
                                        <?php esc_html_e('Layar', 'affos'); ?>
                                    </span>
                                    <span class="spec-value">
                                        <?php echo esc_html($display); ?>
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div class="product-price-section">
                        <div class="price-label">
                            <?php esc_html_e('Harga Mulai', 'affos'); ?>
                        </div>
                        <div class="price-value">
                            <?php echo esc_html($price ?: __('Hubungi Toko', 'affos')); ?>
                        </div>
                        <div class="price-note">
                            <?php esc_html_e('*Harga dapat berbeda di setiap toko', 'affos'); ?>
                        </div>
                    </div>

                    <div class="product-actions">
                        <button class="btn btn-primary add-to-compare"
                            data-compare-id="<?php echo esc_attr($product_id); ?>">
                            <i class="ri-scales-3-line"></i>
                            <?php esc_html_e('Bandingkan', 'affos'); ?>
                        </button>
                        <button class="btn btn-outline"><i class="ri-heart-line"></i>
                            <?php esc_html_e('Simpan', 'affos'); ?>
                        </button>
                        <button class="btn btn-outline"><i class="ri-share-line"></i>
                            <?php esc_html_e('Bagikan', 'affos'); ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Where to Buy -->
            <?php if (!empty($buy_links) && is_array($buy_links)): ?>
                <section class="where-to-buy">
                    <h2 class="section-title"><i class="ri-store-2-line"></i>
                        <?php esc_html_e('Tempat Beli', 'affos'); ?>
                    </h2>
                    <div class="store-list">
                        <?php foreach ($buy_links as $link):
                            if (empty($link['store_url']))
                                continue;
                            $store_info = affos_get_store_info($link['store_name']);
                            ?>
                            <a href="<?php echo esc_url($link['store_url']); ?>" class="store-card" target="_blank"
                                rel="noopener noreferrer">
                                <div class="store-logo">
                                    <?php if (!empty($store_info['logo_url'])): ?>
                                        <img src="<?php echo esc_url($store_info['logo_url']); ?>"
                                            alt="<?php echo esc_attr($store_info['name']); ?>">
                                    <?php else: ?>
                                        <i class="<?php echo esc_attr($store_info['icon']); ?>"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="store-info">
                                    <span class="store-name">
                                        <?php echo esc_html($store_info['name']); ?>
                                    </span>
                                    <span class="store-price">
                                        <?php echo esc_html($link['store_price']); ?>
                                    </span>
                                </div>
                                <i class="ri-external-link-line store-link-icon"></i>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Full Specifications -->
            <section class="full-specs">
                <h2 class="section-title"><i class="ri-file-list-3-line"></i>
                    <?php esc_html_e('Spesifikasi Lengkap', 'affos'); ?>
                </h2>

                <div class="specs-container">
                    <?php foreach ($specs as $section_id => $section):
                        // Check if any field has value
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
                        <div class="spec-table">
                            <div class="spec-table-header">
                                <i class="<?php echo esc_attr($section['icon']); ?>"></i>
                                <h3>
                                    <?php echo esc_html($section['title']); ?>
                                </h3>
                            </div>
                            <div class="spec-table-body">
                                <?php foreach ($section['fields'] as $field_id => $field_label):
                                    $value = get_post_meta($product_id, $field_id, true);
                                    if (!$value)
                                        continue;
                                    ?>
                                    <div class="spec-row">
                                        <span class="spec-key">
                                            <?php echo esc_html($field_label); ?>
                                        </span>
                                        <span class="spec-val">
                                            <?php echo esc_html($value); ?>
                                        </span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
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
                <section class="related-reviews">
                    <h2 class="section-title">
                        <?php esc_html_e('Ulasan Terkait', 'affos'); ?>
                    </h2>
                    <div class="product-grid">
                        <?php
                        foreach ($related_reviews as $review) {
                            get_template_part('template-parts/card', 'review', array('review' => $review));
                        }
                        wp_reset_postdata();
                        ?>
                    </div>
                </section>
            <?php endif; ?>

            <!-- Related Products -->
            <section class="related-products">
                <h2 class="section-title">
                    <?php esc_html_e('Produk Terkait', 'affos'); ?>
                </h2>
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
            </section>
        </div>
    </main>

    <?php
endwhile;
get_footer();
