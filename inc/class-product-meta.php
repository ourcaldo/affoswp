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
        $this->meta_sections = array(
            'network' => array(
                'title' => __('Network', 'affos'),
                'icon' => 'ri-signal-tower-line',
                'fields' => array(
                    '_network_technology' => array('label' => __('Technology', 'affos'), 'type' => 'text'),
                    '_network_2g_bands' => array('label' => __('2G Bands', 'affos'), 'type' => 'text'),
                    '_network_3g_bands' => array('label' => __('3G Bands', 'affos'), 'type' => 'text'),
                    '_network_4g_bands' => array('label' => __('4G Bands', 'affos'), 'type' => 'textarea'),
                    '_network_5g_bands' => array('label' => __('5G Bands', 'affos'), 'type' => 'textarea'),
                    '_network_speed' => array('label' => __('Speed', 'affos'), 'type' => 'text'),
                ),
            ),
            'launch' => array(
                'title' => __('Launch', 'affos'),
                'icon' => 'ri-calendar-line',
                'fields' => array(
                    '_launch_announced' => array('label' => __('Announced', 'affos'), 'type' => 'text'),
                    '_launch_status' => array('label' => __('Status', 'affos'), 'type' => 'text'),
                ),
            ),
            'body' => array(
                'title' => __('Body', 'affos'),
                'icon' => 'ri-smartphone-line',
                'fields' => array(
                    '_body_dimensions' => array('label' => __('Dimensions', 'affos'), 'type' => 'text'),
                    '_body_weight' => array('label' => __('Weight', 'affos'), 'type' => 'text'),
                    '_body_sim' => array('label' => __('SIM', 'affos'), 'type' => 'text'),
                    '_body_other' => array('label' => __('Other', 'affos'), 'type' => 'textarea'),
                ),
            ),
            'display' => array(
                'title' => __('Display', 'affos'),
                'icon' => 'ri-artboard-line',
                'fields' => array(
                    '_display_type' => array('label' => __('Type', 'affos'), 'type' => 'text'),
                    '_display_size' => array('label' => __('Size', 'affos'), 'type' => 'text'),
                    '_display_resolution' => array('label' => __('Resolution', 'affos'), 'type' => 'text'),
                    '_display_protection' => array('label' => __('Protection', 'affos'), 'type' => 'text'),
                    '_display_other' => array('label' => __('Other', 'affos'), 'type' => 'textarea'),
                ),
            ),
            'platform' => array(
                'title' => __('Platform', 'affos'),
                'icon' => 'ri-cpu-line',
                'fields' => array(
                    '_platform_os' => array('label' => __('OS', 'affos'), 'type' => 'text'),
                    '_platform_chipset' => array('label' => __('Chipset', 'affos'), 'type' => 'text'),
                    '_platform_cpu' => array('label' => __('CPU', 'affos'), 'type' => 'text'),
                    '_platform_gpu' => array('label' => __('GPU', 'affos'), 'type' => 'text'),
                ),
            ),
            'memory' => array(
                'title' => __('Memory', 'affos'),
                'icon' => 'ri-hard-drive-line',
                'fields' => array(
                    '_memory_card_slot' => array('label' => __('Card Slot', 'affos'), 'type' => 'text'),
                    '_memory_internal' => array('label' => __('Internal', 'affos'), 'type' => 'text'),
                ),
            ),
            'main_camera' => array(
                'title' => __('Main Camera', 'affos'),
                'icon' => 'ri-camera-lens-line',
                'fields' => array(
                    '_camera_main_specs' => array('label' => __('Specs', 'affos'), 'type' => 'textarea'),
                    '_camera_main_features' => array('label' => __('Features', 'affos'), 'type' => 'text'),
                    '_camera_main_video' => array('label' => __('Video', 'affos'), 'type' => 'text'),
                ),
            ),
            'selfie_camera' => array(
                'title' => __('Selfie Camera', 'affos'),
                'icon' => 'ri-camera-line',
                'fields' => array(
                    '_camera_selfie_specs' => array('label' => __('Specs', 'affos'), 'type' => 'text'),
                    '_camera_selfie_features' => array('label' => __('Features', 'affos'), 'type' => 'text'),
                    '_camera_selfie_video' => array('label' => __('Video', 'affos'), 'type' => 'text'),
                ),
            ),
            'sound' => array(
                'title' => __('Sound', 'affos'),
                'icon' => 'ri-volume-up-line',
                'fields' => array(
                    '_sound_loudspeaker' => array('label' => __('Loudspeaker', 'affos'), 'type' => 'text'),
                    '_sound_jack' => array('label' => __('3.5mm Jack', 'affos'), 'type' => 'text'),
                ),
            ),
            'comms' => array(
                'title' => __('Comms', 'affos'),
                'icon' => 'ri-wifi-line',
                'fields' => array(
                    '_comms_wlan' => array('label' => __('WLAN', 'affos'), 'type' => 'text'),
                    '_comms_bluetooth' => array('label' => __('Bluetooth', 'affos'), 'type' => 'text'),
                    '_comms_positioning' => array('label' => __('Positioning', 'affos'), 'type' => 'text'),
                    '_comms_nfc' => array('label' => __('NFC', 'affos'), 'type' => 'text'),
                    '_comms_radio' => array('label' => __('Radio', 'affos'), 'type' => 'text'),
                    '_comms_usb' => array('label' => __('USB', 'affos'), 'type' => 'text'),
                ),
            ),
            'features' => array(
                'title' => __('Features', 'affos'),
                'icon' => 'ri-settings-3-line',
                'fields' => array(
                    '_features_sensors' => array('label' => __('Sensors', 'affos'), 'type' => 'textarea'),
                    '_features_other' => array('label' => __('Other', 'affos'), 'type' => 'textarea'),
                ),
            ),
            'battery' => array(
                'title' => __('Battery', 'affos'),
                'icon' => 'ri-battery-charge-line',
                'fields' => array(
                    '_battery_type' => array('label' => __('Type', 'affos'), 'type' => 'text'),
                    '_battery_charging' => array('label' => __('Charging', 'affos'), 'type' => 'text'),
                ),
            ),
            'misc' => array(
                'title' => __('Misc', 'affos'),
                'icon' => 'ri-information-line',
                'fields' => array(
                    '_misc_colors' => array('label' => __('Colors', 'affos'), 'type' => 'text'),
                    '_misc_price' => array('label' => __('Price', 'affos'), 'type' => 'text'),
                ),
            ),
        );
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
