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

// Map category to icon
$icon_class = 'ri-smartphone-line';
if ($category_slug === 'laptop') {
    $icon_class = 'ri-laptop-line';
} elseif ($category_slug === 'tablet') {
    $icon_class = 'ri-tablet-line';
} elseif ($category_slug === 'audio') {
    $icon_class = 'ri-headphone-line';
}
?>

<article class="product-card" data-category="<?php echo esc_attr($category_slug); ?>"
    data-product-id="<?php echo esc_attr($product_id); ?>">
    <a href="<?php echo esc_url(get_permalink($product_id)); ?>">
        <div class="product-card-img cat-<?php echo esc_attr($category_slug); ?>">
            <?php if (has_post_thumbnail($product_id)): ?>
                <?php echo get_the_post_thumbnail($product_id, 'medium', array('class' => 'card-img')); ?>
            <?php else: ?>
                <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
    </a>
    <div class="product-card-body">
        <?php if ($category_name): ?>
            <span class="overline"><?php echo esc_html($category_name); ?></span>
        <?php endif; ?>
        <h3><a href="<?php echo esc_url(get_permalink($product_id)); ?>"><?php echo esc_html($product->post_title); ?></a></h3>
        <div class="specs">
            <?php if ($chipset): ?>
                <span class="spec-tag"><?php echo esc_html($chipset); ?></span>
            <?php endif; ?>
            <?php if ($camera_display): ?>
                <span class="spec-tag"><?php echo esc_html($camera_display); ?></span>
            <?php endif; ?>
        </div>
        <div class="card-footer">
            <span class="price"><?php echo esc_html($price ?: __('Lihat Harga', 'affos')); ?></span>
            <button class="card-action add-to-compare" aria-label="<?php esc_attr_e('Bandingkan', 'affos'); ?>"
                data-compare-id="<?php echo esc_attr($product_id); ?>">
                <i class="ri-scales-3-line" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</article>
