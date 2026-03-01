<?php
/**
 * Product Meta Boxes
 *
 * Native WordPress meta boxes for product specifications (GSM Arena style)
 *
 * @package Affos
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_Product_Meta
{

    /**
     * Meta field definitions grouped by section
     */
    private $meta_sections = array();

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->define_meta_fields();
        add_action('add_meta_boxes', array($this, 'add_meta_boxes'));
        add_action('save_post_product', array($this, 'save_meta'), 10, 2);
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    /**
     * Define meta fields by section (GSM Arena style)
     */
    private function define_meta_fields()
    {
        $this->meta_sections = affos_get_product_spec_sections();
    }

    /**
     * Add meta boxes
     */
    public function add_meta_boxes()
    {
        // Specifications meta box
        add_meta_box(
            'affos_product_specs',
            __('Specifications', 'affos'),
            array($this, 'render_specs_meta_box'),
            'product',
            'normal',
            'high'
        );

        // Buy Links meta box
        add_meta_box(
            'affos_product_buy_links',
            __('Buy Links', 'affos'),
            array($this, 'render_buy_links_meta_box'),
            'product',
            'normal',
            'high'
        );
    }

    /**
     * Render specifications meta box
     */
    public function render_specs_meta_box($post)
    {
        wp_nonce_field('affos_product_meta', 'affos_product_meta_nonce');
        ?>
        <div class="affos-meta-tabs">
            <div class="affos-tab-nav">
                <?php
                $first = true;
                foreach ($this->meta_sections as $section_id => $section) {
                    $active = $first ? ' active' : '';
                    printf(
                        '<button type="button" class="affos-tab-btn%s" data-tab="%s"><i class="%s"></i> %s</button>',
                        $active,
                        esc_attr($section_id),
                        esc_attr($section['icon']),
                        esc_html($section['title'])
                    );
                    $first = false;
                }
                ?>
            </div>
            <div class="affos-tab-content">
                <?php
                $first = true;
                foreach ($this->meta_sections as $section_id => $section) {
                    $active = $first ? ' active' : '';
                    ?>
                    <div class="affos-tab-panel<?php echo $active; ?>" data-panel="<?php echo esc_attr($section_id); ?>">
                        <table class="form-table affos-meta-table">
                            <tbody>
                                <?php
                                foreach ($section['fields'] as $field_id => $field) {
                                    $value = get_post_meta($post->ID, $field_id, true);
                                    ?>
                                    <tr>
                                        <th scope="row">
                                            <label for="<?php echo esc_attr($field_id); ?>">
                                                <?php echo esc_html($field['label']); ?>
                                            </label>
                                        </th>
                                        <td>
                                            <?php if ($field['type'] === 'textarea'): ?>
                                                <textarea name="<?php echo esc_attr($field_id); ?>" id="<?php echo esc_attr($field_id); ?>"
                                                    class="large-text" rows="3"><?php echo esc_textarea($value); ?></textarea>
                                            <?php else: ?>
                                                <input type="text" name="<?php echo esc_attr($field_id); ?>"
                                                    id="<?php echo esc_attr($field_id); ?>" class="regular-text"
                                                    value="<?php echo esc_attr($value); ?>" />
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                    <?php
                    $first = false;
                }
                ?>
            </div>
        </div>
        <?php
    }

    /**
     * Render buy links meta box
     */
    public function render_buy_links_meta_box($post)
    {
        wp_nonce_field('affos_product_meta', 'affos_product_meta_nonce');
        $buy_links = get_post_meta($post->ID, '_product_buy_links', true);
        if (!is_array($buy_links)) {
            $buy_links = array();
        }

        // Predefined store options
        $store_options = array(
            'shopee' => 'Shopee',
            'tokopedia' => 'Tokopedia',
            'blibli' => 'Blibli',
            'other' => 'Toko Lainnya',
        );
        ?>
        <div id="affos-buy-links">
            <p class="description" style="margin-bottom: 12px;">
                <?php esc_html_e('Add links to online stores where this product can be purchased.', 'affos'); ?>
            </p>
            <table class="wp-list-table widefat fixed striped" id="buy-links-table">
                <thead>
                    <tr>
                        <th style="width: 20%;">
                            <?php esc_html_e('Store', 'affos'); ?>
                        </th>
                        <th style="width: 45%;">
                            <?php esc_html_e('URL', 'affos'); ?>
                        </th>
                        <th style="width: 25%;">
                            <?php esc_html_e('Price', 'affos'); ?>
                        </th>
                        <th style="width: 10%;">
                            <?php esc_html_e('Actions', 'affos'); ?>
                        </th>
                    </tr>
                </thead>
                <tbody id="buy-links-body">
                    <?php
                    if (!empty($buy_links)) {
                        foreach ($buy_links as $index => $link) {
                            $this->render_buy_link_row($index, $link, $store_options);
                        }
                    }
                    ?>
                </tbody>
            </table>
            <p>
                <button type="button" class="button button-secondary" id="add-buy-link">
                    <span class="dashicons dashicons-plus-alt" style="vertical-align: middle;"></span>
                    <?php esc_html_e('Add Store', 'affos'); ?>
                </button>
            </p>
        </div>
        <script>var affosProductMeta = {rowIndex: <?php echo count($buy_links); ?>};</script>
        <?php
    }

    /**
     * Render single buy link row
     */
    private function render_buy_link_row($index, $link, $store_options)
    {
        $current_store = isset($link['store_name']) ? $link['store_name'] : '';
        ?>
        <tr>
            <td>
                <select name="_product_buy_links[<?php echo esc_attr($index); ?>][store_name]" class="regular-text"
                    style="width: 100%;">
                    <?php foreach ($store_options as $key => $label): ?>
                        <option value="<?php echo esc_attr($key); ?>" <?php selected($current_store, $key); ?>>
                            <?php echo esc_html($label); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
            <td>
                <input type="url" name="_product_buy_links[<?php echo esc_attr($index); ?>][store_url]" class="regular-text"
                    value="<?php echo esc_url($link['store_url'] ?? ''); ?>" placeholder="https://" style="width: 100%;" />
            </td>
            <td>
                <input type="text" name="_product_buy_links[<?php echo esc_attr($index); ?>][store_price]" class="regular-text"
                    value="<?php echo esc_attr($link['store_price'] ?? ''); ?>"
                    placeholder="<?php esc_attr_e('e.g., Rp 24.999.000', 'affos'); ?>" style="width: 100%;" />
            </td>
            <td>
                <button type="button" class="button remove-buy-link">
                    <span class="dashicons dashicons-trash"></span>
                </button>
            </td>
        </tr>
        <?php
    }

    /**
     * Save meta data
     */
    public function save_meta($post_id, $post)
    {
        // Verify nonce
        if (
            !isset($_POST['affos_product_meta_nonce']) ||
            !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['affos_product_meta_nonce'])), 'affos_product_meta')
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

        // Save specification fields
        foreach ($this->meta_sections as $section) {
            foreach ($section['fields'] as $field_id => $field) {
                if (isset($_POST[$field_id])) {
                    $value = sanitize_textarea_field($_POST[$field_id]);
                    update_post_meta($post_id, $field_id, $value);
                }
            }
        }

        // Save buy links
        if (isset($_POST['_product_buy_links']) && is_array($_POST['_product_buy_links'])) {
            $buy_links = array();
            foreach ($_POST['_product_buy_links'] as $link) {
                if (!empty($link['store_name']) || !empty($link['store_url'])) {
                    $buy_links[] = array(
                        'store_name' => sanitize_text_field($link['store_name'] ?? ''),
                        'store_url' => esc_url_raw($link['store_url'] ?? ''),
                        'store_price' => sanitize_text_field($link['store_price'] ?? ''),
                    );
                }
            }
            update_post_meta($post_id, '_product_buy_links', $buy_links);
        } else {
            delete_post_meta($post_id, '_product_buy_links');
        }
    }

    /**
     * Enqueue admin scripts
     */
    public function enqueue_admin_scripts($hook)
    {
        global $post_type;

        if (($hook === 'post.php' || $hook === 'post-new.php') && $post_type === 'product') {
            // Enqueue Remix Icons for admin
            wp_enqueue_style(
                'affos-admin-icons',
                'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css',
                array(),
                '3.5.0'
            );

            // Enqueue product meta box CSS
            wp_enqueue_style(
                'affos-admin-product-meta',
                AFFOS_URI . '/assets/css/admin-product-meta.css',
                array(),
                AFFOS_VERSION
            );

            // Enqueue product meta box JS
            wp_enqueue_script(
                'affos-admin-product-meta',
                AFFOS_URI . '/assets/js/admin-product-meta.js',
                array('jquery'),
                AFFOS_VERSION,
                true
            );

            // Localize static data for the buy links script
            wp_localize_script('affos-admin-product-meta', 'affosProductMetaL10n', array(
                'storeOptions' => array(
                    'shopee' => 'Shopee',
                    'tokopedia' => 'Tokopedia',
                    'blibli' => 'Blibli',
                    'other' => 'Toko Lainnya',
                ),
                'pricePlaceholder' => __('e.g., Rp 24.999.000', 'affos'),
            ));
        }
    }
}

// Initialize
new Affos_Product_Meta();
