<?php
/**
 * Template Part: Product Card
 *
 * @package Affos
 * @since 1.0.0
 */

$product = isset($args['product']) ? $args['product'] : get_post();
$product_id = $product->ID;

// Get meta data
$price = get_post_meta($product_id, '_misc_price', true);
$chipset = get_post_meta($product_id, '_platform_chipset', true);
$camera = get_post_meta($product_id, '_camera_main_specs', true);
$status = get_post_meta($product_id, '_launch_status', true);

// Get category
$categories = get_the_terms($product_id, 'product_category');
$category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';
$category_slug = ($categories && !is_wp_error($categories)) ? $categories[0]->slug : '';

// Extract camera MP for display
$camera_display = '';
if ($camera) {
    preg_match('/(\d+)\s*MP/i', $camera, $matches);
    $camera_display = isset($matches[0]) ? $matches[0] : $camera;
}

// Determine badge text
$badge_text = '';
if (strpos(strtolower($status), 'available') !== false) {
    $badge_text = __('Tersedia', 'affos');
} elseif (strpos(strtolower($status), 'coming') !== false) {
    $badge_text = __('Segera', 'affos');
}
?>

<article class="product-card" data-category="<?php echo esc_attr($category_slug); ?>"
    data-product-id="<?php echo esc_attr($product_id); ?>">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>" class="card-link">
        <div class="card-img-wrapper">
            <?php if ($badge_text): ?>
                <span class="card-badge">
                    <?php echo esc_html($badge_text); ?>
                </span>
            <?php endif; ?>
            <?php if (has_post_thumbnail($product_id)): ?>
                <?php echo get_the_post_thumbnail($product_id, 'medium', array('class' => 'card-img')); ?>
            <?php else: ?>
                <div class="card-img-placeholder">
                    <i class="ri-smartphone-line"></i>
                </div>
            <?php endif; ?>
        </div>
        <div class="card-content">
            <?php if ($category_name): ?>
                <div class="card-cat">
                    <?php echo esc_html($category_name); ?>
                </div>
            <?php endif; ?>
            <h3 class="card-title">
                <?php echo esc_html($product->post_title); ?>
            </h3>
            <div class="card-specs">
                <?php if ($chipset): ?>
                    <div class="spec-item"><i class="ri-cpu-line"></i>
                        <?php echo esc_html($chipset); ?>
                    </div>
                <?php endif; ?>
                <?php if ($camera_display): ?>
                    <div class="spec-item"><i class="ri-camera-lens-line"></i>
                        <?php echo esc_html($camera_display); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-price-row">
                <span class="price">
                    <?php echo esc_html($price ?: __('Lihat Harga', 'affos')); ?>
                </span>
                <span class="card-arrow"><i class="ri-arrow-right-line"></i></span>
            </div>
        </div>
    </a>
    <div class="card-actions">
        <button class="action-btn add-to-compare" title="<?php esc_attr_e('Bandingkan', 'affos'); ?>"
            data-compare-id="<?php echo esc_attr($product_id); ?>">
            <i class="ri-scales-3-line"></i>
        </button>
        <button class="action-btn" title="<?php esc_attr_e('Simpan', 'affos'); ?>">
            <i class="ri-heart-line"></i>
        </button>
    </div>
</article>