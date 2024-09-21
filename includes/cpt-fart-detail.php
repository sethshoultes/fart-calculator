<?php
function fc_register_fart_detail_cpt(): void {
    $labels = array(
        'name'                  => _x( 'Fart Details', 'Post Type General Name', 'fart-calculator' ),
        'singular_name'         => _x( 'Fart Detail', 'Post Type Singular Name', 'fart-calculator' ),
        'menu_name'             => __( 'Fart Details', 'fart-calculator' ),
        'name_admin_bar'        => __( 'Fart Detail', 'fart-calculator' ),
        'archives'              => __( 'Fart Archives', 'fart-calculator' ),
        'attributes'            => __( 'Fart Attributes', 'fart-calculator' ),
        'parent_item_colon'     => __( 'Parent Fart:', 'fart-calculator' ),
        'all_items'             => __( 'All Fart Details', 'fart-calculator' ),
        'add_new_item'          => __( 'Add New Fart Detail', 'fart-calculator' ),
        'add_new'               => __( 'Add New', 'fart-calculator' ),
        'new_item'              => __( 'New Fart Detail', 'fart-calculator' ),
        'edit_item'             => __( 'Edit Fart Detail', 'fart-calculator' ),
        'update_item'           => __( 'Update Fart Detail', 'fart-calculator' ),
        'view_item'             => __( 'View Fart Detail', 'fart-calculator' ),
        'view_items'            => __( 'View Fart Details', 'fart-calculator' ),
        'search_items'          => __( 'Search Fart Detail', 'fart-calculator' ),
        'not_found'             => __( 'Not found', 'fart-calculator' ),
        'not_found_in_trash'    => __( 'Not found in Trash', 'fart-calculator' ),
        'featured_image'        => __( 'Fart Image', 'fart-calculator' ),
        'set_featured_image'    => __( 'Set fart image', 'fart-calculator' ),
        'remove_featured_image' => __( 'Remove fart image', 'fart-calculator' ),
        'use_featured_image'    => __( 'Use as fart image', 'fart-calculator' ),
        'insert_into_item'      => __( 'Insert into fart', 'fart-calculator' ),
        'uploaded_to_this_item' => __( 'Uploaded to this fart', 'fart-calculator' ),
        'items_list'            => __( 'Fart details list', 'fart-calculator' ),
        'items_list_navigation' => __( 'Fart details list navigation', 'fart-calculator' ),
        'filter_items_list'     => __( 'Filter fart details list', 'fart-calculator' ),
    );
    $args = array(
        'label'                 => __( 'Fart Detail', 'fart-calculator' ),
        'description'           => __( 'Details and rankings for farts', 'fart-calculator' ),
        'labels'                => $labels,
        'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
        'hierarchical'          => false,
        'public'                => true,
        'show_ui'               => true,
        'show_in_menu'          => true,
        'menu_position'         => 25,
        'menu_icon'             => 'dashicons-smiley',
        'show_in_admin_bar'     => true,
        'show_in_nav_menus'     => true,
        'can_export'            => true,
        'has_archive'           => false, // Set to true if you want an archive page
        'exclude_from_search'   => true,
        'publicly_queryable'    => true,
        'rewrite'               => array(
            'slug'       => 'fart_detail',
            'with_front' => false,
            'pages'      => true,
            'feeds'      => false,
        ),
        'capability_type'       => 'post',
    );
    register_post_type( 'fart_detail', $args );
}

// Register Category Taxonomy for Fart Details
function fc_register_fart_detail_category_taxonomy() {
    $labels = array(
        'name'              => _x( 'Fart Detail Categories', 'taxonomy general name', 'fart-calculator' ),
        'singular_name'     => _x( 'Fart Detail Category', 'taxonomy singular name', 'fart-calculator' ),
        'search_items'      => __( 'Search Categories', 'fart-calculator' ),
        'all_items'         => __( 'All Categories', 'fart-calculator' ),
        'parent_item'       => __( 'Parent Category', 'fart-calculator' ),
        'parent_item_colon' => __( 'Parent Category:', 'fart-calculator' ),
        'edit_item'         => __( 'Edit Category', 'fart-calculator' ),
        'update_item'       => __( 'Update Category', 'fart-calculator' ),
        'add_new_item'      => __( 'Add New Category', 'fart-calculator' ),
        'new_item_name'     => __( 'New Category Name', 'fart-calculator' ),
        'menu_name'         => __( 'Categories', 'fart-calculator' ),
    );

    $args = array(
        'hierarchical'      => true, // Categories are hierarchical
        'labels'            => $labels,
        'show_ui'           => true,
        'show_admin_column' => true,
        'query_var'         => true,
        'rewrite'           => array( 'slug' => 'fart-detail-category' ),
    );

    register_taxonomy( 'fart_detail_category', array( 'fart_detail' ), $args );
}
// Register Tag Taxonomy for Fart Details
function fc_register_fart_detail_tag_taxonomy() {
    $labels = array(
        'name'                       => _x( 'Fart Detail Tags', 'taxonomy general name', 'fart-calculator' ),
        'singular_name'              => _x( 'Fart Detail Tag', 'taxonomy singular name', 'fart-calculator' ),
        'search_items'               => __( 'Search Tags', 'fart-calculator' ),
        'popular_items'              => __( 'Popular Tags', 'fart-calculator' ),
        'all_items'                  => __( 'All Tags', 'fart-calculator' ),
        'edit_item'                  => __( 'Edit Tag', 'fart-calculator' ),
        'update_item'                => __( 'Update Tag', 'fart-calculator' ),
        'add_new_item'               => __( 'Add New Tag', 'fart-calculator' ),
        'new_item_name'              => __( 'New Tag Name', 'fart-calculator' ),
        'separate_items_with_commas' => __( 'Separate tags with commas', 'fart-calculator' ),
        'add_or_remove_items'        => __( 'Add or remove tags', 'fart-calculator' ),
        'choose_from_most_used'      => __( 'Choose from the most used tags', 'fart-calculator' ),
        'menu_name'                  => __( 'Tags', 'fart-calculator' ),
    );

    $args = array(
        'hierarchical'          => false, // Tags are not hierarchical
        'labels'                => $labels,
        'show_ui'               => true,
        'show_admin_column'     => true,
        'update_count_callback' => '_update_post_term_count',
        'query_var'             => true,
        'rewrite'               => array( 'slug' => 'fart-detail-tag' ),
    );

    register_taxonomy( 'fart_detail_tag', array( 'fart_detail' ), $args );
}
