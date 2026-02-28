<?php
/**
 * Review Meta Boxes
 *
 * Native WordPress meta boxes for review fields
 *
 * @package Affos
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_Review_Meta
{

    /**
     * Rating categories
     */
    private $rating_categories = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rating_categories = array(
            '_review_rating_design' => __('Desain', 'affos'),
            '_review_rating_display' => __('Layar', 'affos'),
            '_review_rating_performance' => __('Performa', 'affos'),
            '_review_rating_camera' => __('Kamera', 'affos'),
            '_review_rating_battery' => __('Baterai', 'affos'),
            '_review_rating_value' => __('Nilai', 'affos'),
        );

        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_review', array($this, 'save_meta'), 10, 2);
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes()
    {
        // Review Score & Verdict
        add_meta_box(
            'affos_review_score',
            __('Review Score', 'affos'),
            array($this, 'render_score_meta_box'),
            'review',
            'side',
            'high'
        );

        // Related Product
        add_meta_box(
            'affos_review_product',
            __('Reviewed Product', 'affos'),
            array($this, 'render_product_meta_box'),
            'review',
            'side',
            'high'
        );

        // Rating Categories
        add_meta_box(
            'affos_review_ratings',
            __('Detailed Ratings', 'affos'),
            array($this, 'render_ratings_meta_box'),
            'review',
            'normal',
            'high'
        );

        // Pros & Cons
        add_meta_box(
            'affos_review_verdict',
            __('Pros & Cons', 'affos'),
            array($this, 'render_verdict_meta_box'),
            'review',
            'normal',
            'high'
        );
    }

    /**
     * Render score meta box
     */
    public function render_score_meta_box($post)
    {
        wp_nonce_field('affos_review_meta', 'affos_review_meta_nonce');

        $score = get_post_meta($post->ID, '_review_score', true);
        $verdict = get_post_meta($post->ID, '_review_verdict', true);
        ?>
        <div class="affos-review-score-box">
            <p>
                <label for="_review_score" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <?php esc_html_e('Overall Score (0-10)', 'affos'); ?>
                </label>
                <input type="number" name="_review_score" id="_review_score" value="<?php echo esc_attr($score); ?>" min="0"
                    max="10" step="0.1" style="width: 100%; font-size: 24px; text-align: center; padding: 10px;" />
            </p>
            <p>
                <label for="_review_verdict" style="display: block; margin-bottom: 5px; font-weight: 600;">
                    <?php esc_html_e('Verdict', 'affos'); ?>
                </label>
                <select name="_review_verdict" id="_review_verdict" style="width: 100%;">
                    <option value="">
                        <?php esc_html_e('Select Verdict', 'affos'); ?>
                    </option>
                    <option value="Excellent" <?php selected($verdict, 'Excellent'); ?>>
                        <?php esc_html_e('Excellent', 'affos'); ?>
                    </option>
                    <option value="Great" <?php selected($verdict, 'Great'); ?>>
                        <?php esc_html_e('Great', 'affos'); ?>
                    </option>
                    <option value="Good" <?php selected($verdict, 'Good'); ?>>
                        <?php esc_html_e('Good', 'affos'); ?>
                    </option>
                    <option value="Average" <?php selected($verdict, 'Average'); ?>>
                        <?php esc_html_e('Average', 'affos'); ?>
                    </option>
                    <option value="Poor" <?php selected($verdict, 'Poor'); ?>>
                        <?php esc_html_e('Poor', 'affos'); ?>
                    </option>
                </select>
            </p>
        </div>
        <?php
    }

    /**
     * Render product meta box
     */
    public function render_product_meta_box($post)
    {
        $product_id = get_post_meta($post->ID, '_review_product_id', true);

        // Get all products
        $products = get_posts(array(
            'post_type' => 'product',
            'posts_per_page' => -1,
            'orderby' => 'title',
            'order' => 'ASC',
            'post_status' => 'publish',
        ));
        ?>
        <p>
            <label for="_review_product_id" style="display: block; margin-bottom: 5px; font-weight: 600;">
                <?php esc_html_e('Select Product', 'affos'); ?>
            </label>
            <select name="_review_product_id" id="_review_product_id" style="width: 100%;">
                <option value="">
                    <?php esc_html_e('— Select Product —', 'affos'); ?>
                </option>
                <?php foreach ($products as $product): ?>
                    <option value="<?php echo esc_attr($product->ID); ?>" <?php selected($product_id, $product->ID); ?>>
                        <?php echo esc_html($product->post_title); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </p>
        <?php if ($product_id): ?>
            <p>
                <a href="<?php echo esc_url(get_edit_post_link($product_id)); ?>" target="_blank" class="button button-small">
                    <?php esc_html_e('Edit Product', 'affos'); ?>
                </a>
                <a href="<?php echo esc_url(get_permalink($product_id)); ?>" target="_blank" class="button button-small">
                    <?php esc_html_e('View Product', 'affos'); ?>
                </a>
            </p>
        <?php endif; ?>
    <?php
    }

    /**
     * Render ratings meta box
     */
    public function render_ratings_meta_box($post)
    {
        ?>
        <div class="affos-ratings-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
            <?php foreach ($this->rating_categories as $field_id => $label):
                $value = get_post_meta($post->ID, $field_id, true);
                ?>
                <div class="rating-item">
                    <label for="<?php echo esc_attr($field_id); ?>" style="display: block; margin-bottom: 5px; font-weight: 600;">
                        <?php echo esc_html($label); ?>
                    </label>
                    <div style="display: flex; align-items: center; gap: 10px;">
                        <input type="range" name="<?php echo esc_attr($field_id); ?>" id="<?php echo esc_attr($field_id); ?>"
                            value="<?php echo esc_attr($value ?: '0'); ?>" min="0" max="10" step="0.1" style="flex: 1;"
                            oninput="this.nextElementSibling.textContent = this.value" />
                        <span style="min-width: 30px; text-align: center; font-weight: 600; font-size: 16px;">
                            <?php echo esc_html($value ?: '0'); ?>
                        </span>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
        <?php
    }

    /**
     * Render verdict meta box (Pros & Cons)
     */
    public function render_verdict_meta_box($post)
    {
        $pros = get_post_meta($post->ID, '_review_pros', true);
        $cons = get_post_meta($post->ID, '_review_cons', true);
        ?>
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <div class="pros-section">
                <label for="_review_pros" style="display: block; margin-bottom: 8px; font-weight: 600; color: #16a34a;">
                    <span class="dashicons dashicons-thumbs-up" style="color: #16a34a;"></span>
                    <?php esc_html_e('Pros (one per line)', 'affos'); ?>
                </label>
                <textarea name="_review_pros" id="_review_pros" rows="6" class="large-text"
                    placeholder="<?php esc_attr_e('Great battery life&#10;Excellent camera&#10;Premium build quality', 'affos'); ?>"
                    style="border-color: #86efac;"><?php echo esc_textarea($pros); ?></textarea>
            </div>
            <div class="cons-section">
                <label for="_review_cons" style="display: block; margin-bottom: 8px; font-weight: 600; color: #dc2626;">
                    <span class="dashicons dashicons-thumbs-down" style="color: #dc2626;"></span>
                    <?php esc_html_e('Cons (one per line)', 'affos'); ?>
                </label>
                <textarea name="_review_cons" id="_review_cons" rows="6" class="large-text"
                    placeholder="<?php esc_attr_e('Expensive&#10;No headphone jack&#10;Slow charging', 'affos'); ?>"
                    style="border-color: #fca5a5;"><?php echo esc_textarea($cons); ?></textarea>
            </div>
        </div>
        <?php
    }

    /**
     * Save meta data
     */
    public function save_meta($post_id, $post)
    {
        // Verify nonce
        if (
            !isset($_POST['affos_review_meta_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['affos_review_meta_nonce'])), 'affos_review_meta')
        ) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check post revision
        if (wp_is_post_revision($post_id)) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save score
        if (isset($_POST['_review_score'])) {
            $score = floatval($_POST['_review_score']);
            $score = max(0, min(10, $score)); // Clamp between 0-10
            update_post_meta($post_id, '_review_score', $score);
        }

        // Save verdict
        if (isset($_POST['_review_verdict'])) {
            update_post_meta($post_id, '_review_verdict', sanitize_text_field($_POST['_review_verdict']));
        }

        // Save product ID
        if (isset($_POST['_review_product_id'])) {
            update_post_meta($post_id, '_review_product_id', absint($_POST['_review_product_id']));
        }

        // Save rating categories
        foreach ($this->rating_categories as $field_id => $label) {
            if (isset($_POST[$field_id])) {
                $rating = floatval($_POST[$field_id]);
                $rating = max(0, min(10, $rating)); // Clamp between 0-10
                update_post_meta($post_id, $field_id, $rating);
            }
        }

        // Save pros
        if (isset($_POST['_review_pros'])) {
            update_post_meta($post_id, '_review_pros', sanitize_textarea_field($_POST['_review_pros']));
        }

        // Save cons
        if (isset($_POST['_review_cons'])) {
            update_post_meta($post_id, '_review_cons', sanitize_textarea_field($_POST['_review_cons']));
        }
    }
}

// Initialize
new Affos_Review_Meta();
