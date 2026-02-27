<?php
/**
 * Template Part: Review Card
 *
 * @package Affos
 * @since 1.0.0
 */

$review = isset($args['review']) ? $args['review'] : get_post();
$review_id = $review->ID;

// Get meta data
$score = get_post_meta($review_id, '_review_score', true);
$verdict = get_post_meta($review_id, '_review_verdict', true);
$product_id = get_post_meta($review_id, '_review_product_id', true);

// Get category
$categories = get_the_terms($review_id, 'review_category');
$category_name = ($categories && !is_wp_error($categories)) ? $categories[0]->name : '';

// Get author
$author_id = $review->post_author;
$author_name = get_the_author_meta('display_name', $author_id);

// Format date
$date = get_the_date('j M Y', $review_id);

// Is featured (first review or has high score)
$is_featured = isset($args['featured']) ? $args['featured'] : false;
?>

<article class="review-card <?php echo $is_featured ? 'featured' : ''; ?>"
    data-category="<?php echo esc_attr(sanitize_title($category_name)); ?>">
    <a href="<?php echo esc_url(get_permalink($review_id)); ?>" class="review-link">
        <div class="review-img">
            <?php if ($verdict === 'Excellent'): ?>
                <span class="review-badge">
                    <?php esc_html_e("Editor's Choice", 'affos'); ?>
                </span>
            <?php endif; ?>
            <?php if ($score): ?>
                <div class="review-score">
                    <?php echo esc_html(number_format((float) $score, 1)); ?>
                </div>
            <?php endif; ?>
            <?php if (has_post_thumbnail($review_id)): ?>
                <?php echo get_the_post_thumbnail($review_id, 'medium_large', array('class' => 'review-cover')); ?>
            <?php else: ?>
                <div class="review-img-placeholder">
                </div>
            <?php endif; ?>
        </div>
        <div class="review-content">
            <div class="review-meta">
                <?php if ($category_name): ?>
                    <span class="review-category">
                        <?php echo esc_html($category_name); ?>
                    </span>
                <?php endif; ?>
                <span class="review-date">
                    <?php echo esc_html($date); ?>
                </span>
            </div>
            <h2 class="review-title">
                <?php echo esc_html($review->post_title); ?>
            </h2>
            <?php if ($is_featured && $review->post_excerpt): ?>
                <p class="review-excerpt">
                    <?php echo esc_html(wp_trim_words($review->post_excerpt, 20)); ?>
                </p>
            <?php endif; ?>
            <div class="review-author">
                <div class="author-avatar">
                    <?php echo get_avatar($author_id, 32); ?>
                </div>
                <div class="author-info">
                    <span class="author-name">
                        <?php echo esc_html($author_name); ?>
                    </span>
                </div>
            </div>
        </div>
    </a>
</article>