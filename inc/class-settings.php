<?php
/**
 * Affos Theme Settings
 *
 * Admin settings panel with sidebar navigation.
 * Uses TugasinWP styling as reference.
 *
 * @package Affos
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_Settings
{

    /**
     * Settings options key
     */
    private $option_key = 'affos_settings';

    /**
     * Default settings
     */
    private $defaults = array(
        // Branding
        'primary_color' => '#2563EB',
        'secondary_color' => '#7C3AED',
        'logo_id' => 0,
        'logo_height' => 40,
        'favicon_id' => 0,

        // Pages
        'page_compare' => 0,
        'page_contact' => 0,
        'page_privacy' => 0,
        'page_terms' => 0,

        // Archive SEO
        'seo_products_enabled' => false,
        'seo_products_title' => '',
        'seo_products_desc' => '',
        'seo_reviews_enabled' => false,
        'seo_reviews_title' => '',
        'seo_reviews_desc' => '',

        // Optimization
        'lazy_load_enabled' => true,
        'fallback_alt_enabled' => false,
        'fallback_alt_text' => 'Image',
    );

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_menu_page'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('wp_ajax_affos_save_settings', array($this, 'ajax_save_settings'));
    }

    /**
     * Add menu page
     */
    public function add_menu_page()
    {
        add_menu_page(
            __('Affos Settings', 'affos'),
            __('Affos Settings', 'affos'),
            'manage_options',
            'affos-settings',
            array($this, 'render_page'),
            'dashicons-admin-generic',
            3
        );
    }

    /**
     * Enqueue admin assets
     */
    public function enqueue_assets($hook)
    {
        if ($hook !== 'toplevel_page_affos-settings') {
            return;
        }

        // Admin CSS
        wp_enqueue_style(
            'affos-admin-settings',
            AFFOS_URI . '/assets/css/admin-settings.css',
            array(),
            AFFOS_VERSION
        );

        // Media uploader
        wp_enqueue_media();

        // Color picker
        wp_enqueue_style('wp-color-picker');
        wp_enqueue_script('wp-color-picker');

        // Admin JS
        wp_enqueue_script(
            'affos-admin-settings',
            AFFOS_URI . '/assets/js/admin-settings.js',
            array('jquery', 'wp-color-picker'),
            AFFOS_VERSION,
            true
        );

        wp_localize_script('affos-admin-settings', 'affosAdmin', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('affos_settings_nonce'),
        ));
    }

    /**
     * Get settings
     */
    public function get_settings()
    {
        $saved = get_option($this->option_key, array());
        return wp_parse_args($saved, $this->defaults);
    }

    /**
     * AJAX save settings
     */
    public function ajax_save_settings()
    {
        check_ajax_referer('affos_settings_nonce', 'nonce');

        if (!current_user_can('manage_options')) {
            wp_send_json_error('Unauthorized');
        }

        $settings = array();

        // Branding
        $settings['primary_color'] = sanitize_hex_color($_POST['primary_color'] ?? '#2563EB') ?: '#2563EB';
        $settings['secondary_color'] = sanitize_hex_color($_POST['secondary_color'] ?? '#7C3AED') ?: '#7C3AED';
        $settings['logo_id'] = absint($_POST['logo_id'] ?? 0);
        $settings['logo_height'] = absint($_POST['logo_height'] ?? 40);
        $settings['favicon_id'] = absint($_POST['favicon_id'] ?? 0);

        // Pages
        $settings['page_compare'] = absint($_POST['page_compare'] ?? 0);
        $settings['page_contact'] = absint($_POST['page_contact'] ?? 0);
        $settings['page_privacy'] = absint($_POST['page_privacy'] ?? 0);
        $settings['page_terms'] = absint($_POST['page_terms'] ?? 0);

        // Archive SEO
        $settings['seo_products_enabled'] = isset($_POST['seo_products_enabled']);
        $settings['seo_products_title'] = sanitize_text_field($_POST['seo_products_title'] ?? '');
        $settings['seo_products_desc'] = sanitize_textarea_field($_POST['seo_products_desc'] ?? '');
        $settings['seo_reviews_enabled'] = isset($_POST['seo_reviews_enabled']);
        $settings['seo_reviews_title'] = sanitize_text_field($_POST['seo_reviews_title'] ?? '');
        $settings['seo_reviews_desc'] = sanitize_textarea_field($_POST['seo_reviews_desc'] ?? '');

        // Optimization
        $settings['lazy_load_enabled'] = isset($_POST['lazy_load_enabled']);
        $settings['fallback_alt_enabled'] = isset($_POST['fallback_alt_enabled']);
        $settings['fallback_alt_text'] = sanitize_text_field($_POST['fallback_alt_text'] ?? 'Image');

        update_option($this->option_key, $settings);

        wp_send_json_success('Settings saved');
    }

    /**
     * Render settings page
     */
    public function render_page()
    {
        $settings = $this->get_settings();
        $pages = get_pages(array('post_status' => 'publish'));
        ?>
        <div class="affos-wrap">
            <!-- Header -->
            <div class="affos-header">
                <div class="affos-header-content">
                    <div class="affos-header-icon">A</div>
                    <div class="affos-header-text">
                        <h1><?php esc_html_e('Affos Settings', 'affos'); ?></h1>
                        <p><?php esc_html_e('Configure your theme settings and customize your site.', 'affos'); ?></p>
                    </div>
                </div>
                <span class="affos-version">v<?php echo esc_html(AFFOS_VERSION); ?></span>
            </div>

            <div class="affos-body">
                <!-- Sidebar -->
                <nav class="affos-sidebar">
                    <div class="affos-nav-group">
                        <div class="affos-nav-label"><?php esc_html_e('SETTINGS', 'affos'); ?></div>
                        <a href="#" class="affos-nav-item active" data-tab="branding">
                            <span class="dashicons dashicons-art"></span>
                            <?php esc_html_e('Branding', 'affos'); ?>
                        </a>
                        <a href="#" class="affos-nav-item" data-tab="pages">
                            <span class="dashicons dashicons-admin-page"></span>
                            <?php esc_html_e('Pages', 'affos'); ?>
                        </a>
                    </div>

                    <div class="affos-nav-group">
                        <div class="affos-nav-label"><?php esc_html_e('SEO', 'affos'); ?></div>
                        <a href="#" class="affos-nav-item" data-tab="seo">
                            <span class="dashicons dashicons-search"></span>
                            <?php esc_html_e('Archive SEO', 'affos'); ?>
                        </a>
                    </div>

                    <div class="affos-nav-group">
                        <div class="affos-nav-label"><?php esc_html_e('PERFORMANCE', 'affos'); ?></div>
                        <a href="#" class="affos-nav-item" data-tab="optimization">
                            <span class="dashicons dashicons-performance"></span>
                            <?php esc_html_e('Optimization', 'affos'); ?>
                        </a>
                    </div>
                </nav>

                <!-- Content -->
                <div class="affos-content">
                    <form id="affos-settings-form">

                        <!-- Branding Tab -->
                        <div class="affos-tab active" id="tab-branding">
                            <div class="affos-tab-header">
                                <h2><span class="dashicons dashicons-art"></span> <?php esc_html_e('Branding', 'affos'); ?></h2>
                                <p><?php esc_html_e('Customize your site logo, icon, and brand colors.', 'affos'); ?></p>
                            </div>

                            <!-- Logo & Icons -->
                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-format-image"></span>
                                    <?php esc_html_e('Logo & Icons', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field">
                                        <label><?php esc_html_e('Site Logo', 'affos'); ?></label>
                                        <div class="affos-media-field">
                                            <div class="affos-media-preview" id="logo-preview">
                                                <?php if ($settings['logo_id']): ?>
                                                    <?php echo wp_get_attachment_image($settings['logo_id'], 'medium'); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="affos-media-actions">
                                                <button type="button" class="button affos-upload-btn"
                                                    data-target="logo_id"><?php esc_html_e('Upload Logo', 'affos'); ?></button>
                                                <button type="button" class="button affos-remove-btn" data-target="logo_id"
                                                    <?php echo !$settings['logo_id'] ? 'style="display:none"' : ''; ?>><?php esc_html_e('Remove', 'affos'); ?></button>
                                            </div>
                                            <input type="hidden" name="logo_id" id="logo_id"
                                                value="<?php echo esc_attr($settings['logo_id']); ?>">
                                        </div>
                                        <p class="affos-field-desc">
                                            <?php esc_html_e('Upload your site logo. If empty, site title text will be displayed.', 'affos'); ?>
                                        </p>
                                    </div>

                                    <div class="affos-field">
                                        <label><?php esc_html_e('Logo Height (px)', 'affos'); ?></label>
                                        <input type="number" name="logo_height"
                                            value="<?php echo esc_attr($settings['logo_height']); ?>" min="20" max="120"
                                            class="small-text">
                                    </div>

                                    <div class="affos-field">
                                        <label><?php esc_html_e('Site Icon (Favicon)', 'affos'); ?></label>
                                        <div class="affos-media-field">
                                            <div class="affos-media-preview affos-media-small" id="favicon-preview">
                                                <?php if ($settings['favicon_id']): ?>
                                                    <?php echo wp_get_attachment_image($settings['favicon_id'], 'thumbnail'); ?>
                                                <?php endif; ?>
                                            </div>
                                            <div class="affos-media-actions">
                                                <button type="button" class="button affos-upload-btn"
                                                    data-target="favicon_id"><?php esc_html_e('Upload Icon', 'affos'); ?></button>
                                                <button type="button" class="button affos-remove-btn" data-target="favicon_id"
                                                    <?php echo !$settings['favicon_id'] ? 'style="display:none"' : ''; ?>><?php esc_html_e('Remove', 'affos'); ?></button>
                                            </div>
                                            <input type="hidden" name="favicon_id" id="favicon_id"
                                                value="<?php echo esc_attr($settings['favicon_id']); ?>">
                                        </div>
                                        <p class="affos-field-desc">
                                            <?php esc_html_e('Upload your site icon. Recommended size: 512×512 pixels.', 'affos'); ?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Colors -->
                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-admin-appearance"></span>
                                    <?php esc_html_e('Colors', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field affos-field-inline">
                                        <label><?php esc_html_e('Primary Color', 'affos'); ?></label>
                                        <input type="text" name="primary_color"
                                            value="<?php echo esc_attr($settings['primary_color']); ?>"
                                            class="affos-color-picker">
                                    </div>

                                    <div class="affos-field affos-field-inline">
                                        <label><?php esc_html_e('Secondary Color', 'affos'); ?></label>
                                        <input type="text" name="secondary_color"
                                            value="<?php echo esc_attr($settings['secondary_color']); ?>"
                                            class="affos-color-picker">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pages Tab -->
                        <div class="affos-tab" id="tab-pages">
                            <div class="affos-tab-header">
                                <h2><span class="dashicons dashicons-admin-page"></span> <?php esc_html_e('Pages', 'affos'); ?>
                                </h2>
                                <p><?php esc_html_e('Assign pages to different sections of your site.', 'affos'); ?></p>
                            </div>

                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-admin-home"></span>
                                    <?php esc_html_e('Homepage & Blog', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <p class="affos-notice">
                                        <?php esc_html_e('Homepage and Blog page settings are managed in WordPress Settings → Reading.', 'affos'); ?>
                                    </p>
                                    <a href="<?php echo esc_url(admin_url('options-reading.php')); ?>"
                                        class="button"><?php esc_html_e('Go to Reading Settings', 'affos'); ?></a>
                                </div>
                            </div>

                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-admin-links"></span>
                                    <?php esc_html_e('Page Assignments', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Contact Page', 'affos'); ?></label>
                                        <select name="page_contact">
                                            <option value="0"><?php esc_html_e('— Select —', 'affos'); ?></option>
                                            <?php foreach ($pages as $page): ?>
                                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($settings['page_contact'], $page->ID); ?>>
                                                    <?php echo esc_html($page->post_title); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Privacy Policy', 'affos'); ?></label>
                                        <select name="page_privacy">
                                            <option value="0"><?php esc_html_e('— Select —', 'affos'); ?></option>
                                            <?php foreach ($pages as $page): ?>
                                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($settings['page_privacy'], $page->ID); ?>>
                                                    <?php echo esc_html($page->post_title); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Terms of Service', 'affos'); ?></label>
                                        <select name="page_terms">
                                            <option value="0"><?php esc_html_e('— Select —', 'affos'); ?></option>
                                            <?php foreach ($pages as $page): ?>
                                                <option value="<?php echo esc_attr($page->ID); ?>" <?php selected($settings['page_terms'], $page->ID); ?>>
                                                    <?php echo esc_html($page->post_title); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-portfolio"></span>
                                    <?php esc_html_e('Archive Pages', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <p class="affos-notice affos-notice-info">
                                        <?php esc_html_e('Archive pages are automatically generated by WordPress.', 'affos'); ?>
                                    </p>
                                    <div class="affos-readonly-list">
                                        <div class="affos-readonly-item">
                                            <span><?php esc_html_e('Products Archive', 'affos'); ?></span>
                                            <code>/produk/</code>
                                        </div>
                                        <div class="affos-readonly-item">
                                            <span><?php esc_html_e('Reviews Archive', 'affos'); ?></span>
                                            <code>/ulasan/</code>
                                        </div>
                                        <div class="affos-readonly-item">
                                            <span><?php esc_html_e('Compare Page', 'affos'); ?></span>
                                            <code>/bandingkan/</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Tab -->
                        <div class="affos-tab" id="tab-seo">
                            <div class="affos-tab-header">
                                <h2><span class="dashicons dashicons-search"></span>
                                    <?php esc_html_e('Archive SEO', 'affos'); ?></h2>
                                <p><?php esc_html_e('Configure SEO meta tags for archive pages.', 'affos'); ?></p>
                            </div>

                            <!-- Products Archive SEO -->
                            <div class="affos-card affos-card-collapsible">
                                <div class="affos-card-title affos-card-toggle">
                                    <span class="dashicons dashicons-smartphone"></span>
                                    <?php esc_html_e('Products Archive', 'affos'); ?>
                                    <span class="dashicons dashicons-arrow-down-alt2 affos-toggle-icon"></span>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field">
                                        <label class="affos-toggle-label">
                                            <input type="checkbox" name="seo_products_enabled" <?php checked($settings['seo_products_enabled']); ?>>
                                            <?php esc_html_e('Enable SEO meta', 'affos'); ?>
                                        </label>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Title', 'affos'); ?></label>
                                        <input type="text" name="seo_products_title"
                                            value="<?php echo esc_attr($settings['seo_products_title']); ?>" maxlength="60"
                                            placeholder="<?php esc_attr_e('Products - Site Title', 'affos'); ?>">
                                        <span class="affos-char-count">0/60</span>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Meta Description', 'affos'); ?></label>
                                        <textarea name="seo_products_desc" maxlength="160" rows="3"
                                            placeholder="<?php esc_attr_e('Browse our collection of products...', 'affos'); ?>"><?php echo esc_textarea($settings['seo_products_desc']); ?></textarea>
                                        <span class="affos-char-count">0/160</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Reviews Archive SEO -->
                            <div class="affos-card affos-card-collapsible">
                                <div class="affos-card-title affos-card-toggle">
                                    <span class="dashicons dashicons-star-filled"></span>
                                    <?php esc_html_e('Reviews Archive', 'affos'); ?>
                                    <span class="dashicons dashicons-arrow-down-alt2 affos-toggle-icon"></span>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field">
                                        <label class="affos-toggle-label">
                                            <input type="checkbox" name="seo_reviews_enabled" <?php checked($settings['seo_reviews_enabled']); ?>>
                                            <?php esc_html_e('Enable SEO meta', 'affos'); ?>
                                        </label>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Title', 'affos'); ?></label>
                                        <input type="text" name="seo_reviews_title"
                                            value="<?php echo esc_attr($settings['seo_reviews_title']); ?>" maxlength="60"
                                            placeholder="<?php esc_attr_e('Reviews - Site Title', 'affos'); ?>">
                                        <span class="affos-char-count">0/60</span>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Meta Description', 'affos'); ?></label>
                                        <textarea name="seo_reviews_desc" maxlength="160" rows="3"
                                            placeholder="<?php esc_attr_e('Read our in-depth product reviews...', 'affos'); ?>"><?php echo esc_textarea($settings['seo_reviews_desc']); ?></textarea>
                                        <span class="affos-char-count">0/160</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Optimization Tab -->
                        <div class="affos-tab" id="tab-optimization">
                            <div class="affos-tab-header">
                                <h2><span class="dashicons dashicons-performance"></span>
                                    <?php esc_html_e('Optimization', 'affos'); ?></h2>
                                <p><?php esc_html_e('Performance and image optimization settings.', 'affos'); ?></p>
                            </div>

                            <div class="affos-card">
                                <div class="affos-card-title">
                                    <span class="dashicons dashicons-images-alt2"></span>
                                    <?php esc_html_e('Image Optimization', 'affos'); ?>
                                </div>
                                <div class="affos-card-content">
                                    <div class="affos-field">
                                        <label class="affos-toggle-label">
                                            <input type="checkbox" name="lazy_load_enabled" <?php checked($settings['lazy_load_enabled']); ?>>
                                            <?php esc_html_e('Enable Lazy Load', 'affos'); ?>
                                        </label>
                                        <p class="affos-field-desc">
                                            <?php esc_html_e('Adds loading="lazy" to images for faster page loads.', 'affos'); ?>
                                        </p>
                                    </div>

                                    <div class="affos-field">
                                        <label class="affos-toggle-label">
                                            <input type="checkbox" name="fallback_alt_enabled" <?php checked($settings['fallback_alt_enabled']); ?>>
                                            <?php esc_html_e('Use Fallback Alt Text', 'affos'); ?>
                                        </label>
                                        <p class="affos-field-desc">
                                            <?php esc_html_e('When image alt is empty, use fallback text for accessibility.', 'affos'); ?>
                                        </p>
                                    </div>

                                    <div class="affos-field affos-field-row">
                                        <label><?php esc_html_e('Fallback Alt Text', 'affos'); ?></label>
                                        <input type="text" name="fallback_alt_text"
                                            value="<?php echo esc_attr($settings['fallback_alt_text']); ?>" placeholder="Image">
                                    </div>
                                </div>
                            </div>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Footer -->
            <div class="affos-footer">
                <p><?php esc_html_e('Changes are saved automatically.', 'affos'); ?></p>
                <span class="affos-save-status"></span>
            </div>
        </div>
        <?php
    }
}

// Initialize
new Affos_Settings();
