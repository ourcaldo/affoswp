<?php
/**
 * Header template
 *
 * @package Affos
 * @since 1.0.0
 */

// Get compare page URL
$compare_page_id = get_option('affos_compare_page');
$compare_url = $compare_page_id ? get_permalink($compare_page_id) : home_url('/bandingkan/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?> data-compare-url="<?php echo esc_url($compare_url); ?>">
    <?php wp_body_open(); ?>

    <a class="skip-link" href="#main-content"><?php esc_html_e('Langsung ke konten', 'affos'); ?></a>

    <!-- Header -->
    <header class="site-header">
        <div class="container header-inner">
            <?php
            if (has_custom_logo()) {
                the_custom_logo();
            } else {
                ?>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="logo">
                    <?php echo esc_html(get_bloginfo('name')); ?><span>.</span>
                </a>
                <?php
            }
            ?>

            <nav class="nav-main" aria-label="<?php esc_attr_e('Menu utama', 'affos'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'items_wrap' => '%3$s',
                    'fallback_cb' => 'affos_fallback_menu',
                    'depth' => 1,
                ));
                ?>
            </nav>

            <div class="header-actions">
                <button class="icon-btn mobile-menu-btn" id="mobile-menu-toggle" aria-label="<?php esc_attr_e('Menu', 'affos'); ?>" aria-expanded="false" aria-controls="mobile-menu">
                    <i class="ri-menu-line" aria-hidden="true"></i>
                </button>
                <button class="icon-btn search-toggle" id="search-toggle" aria-label="<?php esc_attr_e('Search', 'affos'); ?>">
                    <i class="ri-search-line" aria-hidden="true"></i>
                </button>
                <?php
                $subscribe_url = apply_filters('affos_subscribe_url', '#newsletter');
                ?>
                <a href="<?php echo esc_url($subscribe_url); ?>" class="btn-primary">
                    <?php esc_html_e('Langganan', 'affos'); ?>
                </a>
            </div>
        </div>
    </header>

    <!-- Mobile Nav Overlay -->
    <div class="mobile-nav-overlay" id="mobile-menu" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Menu navigasi', 'affos'); ?>">
        <div class="mobile-nav-panel">
            <button class="icon-btn mobile-nav-close" id="close-mobile-menu" aria-label="<?php esc_attr_e('Tutup', 'affos'); ?>">
                <i class="ri-close-line" aria-hidden="true"></i>
            </button>
            <nav aria-label="<?php esc_attr_e('Menu mobile', 'affos'); ?>">
                <?php
                wp_nav_menu(array(
                    'theme_location' => 'primary',
                    'container' => false,
                    'menu_class' => 'mobile-nav-links',
                    'fallback_cb' => 'affos_fallback_menu',
                    'depth' => 2,
                ));
                ?>
            </nav>
        </div>
    </div>
