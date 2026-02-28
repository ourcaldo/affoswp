<?php
/**
 * Template Part: Review Card
 *
 * @package Affos
 * @since 1.0.0
 */

$review = isset($args['review']) ? $args['review'] : get_post();
if (!$review) {
    return;
}
$review_id = $review->ID;

// Get meta data
$score = get_post_meta($review_id, '_review_score', true);

// Get category
$categories = get_the_terms($review_id, 'review_category');
$category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';
$category_slug = ($categories && !is_wp_error($categories)) ? sanitize_title($categories[0]->name) : '';

// Get author
$author_id = $review->post_author;
$author_name = get_the_author_meta('display_name', $author_id);

// Format date
$date = get_the_date('j M Y', $review_id);

// Is featured
$is_featured = isset($args['featured']) ? $args['featured'] : false;

// Score level class
$score_level = '';
$score_num = (float) $score;
if ($score_num >= 9.0) {
    $score_level = 'score-excellent';
} elseif ($score_num >= 7.0) {
    $score_level = 'score-good';
} elseif ($score_num >= 5.0) {
    $score_level = 'score-average';
} else {
    $score_level = 'score-poor';
}

// Map category to icon
$icon_class = 'ri-smartphone-line';
if ($category_slug === 'laptop') {
    $icon_class = 'ri-macbook-line';
} elseif ($category_slug === 'tablet') {
    $icon_class = 'ri-tablet-line';
} elseif ($category_slug === 'audio') {
    $icon_class = 'ri-headphone-line';
}
?>

<article class="review-card <?php echo $is_featured ? 'featured' : ''; ?>"
    data-category="<?php echo esc_attr($category_slug); ?>">
    <div class="review-card-img cat-<?php echo esc_attr($category_slug); ?>">
        <?php if ($score): ?>
            <span class="score-badge <?php echo esc_attr($score_level); ?>">
                <?php echo esc_html(number_format($score_num, 1)); ?>
            </span>
        <?php endif; ?>
        <?php if (has_post_thumbnail($review_id)): ?>
            <?php echo get_the_post_thumbnail($review_id, 'medium_large', array('class' => 'review-cover')); ?>
        <?php else: ?>
            <i class="<?php echo esc_attr($icon_class); ?>" aria-hidden="true"></i>
        <?php endif; ?>
    </div>
    <div class="review-card-body">
        <?php if ($category_name): ?>
            <p class="category-label"><?php echo esc_html($category_name); ?></p>
        <?php endif; ?>
        <h3><a href="<?php echo esc_url(get_permalink($review_id)); ?>"><?php echo esc_html($review->post_title); ?></a></h3>
        <?php if ($is_featured && $review->post_excerpt): ?>
            <p class="excerpt"><?php echo esc_html(wp_trim_words($review->post_excerpt, 20)); ?></p>
        <?php endif; ?>
        <div class="meta-row">
            <div class="author-avatar">
                <?php echo get_avatar($author_id, 32); ?>
            </div>
            <span class="author-name"><?php echo esc_html($author_name); ?></span>
            <span><?php echo esc_html($date); ?></span>
        </div>
    </div>
</article>
