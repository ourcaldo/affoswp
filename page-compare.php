<?php
/**
 * Template Name: Compare Page
 * Template for comparing products
 * 
 * Supports:
 * - /bandingkan/ (empty state or localStorage-based)
 * - /bandingkan/product-slug-vs-product-slug/ (SEO-friendly URLs)
 *
 * @package Affos
 * @since 1.1.1
 */

get_header();

// Check if products are specified via URL
$compare_products_slug = get_query_var('compare_products', '');
$url_products = array();
$has_url_products = false;

if ($compare_products_slug) {
    $slugs = explode('-vs-', $compare_products_slug);
    foreach ($slugs as $slug) {
        $product = get_page_by_path($slug, OBJECT, 'product');
        if ($product) {
            $url_products[] = $product->ID;
        }
    }
    $has_url_products = count($url_products) >= 2;
}

// Calculate grid columns based on product count
$product_count = count($url_products);
$show_add_slot = $product_count < 3;
$total_cols = $product_count + ($show_add_slot ? 1 : 0);
?>

<main class="compare-page<?php echo $has_url_products ? ' has-products' : ''; ?>">
    <div class="container">
        <?php if (!$has_url_products): ?>
            <!-- Empty State: Hero Section -->
            <div class="compare-header">
                <span class="compare-badge">
                    <i class="ri-scales-3-line"></i>
                    <?php esc_html_e('Perbandingan', 'affos'); ?>
                </span>
                <h1><?php esc_html_e('Bandingkan Gadget', 'affos'); ?></h1>
                <p><?php esc_html_e('Pilih hingga 3 gadget untuk membandingkan spesifikasi secara langsung.', 'affos'); ?>
                </p>
            </div>

            <!-- Empty State Message -->
            <div class="compare-empty-state">
                <i class="ri-scales-3-line"></i>
                <p><?php esc_html_e('Belum ada produk yang dipilih untuk dibandingkan.', 'affos'); ?></p>
                <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="btn btn-primary">
                    <i class="ri-smartphone-line"></i>
                    <?php esc_html_e('Pilih Gadget', 'affos'); ?>
                </a>
            </div>

        <?php else: ?>
            <!-- Compare Results: Has Products -->

            <!-- Page Title -->
            <div class="compare-title-section">
                <h1><?php esc_html_e('Bandingkan Gadget', 'affos'); ?></h1>
                <p><?php esc_html_e('Bandingkan spesifikasi dan harga dari produk yang Anda pilih', 'affos'); ?></p>
            </div>

            <!-- Actions Bar -->
            <div class="compare-actions-bar">
                <div class="compare-actions-left">
                    <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="action-link">
                        <i class="ri-add-circle-line"></i>
                        <?php esc_html_e('Tambah Produk', 'affos'); ?>
                    </a>
                    <a href="<?php echo esc_url(home_url('/bandingkan/')); ?>" class="action-link danger"
                        id="clear-compare-all">
                        <i class="ri-close-circle-line"></i>
                        <?php esc_html_e('Hapus Semua', 'affos'); ?>
                    </a>
                </div>
                <label class="toggle-label">
                    <input type="checkbox" id="show-diff-only">
                    <?php esc_html_e('Tampilkan perbedaan saja', 'affos'); ?>
                </label>
            </div>

            <!-- Compare Table -->
            <div class="compare-table-container">
                <!-- Product Cards Header -->
                <div class="compare-products-header"
                    style="grid-template-columns: 200px repeat(<?php echo $total_cols; ?>, 1fr);">
                    <div class="compare-label-cell">
                        <!-- Empty corner -->
                    </div>

                    <?php foreach ($url_products as $index => $pid):
                        $product = get_post($pid);
                        $thumbnail = get_the_post_thumbnail_url($pid, 'medium');
                        $price = get_post_meta($pid, '_misc_price', true);
                        $status = get_post_meta($pid, '_launch_status', true);
                        $is_available = strpos(strtolower($status), 'available') !== false;
                        $review_count = get_comments_number($pid);
                        ?>
                        <div class="compare-product-card" data-product-id="<?php echo esc_attr($pid); ?>">
                            <?php if ($index === 0): ?>
                                <span class="product-badge recommended"><?php esc_html_e('Rekomendasi', 'affos'); ?></span>
                            <?php endif; ?>
                            <button class="remove-product-btn" data-remove-id="<?php echo esc_attr($pid); ?>" aria-label="<?php esc_attr_e('Hapus produk', 'affos'); ?>">
                                <i class="ri-close-line" aria-hidden="true"></i>
                            </button>
                            <div class="product-image">
                                <?php if ($thumbnail): ?>
                                    <img src="<?php echo esc_url($thumbnail); ?>"
                                        alt="<?php echo esc_attr($product->post_title); ?>">
                                <?php else: ?>
                                    <i class="ri-smartphone-line" aria-hidden="true"></i>
                                <?php endif; ?>
                            </div>
                            <h3 class="product-name"><?php echo esc_html($product->post_title); ?></h3>
                            <div class="product-rating" aria-hidden="true">
                                <?php
                                // Get actual review score for this product
                                $product_review = get_posts(array(
                                    'post_type' => 'review',
                                    'posts_per_page' => 1,
                                    'meta_query' => array(
                                        array(
                                            'key' => '_review_product_id',
                                            'value' => $pid,
                                            'compare' => '=',
                                        ),
                                    ),
                                ));
                                $review_score = 0;
                                if (!empty($product_review)) {
                                    $review_score = (float) get_post_meta($product_review[0]->ID, '_review_score', true);
                                }
                                if ($review_score > 0):
                                    $full_stars = floor($review_score / 2);
                                    $half_star = ($review_score / 2) - $full_stars >= 0.25;
                                    $empty_stars = 5 - $full_stars - ($half_star ? 1 : 0);
                                    for ($s = 0; $s < $full_stars; $s++): ?>
                                        <i class="ri-star-fill"></i>
                                    <?php endfor;
                                    if ($half_star): ?>
                                        <i class="ri-star-half-fill"></i>
                                    <?php endif;
                                    for ($s = 0; $s < $empty_stars; $s++): ?>
                                        <i class="ri-star-line"></i>
                                    <?php endfor; ?>
                                    <span><?php echo esc_html(number_format($review_score, 1)); ?></span>
                                <?php else: ?>
                                    <span><?php esc_html_e('Belum ada review', 'affos'); ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="product-availability">
                                <?php if ($is_available): ?>
                                    <i class="ri-checkbox-circle-fill"></i>
                                    <?php esc_html_e('Tersedia', 'affos'); ?>
                                <?php else: ?>
                                    <i class="ri-time-line"></i>
                                    <?php esc_html_e('Coming Soon', 'affos'); ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if ($show_add_slot): ?>
                        <!-- Add Product Slot -->
                        <div class="add-product-slot">
                            <a href="<?php echo esc_url(get_post_type_archive_link('product')); ?>" class="add-product-btn">
                                <i class="ri-add-line"></i>
                                <span><?php esc_html_e('Tambah Produk', 'affos'); ?></span>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>

                <?php
                // Get buy links for each product
                $all_buy_links = array();
                foreach ($url_products as $pid) {
                    $all_buy_links[$pid] = get_post_meta($pid, '_product_buy_links', true);
                }

                // Check if any product has buy links
                $has_any_buy_links = false;
                foreach ($all_buy_links as $links) {
                    if (!empty($links) && is_array($links)) {
                        $has_any_buy_links = true;
                        break;
                    }
                }
                ?>

                <?php if ($has_any_buy_links): ?>
                    <!-- Section: Beli Online -->
                    <div class="compare-spec-section">
                        <h3><?php esc_html_e('Beli Online', 'affos'); ?></h3>
                    </div>
                    <div class="compare-spec-row" style="grid-template-columns: 200px repeat(<?php echo $total_cols; ?>, 1fr);">
                        <div class="spec-label"><?php esc_html_e('Toko Online', 'affos'); ?></div>
                        <?php foreach ($url_products as $pid):
                            $buy_links = isset($all_buy_links[$pid]) ? $all_buy_links[$pid] : array();
                            ?>
                            <div class="spec-value">
                                <?php if (!empty($buy_links) && is_array($buy_links)): ?>
                                    <div class="store-logos-row">
                                        <?php foreach ($buy_links as $link):
                                            if (empty($link['store_url']))
                                                continue;
                                            $store_info = affos_get_store_info($link['store_name']);
                                            ?>
                                            <a href="<?php echo esc_url($link['store_url']); ?>" class="store-logo-btn"
                                                title="<?php echo esc_attr($store_info['name']); ?>" target="_blank"
                                                rel="noopener noreferrer">
                                                <?php if (!empty($store_info['logo_url'])): ?>
                                                    <img src="<?php echo esc_url($store_info['logo_url']); ?>"
                                                        alt="<?php echo esc_attr($store_info['name']); ?>">
                                                <?php else: ?>
                                                    <i class="<?php echo esc_attr($store_info['icon']); ?>"></i>
                                                <?php endif; ?>
                                            </a>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                        <?php if ($show_add_slot): ?>
                            <div class="spec-value empty">-</div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <!-- Section: Harga -->
                <div class="compare-spec-section">
                    <h3><?php esc_html_e('Harga', 'affos'); ?></h3>
                </div>
                <div class="compare-spec-row" style="grid-template-columns: 200px repeat(<?php echo $total_cols; ?>, 1fr);">
                    <div class="spec-label"><?php esc_html_e('Harga', 'affos'); ?></div>
                    <?php
                    $prices = array();
                    foreach ($url_products as $pid) {
                        $price = get_post_meta($pid, '_misc_price', true);
                        $prices[$pid] = $price;
                    }
                    // Find lowest price for highlighting
                    $numeric_prices = array_filter(array_map(function ($p) {
                        return (int) preg_replace('/[^0-9]/', '', $p);
                    }, $prices));
                    $min_price = !empty($numeric_prices) ? min($numeric_prices) : 0;

                    foreach ($url_products as $pid):
                        $price = $prices[$pid];
                        $numeric = (int) preg_replace('/[^0-9]/', '', $price);
                        $is_lowest = ($numeric > 0 && $numeric === $min_price);
                        ?>
                        <div class="spec-value<?php echo $is_lowest ? ' highlight' : ''; ?>">
                            <?php echo esc_html($price ?: '-'); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if ($show_add_slot): ?>
                        <div class="spec-value empty">-</div>
                    <?php endif; ?>
                </div>

                <?php
                // Full GSMArena-style spec sections with all sub-fields
                $spec_sections = array(
                    'network' => array(
                        'title' => __('Network', 'affos'),
                        'fields' => array(
                            '_network_technology' => __('Technology', 'affos'),
                            '_network_2g_bands' => __('2G Bands', 'affos'),
                            '_network_3g_bands' => __('3G Bands', 'affos'),
                            '_network_4g_bands' => __('4G Bands', 'affos'),
                            '_network_5g_bands' => __('5G Bands', 'affos'),
                            '_network_speed' => __('Speed', 'affos'),
                        ),
                    ),
                    'launch' => array(
                        'title' => __('Launch', 'affos'),
                        'fields' => array(
                            '_launch_announced' => __('Announced', 'affos'),
                            '_launch_status' => __('Status', 'affos'),
                        ),
                    ),
                    'body' => array(
                        'title' => __('Body', 'affos'),
                        'fields' => array(
                            '_body_dimensions' => __('Dimensions', 'affos'),
                            '_body_weight' => __('Weight', 'affos'),
                            '_body_sim' => __('SIM', 'affos'),
                            '_body_other' => __('Other', 'affos'),
                        ),
                    ),
                    'display' => array(
                        'title' => __('Display', 'affos'),
                        'fields' => array(
                            '_display_type' => __('Type', 'affos'),
                            '_display_size' => __('Size', 'affos'),
                            '_display_resolution' => __('Resolution', 'affos'),
                            '_display_protection' => __('Protection', 'affos'),
                            '_display_other' => __('Other', 'affos'),
                        ),
                    ),
                    'platform' => array(
                        'title' => __('Platform', 'affos'),
                        'fields' => array(
                            '_platform_os' => __('OS', 'affos'),
                            '_platform_chipset' => __('Chipset', 'affos'),
                            '_platform_cpu' => __('CPU', 'affos'),
                            '_platform_gpu' => __('GPU', 'affos'),
                        ),
                    ),
                    'memory' => array(
                        'title' => __('Memory', 'affos'),
                        'fields' => array(
                            '_memory_card_slot' => __('Card Slot', 'affos'),
                            '_memory_internal' => __('Internal', 'affos'),
                        ),
                    ),
                    'main_camera' => array(
                        'title' => __('Main Camera', 'affos'),
                        'fields' => array(
                            '_camera_main_specs' => __('Specs', 'affos'),
                            '_camera_main_features' => __('Features', 'affos'),
                            '_camera_main_video' => __('Video', 'affos'),
                        ),
                    ),
                    'selfie_camera' => array(
                        'title' => __('Selfie Camera', 'affos'),
                        'fields' => array(
                            '_camera_selfie_specs' => __('Specs', 'affos'),
                            '_camera_selfie_features' => __('Features', 'affos'),
                            '_camera_selfie_video' => __('Video', 'affos'),
                        ),
                    ),
                    'sound' => array(
                        'title' => __('Sound', 'affos'),
                        'fields' => array(
                            '_sound_loudspeaker' => __('Loudspeaker', 'affos'),
                            '_sound_jack' => __('3.5mm Jack', 'affos'),
                        ),
                    ),
                    'comms' => array(
                        'title' => __('Comms', 'affos'),
                        'fields' => array(
                            '_comms_wlan' => __('WLAN', 'affos'),
                            '_comms_bluetooth' => __('Bluetooth', 'affos'),
                            '_comms_nfc' => __('NFC', 'affos'),
                            '_comms_gps' => __('GPS', 'affos'),
                            '_comms_radio' => __('Radio', 'affos'),
                            '_comms_usb' => __('USB', 'affos'),
                        ),
                    ),
                    'features' => array(
                        'title' => __('Features', 'affos'),
                        'fields' => array(
                            '_features_sensors' => __('Sensors', 'affos'),
                            '_features_other' => __('Other', 'affos'),
                        ),
                    ),
                    'battery' => array(
                        'title' => __('Battery', 'affos'),
                        'fields' => array(
                            '_battery_type' => __('Type', 'affos'),
                            '_battery_charging' => __('Charging', 'affos'),
                        ),
                    ),
                    'misc' => array(
                        'title' => __('Misc', 'affos'),
                        'fields' => array(
                            '_misc_colors' => __('Colors', 'affos'),
                            '_misc_models' => __('Models', 'affos'),
                            '_misc_price' => __('Price', 'affos'),
                        ),
                    ),
                );

                foreach ($spec_sections as $section_key => $section):
                    // Check if any product has data in this section
                    $section_has_data = false;
                    foreach ($section['fields'] as $field_key => $field_label) {
                        foreach ($url_products as $pid) {
                            if (get_post_meta($pid, $field_key, true)) {
                                $section_has_data = true;
                                break 2;
                            }
                        }
                    }
                    // Always show all sections for completeness
                    ?>
                    <!-- Section: <?php echo esc_html($section['title']); ?> -->
                    <div class="compare-spec-section">
                        <h3><?php echo esc_html($section['title']); ?></h3>
                    </div>

                    <?php foreach ($section['fields'] as $field_key => $field_label): ?>
                        <div class="compare-spec-row" style="grid-template-columns: 200px repeat(<?php echo $total_cols; ?>, 1fr);">
                            <div class="spec-label"><?php echo esc_html($field_label); ?></div>
                            <?php foreach ($url_products as $pid):
                                $value = get_post_meta($pid, $field_key, true);
                                ?>
                                <div class="spec-value<?php echo empty($value) ? ' empty' : ''; ?>">
                                    <?php echo esc_html($value ?: '-'); ?>
                                </div>
                            <?php endforeach; ?>
                            <?php if ($show_add_slot): ?>
                                <div class="spec-value empty">-</div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            </div>

        <?php endif; ?>
    </div>
</main>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (!$has_url_products): ?>
            // Check localStorage for products and redirect if found
            const compareState = JSON.parse(localStorage.getItem('affos_compare') || '[]');
            if (compareState.length >= 2) {
                // Redirect to SEO-friendly URL
                fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=affos_get_compare_slugs&ids=' + compareState.join(','))
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.url) {
                            window.location.href = data.data.url;
                        }
                    });
            }
        <?php else: ?>
            // Handle remove product button
            document.querySelectorAll('.remove-product-btn').forEach(btn => {
                btn.addEventListener('click', function () {
                    const removeId = this.dataset.removeId;
                    let compareState = JSON.parse(localStorage.getItem('affos_compare') || '[]');
                    compareState = compareState.filter(id => id !== removeId);
                    localStorage.setItem('affos_compare', JSON.stringify(compareState));

                    if (compareState.length < 2) {
                        window.location.href = '<?php echo esc_url(home_url('/bandingkan/')); ?>';
                    } else {
                        // Redirect to new comparison URL
                        fetch('<?php echo esc_url(admin_url('admin-ajax.php')); ?>?action=affos_get_compare_slugs&ids=' + compareState.join(','))
                            .then(response => response.json())
                            .then(data => {
                                if (data.success && data.data.url) {
                                    window.location.href = data.data.url;
                                }
                            });
                    }
                });
            });

            // Handle clear all button
            document.getElementById('clear-compare-all')?.addEventListener('click', function (e) {
                e.preventDefault();
                localStorage.setItem('affos_compare', '[]');
                window.location.href = '<?php echo esc_url(home_url('/bandingkan/')); ?>';
            });
        <?php endif; ?>
    });
</script>

<?php get_footer(); ?>