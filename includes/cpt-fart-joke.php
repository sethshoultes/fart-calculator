<?php
// Register Fart Joke CPT
function fc_register_fart_joke_cpt() {
    $labels = array(
        'name'                  => _x( 'Fart Jokes', 'Post Type General Name', 'fart-calculator' ),
        'singular_name'         => _x( 'Fart Joke', 'Post Type Singular Name', 'fart-calculator' ),
        'menu_name'             => __( 'Fart Jokes', 'fart-calculator' ),
        'all_items'             => __( 'All Fart Jokes', 'fart-calculator' ),
        'add_new_item'          => __( 'Add New Fart Joke', 'fart-calculator' ),
    );

    $args = array(
        'label'                 => __( 'Fart Joke', 'fart-calculator' ),
        'public'                => true,
        'supports'              => array( 'title', 'editor' ),
        'show_in_menu'          => true,
        'menu_icon'             => 'dashicons-smiley',
        'has_archive'           => false,
    );

    register_post_type( 'fart_joke', $args );
}
