<?php
/**
 * Sidebar Template
 *
 * @package Affos
 * @since 1.1.4
 */

if (!is_active_sidebar('sidebar-blog')) {
    return;
}
?>

<aside class="sidebar" role="complementary" aria-label="<?php esc_attr_e('Blog Sidebar', 'affos'); ?>">
    <?php dynamic_sidebar('sidebar-blog'); ?>
</aside>
