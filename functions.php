<?php
/**
 * Affos Theme Functions
 *
 * @package Affos
 * @since 1.0.0
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme constants
define('AFFOS_VERSION', '1.1.4');
define('AFFOS_DIR', get_template_directory());
define('AFFOS_URI', get_template_directory_uri());

/**
 * Autoload theme classes
 */
function affos_autoload()
{
    $classes = array(
        'class-theme-setup',
        'class-cpt',
        'class-product-meta',
        'class-review-meta',
        'class-settings',
    );

    foreach ($classes as $class) {
        $file = AFFOS_DIR . '/inc/' . $class . '.php';
        if (file_exists($file)) {
            require_once $file;
        }
    }
}
add_action('after_setup_theme', 'affos_autoload', 1);

/**
 * Enqueue scripts and styles
 */
function affos_enqueue_assets()
{
    // Google Fonts - Plus Jakarta Sans
    wp_enqueue_style(
        'affos-fonts',
        'https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap',
        array(),
        null
    );

    // Remix Icons
    wp_enqueue_style(
        'affos-icons',
        'https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css',
        array(),
        '3.5.0'
    );

    // Main stylesheet (imports all modular CSS files)
    wp_enqueue_style(
        'affos-theme',
        AFFOS_URI . '/assets/css/main.css',
        array('affos-fonts', 'affos-icons'),
        AFFOS_VERSION
    );

    // Main JavaScript
    wp_enqueue_script(
        'affos-main',
        AFFOS_URI . '/assets/js/main.js',
        array(),
        AFFOS_VERSION,
        true
    );

    // Localize script for AJAX
    wp_localize_script('affos-main', 'affosData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('affos_nonce'),
        'homeUrl' => home_url('/'),
    ));
}
add_action('wp_enqueue_scripts', 'affos_enqueue_assets');

/**
 * Theme setup
 */
function affos_theme_setup()
{
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ));
    add_theme_support('custom-logo', array(
        'height' => 60,
        'width' => 200,
        'flex-height' => true,
        'flex-width' => true,
    ));
    add_theme_support('editor-styles');
    add_theme_support('responsive-embeds');

    // Register navigation menus
    register_nav_menus(array(
        'primary' => esc_html__('Primary Menu', 'affos'),
        'footer' => esc_html__('Footer Menu', 'affos'),
    ));

    // Set content width
    if (!isset($content_width)) {
        $content_width = 1200;
    }
}
add_action('after_setup_theme', 'affos_theme_setup');

/**
 * Register widget areas
 */
function affos_widgets_init()
{
    register_sidebar(array(
        'name' => esc_html__('Blog Sidebar', 'affos'),
        'id' => 'sidebar-blog',
        'description' => esc_html__('Widgets for blog sidebar.', 'affos'),
        'before_widget' => '<div id="%1$s" class="sidebar-card %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4 class="sidebar-title">',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer Column 1', 'affos'),
        'id' => 'footer-1',
        'description' => esc_html__('First footer column.', 'affos'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer Column 2', 'affos'),
        'id' => 'footer-2',
        'description' => esc_html__('Second footer column.', 'affos'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));

    register_sidebar(array(
        'name' => esc_html__('Footer Column 3', 'affos'),
        'id' => 'footer-3',
        'description' => esc_html__('Third footer column.', 'affos'),
        'before_widget' => '<div id="%1$s" class="footer-widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h4>',
        'after_title' => '</h4>',
    ));
}
add_action('widgets_init', 'affos_widgets_init');

/**
 * Add body classes
 */
function affos_body_classes($classes)
{
    // Add page-specific classes
    if (is_singular('product')) {
        $classes[] = 'single-product-page';
    } elseif (is_singular('review')) {
        $classes[] = 'single-review-page';
    } elseif (is_post_type_archive('product')) {
        $classes[] = 'products-archive';
    } elseif (is_post_type_archive('review')) {
        $classes[] = 'reviews-archive';
    }

    // Add has-sidebar class
    if (is_active_sidebar('sidebar-blog') && is_singular('post')) {
        $classes[] = 'has-sidebar';
    }

    return $classes;
}
add_filter('body_class', 'affos_body_classes');

/**
 * Add rewrite rules for /bandingkan/ virtual page
 * Supports: /bandingkan/ and /bandingkan/product-slug-vs-product-slug/
 */
function affos_compare_rewrite_rules()
{
    // Base compare page (empty or localStorage-based)
    add_rewrite_rule('^bandingkan/?$', 'index.php?affos_compare=1', 'top');

    // SEO-friendly compare URL: /bandingkan/product-1-vs-product-2/
    add_rewrite_rule('^bandingkan/([^/]+)-vs-([^/]+)/?$', 'index.php?affos_compare=1&compare_products=$matches[1]-vs-$matches[2]', 'top');

    // Multi-product compare: /bandingkan/product-1-vs-product-2-vs-product-3/
    add_rewrite_rule('^bandingkan/([^/]+)-vs-([^/]+)-vs-([^/]+)/?$', 'index.php?affos_compare=1&compare_products=$matches[1]-vs-$matches[2]-vs-$matches[3]', 'top');
}
add_action('init', 'affos_compare_rewrite_rules');

/**
 * Register compare query vars
 */
function affos_compare_query_vars($vars)
{
    $vars[] = 'affos_compare';
    $vars[] = 'compare_products';
    return $vars;
}
add_filter('query_vars', 'affos_compare_query_vars');

/**
 * Load compare template for /bandingkan/ URL
 */
function affos_compare_template($template)
{
    if (get_query_var('affos_compare')) {
        $compare_template = locate_template('page-compare.php');
        if ($compare_template) {
            return $compare_template;
        }
    }
    return $template;
}
add_filter('template_include', 'affos_compare_template');

/**
 * Set proper title for compare page
 */
function affos_compare_title($title)
{
    if (get_query_var('affos_compare')) {
        // Try to build dynamic title from product slugs in URL
        $compare_slug = get_query_var('compare_products', '');
        if ($compare_slug) {
            $slugs = explode('-vs-', $compare_slug);
            $names = array();
            foreach ($slugs as $slug) {
                $product = get_page_by_path($slug, OBJECT, 'product');
                if ($product) {
                    $names[] = $product->post_title;
                }
            }
            if (count($names) >= 2) {
                return 'Perbandingan ' . implode(' vs ', $names) . ' - ' . get_bloginfo('name');
            }
        }
        return __('Bandingkan Gadget', 'affos') . ' - ' . get_bloginfo('name');
    }
    return $title;
}
add_filter('pre_get_document_title', 'affos_compare_title');

/**
 * Get store info (name, logo, icon) for a store key
 * 
 * @param string $store_key Store key (shopee, tokopedia, blibli, other)
 * @return array Store info with name, logo_url, icon
 */
function affos_get_store_info($store_key)
{
    $stores = array(
        'shopee' => array(
            'name' => 'Shopee',
            'logo' => 'shopee-seeklogo.png',
            'icon' => 'ri-shopping-bag-line',
        ),
        'tokopedia' => array(
            'name' => 'Tokopedia',
            'logo' => 'tokopedia-seeklogo.png',
            'icon' => 'ri-store-2-line',
        ),
        'blibli' => array(
            'name' => 'Blibli',
            'logo' => 'blibli.webp',
            'icon' => 'ri-shopping-cart-2-line',
        ),
        'other' => array(
            'name' => 'Toko Lainnya',
            'logo' => '',
            'icon' => 'ri-store-line',
        ),
    );

    // Check if key exists in predefined stores
    if (isset($stores[$store_key])) {
        $store = $stores[$store_key];
        $store['logo_url'] = !empty($store['logo'])
            ? AFFOS_URI . '/assets/images/' . $store['logo']
            : '';
        return $store;
    }

    // Legacy support: if old text value, try to match
    $store_lower = strtolower($store_key);
    foreach ($stores as $key => $info) {
        if (strpos($store_lower, $key) !== false || strpos($store_lower, strtolower($info['name'])) !== false) {
            $info['logo_url'] = !empty($info['logo'])
                ? AFFOS_URI . '/assets/images/' . $info['logo']
                : '';
            return $info;
        }
    }

    // Fallback for unknown stores
    return array(
        'name' => $store_key ?: 'Toko Online',
        'logo' => '',
        'logo_url' => '',
        'icon' => 'ri-store-line',
    );
}

/**
 * Flush rewrite rules on theme activation
 */
function affos_rewrite_flush()
{
    // Register rewrite rules first
    affos_compare_rewrite_rules();

    // Manually register CPTs before flushing (init hasn't fired yet)
    $cpt_file = get_template_directory() . '/inc/class-cpt.php';
    if (file_exists($cpt_file)) {
        require_once $cpt_file;
        $cpt = new Affos_CPT();
        $cpt->register_post_types();
        $cpt->register_taxonomies();
    }
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'affos_rewrite_flush');

/**
 * Also flush on init if option is set (for manual flush)
 */
function affos_maybe_flush_rewrite()
{
    if (get_option('affos_flush_rewrite_rules')) {
        flush_rewrite_rules();
        delete_option('affos_flush_rewrite_rules');
    }
}
add_action('init', 'affos_maybe_flush_rewrite', 99);

/**
 * AJAX handler for compare page data
 */
function affos_get_compare_data()
{
    $ids = isset($_GET['ids']) ? sanitize_text_field($_GET['ids']) : '';

    if (empty($ids)) {
        wp_send_json_error('No product IDs provided');
    }

    $product_ids = array_map('intval', explode(',', $ids));
    $products = array();

    foreach ($product_ids as $id) {
        $post = get_post($id);
        if (!$post || $post->post_type !== 'product') {
            continue;
        }

        // Get thumbnail
        $thumbnail = '';
        if (has_post_thumbnail($id)) {
            $thumbnail = get_the_post_thumbnail_url($id, 'medium');
        }

        // Get specs
        $products[] = array(
            'id' => $id,
            'title' => $post->post_title,
            'thumbnail' => $thumbnail,
            'price' => get_post_meta($id, '_misc_price', true),
            'specs' => array(
                'chipset' => get_post_meta($id, '_platform_chipset', true),
                'display' => get_post_meta($id, '_display_type', true),
                'camera' => get_post_meta($id, '_camera_main_specs', true),
                'battery' => get_post_meta($id, '_battery_type', true),
                'ram' => get_post_meta($id, '_memory_internal', true),
                'storage' => get_post_meta($id, '_memory_internal', true),
                'os' => get_post_meta($id, '_platform_os', true),
                'dimensions' => get_post_meta($id, '_body_dimensions', true),
                'weight' => get_post_meta($id, '_body_weight', true),
            ),
        );
    }

    wp_send_json_success($products);
}
add_action('wp_ajax_affos_get_compare_data', 'affos_get_compare_data');
add_action('wp_ajax_nopriv_affos_get_compare_data', 'affos_get_compare_data');

/**
 * AJAX handler to get SEO-friendly compare URL from product IDs
 */
function affos_get_compare_slugs()
{
    $ids = isset($_GET['ids']) ? sanitize_text_field($_GET['ids']) : '';

    if (empty($ids)) {
        wp_send_json_error('No IDs provided');
    }

    $product_ids = array_filter(array_map('intval', explode(',', $ids)));
    $slugs = array();

    foreach ($product_ids as $id) {
        $post = get_post($id);
        if ($post && $post->post_type === 'product') {
            $slugs[] = $post->post_name;
        }
    }

    if (count($slugs) < 2) {
        wp_send_json_error('Need at least 2 valid products');
    }

    $compare_url = home_url('/bandingkan/' . implode('-vs-', $slugs) . '/');

    wp_send_json_success(array('url' => $compare_url, 'slugs' => $slugs));
}
add_action('wp_ajax_affos_get_compare_slugs', 'affos_get_compare_slugs');
add_action('wp_ajax_nopriv_affos_get_compare_slugs', 'affos_get_compare_slugs');

/**
 * AJAX handler to get product names for compare bar tooltips
 */
function affos_get_compare_names()
{
    $ids = isset($_GET['ids']) ? sanitize_text_field($_GET['ids']) : '';

    if (empty($ids)) {
        wp_send_json_error('No IDs provided');
    }

    $product_ids = array_filter(array_map('intval', explode(',', $ids)));
    $names = array();

    foreach ($product_ids as $id) {
        $post = get_post($id);
        if ($post && $post->post_type === 'product') {
            $names[strval($id)] = $post->post_title;
        }
    }

    wp_send_json_success($names);
}
add_action('wp_ajax_affos_get_compare_names', 'affos_get_compare_names');
add_action('wp_ajax_nopriv_affos_get_compare_names', 'affos_get_compare_names');
