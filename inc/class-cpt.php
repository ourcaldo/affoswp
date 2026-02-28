<?php
/**
 * Custom Post Types Registration
 *
 * Registers Product and Review CPTs with taxonomies
 *
 * @package Affos
 * @since 1.0.0
 */

if (!defined('ABSPATH')) {
    exit;
}

class Affos_CPT
{

    /**
     * Constructor
     */
    public function __construct()
    {
        add_action('init', array($this, 'register_post_types'));
        add_action('init', array($this, 'register_taxonomies'));
    }

    /**
     * Register Custom Post Types
     */
    public function register_post_types()
    {
        // Product CPT
        $product_labels = array(
            'name' => _x('Products', 'Post type general name', 'affos'),
            'singular_name' => _x('Product', 'Post type singular name', 'affos'),
            'menu_name' => _x('Products', 'Admin Menu text', 'affos'),
            'name_admin_bar' => _x('Product', 'Add New on Toolbar', 'affos'),
            'add_new' => __('Add New', 'affos'),
            'add_new_item' => __('Add New Product', 'affos'),
            'new_item' => __('New Product', 'affos'),
            'edit_item' => __('Edit Product', 'affos'),
            'view_item' => __('View Product', 'affos'),
            'all_items' => __('All Products', 'affos'),
            'search_items' => __('Search Products', 'affos'),
            'parent_item_colon' => __('Parent Products:', 'affos'),
            'not_found' => __('No products found.', 'affos'),
            'not_found_in_trash' => __('No products found in Trash.', 'affos'),
            'featured_image' => _x('Product Image', 'Overrides the "Featured Image" phrase', 'affos'),
            'set_featured_image' => _x('Set product image', 'Overrides the "Set featured image" phrase', 'affos'),
            'remove_featured_image' => _x('Remove product image', 'Overrides the "Remove featured image" phrase', 'affos'),
            'use_featured_image' => _x('Use as product image', 'Overrides the "Use as featured image" phrase', 'affos'),
            'archives' => _x('Product archives', 'The post type archive label', 'affos'),
            'insert_into_item' => _x('Insert into product', 'Overrides the "Insert into post" phrase', 'affos'),
            'uploaded_to_this_item' => _x('Uploaded to this product', 'Overrides the "Uploaded to this post" phrase', 'affos'),
            'filter_items_list' => _x('Filter products list', 'Screen reader text', 'affos'),
            'items_list_navigation' => _x('Products list navigation', 'Screen reader text', 'affos'),
            'items_list' => _x('Products list', 'Screen reader text', 'affos'),
        );

        $product_args = array(
            'labels' => $product_labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'produk', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 5,
            'menu_icon' => 'dashicons-smartphone',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
            'show_in_rest' => true,
        );

        register_post_type('product', $product_args);

        // Review CPT
        $review_labels = array(
            'name' => _x('Reviews', 'Post type general name', 'affos'),
            'singular_name' => _x('Review', 'Post type singular name', 'affos'),
            'menu_name' => _x('Reviews', 'Admin Menu text', 'affos'),
            'name_admin_bar' => _x('Review', 'Add New on Toolbar', 'affos'),
            'add_new' => __('Add New', 'affos'),
            'add_new_item' => __('Add New Review', 'affos'),
            'new_item' => __('New Review', 'affos'),
            'edit_item' => __('Edit Review', 'affos'),
            'view_item' => __('View Review', 'affos'),
            'all_items' => __('All Reviews', 'affos'),
            'search_items' => __('Search Reviews', 'affos'),
            'parent_item_colon' => __('Parent Reviews:', 'affos'),
            'not_found' => __('No reviews found.', 'affos'),
            'not_found_in_trash' => __('No reviews found in Trash.', 'affos'),
            'featured_image' => _x('Review Image', 'Overrides the "Featured Image" phrase', 'affos'),
            'set_featured_image' => _x('Set review image', 'Overrides the "Set featured image" phrase', 'affos'),
            'remove_featured_image' => _x('Remove review image', 'Overrides the "Remove featured image" phrase', 'affos'),
            'use_featured_image' => _x('Use as review image', 'Overrides the "Use as featured image" phrase', 'affos'),
            'archives' => _x('Review archives', 'The post type archive label', 'affos'),
            'insert_into_item' => _x('Insert into review', 'Overrides the "Insert into post" phrase', 'affos'),
            'uploaded_to_this_item' => _x('Uploaded to this review', 'Overrides the "Uploaded to this post" phrase', 'affos'),
            'filter_items_list' => _x('Filter reviews list', 'Screen reader text', 'affos'),
            'items_list_navigation' => _x('Reviews list navigation', 'Screen reader text', 'affos'),
            'items_list' => _x('Reviews list', 'Screen reader text', 'affos'),
        );

        $review_args = array(
            'labels' => $review_labels,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'ulasan', 'with_front' => false),
            'capability_type' => 'post',
            'has_archive' => true,
            'hierarchical' => false,
            'menu_position' => 6,
            'menu_icon' => 'dashicons-star-filled',
            'supports' => array('title', 'editor', 'thumbnail', 'excerpt', 'custom-fields', 'revisions'),
            'show_in_rest' => true,
        );

        register_post_type('review', $review_args);
    }

    /**
     * Register Taxonomies
     */
    public function register_taxonomies()
    {
        // Product Category
        $cat_labels = array(
            'name' => _x('Product Categories', 'taxonomy general name', 'affos'),
            'singular_name' => _x('Product Category', 'taxonomy singular name', 'affos'),
            'search_items' => __('Search Categories', 'affos'),
            'all_items' => __('All Categories', 'affos'),
            'parent_item' => __('Parent Category', 'affos'),
            'parent_item_colon' => __('Parent Category:', 'affos'),
            'edit_item' => __('Edit Category', 'affos'),
            'update_item' => __('Update Category', 'affos'),
            'add_new_item' => __('Add New Category', 'affos'),
            'new_item_name' => __('New Category Name', 'affos'),
            'menu_name' => __('Categories', 'affos'),
        );

        register_taxonomy('product_category', array('product'), array(
            'hierarchical' => true,
            'labels' => $cat_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'produk/kategori', 'with_front' => false),
            'show_in_rest' => true,
        ));

        // Product Brand
        $brand_labels = array(
            'name' => _x('Brands', 'taxonomy general name', 'affos'),
            'singular_name' => _x('Brand', 'taxonomy singular name', 'affos'),
            'search_items' => __('Search Brands', 'affos'),
            'all_items' => __('All Brands', 'affos'),
            'edit_item' => __('Edit Brand', 'affos'),
            'update_item' => __('Update Brand', 'affos'),
            'add_new_item' => __('Add New Brand', 'affos'),
            'new_item_name' => __('New Brand Name', 'affos'),
            'menu_name' => __('Brands', 'affos'),
        );

        register_taxonomy('product_brand', array('product'), array(
            'hierarchical' => false,
            'labels' => $brand_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'produk/merek', 'with_front' => false),
            'show_in_rest' => true,
        ));

        // Review Category
        $review_cat_labels = array(
            'name' => _x('Review Categories', 'taxonomy general name', 'affos'),
            'singular_name' => _x('Review Category', 'taxonomy singular name', 'affos'),
            'search_items' => __('Search Categories', 'affos'),
            'all_items' => __('All Categories', 'affos'),
            'parent_item' => __('Parent Category', 'affos'),
            'parent_item_colon' => __('Parent Category:', 'affos'),
            'edit_item' => __('Edit Category', 'affos'),
            'update_item' => __('Update Category', 'affos'),
            'add_new_item' => __('Add New Category', 'affos'),
            'new_item_name' => __('New Category Name', 'affos'),
            'menu_name' => __('Categories', 'affos'),
        );

        register_taxonomy('review_category', array('review'), array(
            'hierarchical' => true,
            'labels' => $review_cat_labels,
            'show_ui' => true,
            'show_admin_column' => true,
            'query_var' => true,
            'rewrite' => array('slug' => 'ulasan/kategori', 'with_front' => false),
            'show_in_rest' => true,
        ));
    }
}
