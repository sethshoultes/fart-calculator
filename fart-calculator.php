<?php
/**
 * Plugin Name: Fart Calculator
 * Description: A fun calculator that estimates fart frequency based on user inputs, with detailed rankings and front-end submission.
 * Version: 1.8 
 * Author: Seth Shoultes
 * License: GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require_once plugin_dir_path( __FILE__ ) . 'includes/rest-api.php';
//require_once plugin_dir_path( __FILE__ ) . 'includes/cpt-fart-details.php';


/**
 * Main Plugin Class
 */
if ( ! class_exists( 'Fart_Calculator' ) ) :

class Fart_Calculator {

    /**
     * Constructor: Initializes the plugin by setting up hooks.
     */
    public function __construct() {
        // Register Fart Brands Custom Post Type
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_detail_cpt' ), priority: 0 );
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_detail_category_taxonomy' ), priority: 0 );
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_detail_tag_taxonomy' ), priority: 0 );

         
        // Register Fart Jokes Custom Post Type
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_joke_cpt' ) );
        add_action( hook_name: 'init', callback: array( $this, 'fc_add_fart_joke_votes_meta') );
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_joke_category_taxonomy' ), priority: 0 );
        add_action( hook_name: 'init', callback: array( $this, 'fc_register_fart_joke_tag_taxonomy' ), priority: 0 );



        // Add Meta Boxes
        add_action( hook_name: 'add_meta_boxes', callback: array( $this, 'fc_add_fart_detail_meta_boxes' ) );
        add_action( hook_name: 'add_meta_boxes', callback: array( $this, 'fc_add_fart_joke_meta_boxes' ) );
        add_action( hook_name: 'add_meta_boxes', callback: array( $this, 'fc_add_fart_detail_votes_meta' ) );


        // Save Meta Box Data
        add_action( hook_name: 'save_post', callback: array( $this, 'fc_save_fart_detail_meta_boxes' ) );
        add_action( hook_name: 'save_post', callback: array( $this, 'fc_save_fart_joke_meta_boxes' ) );
        add_action( hook_name: 'save_post', callback: array( $this, 'fc_save_fart_detail_votes' ) );

        // Customize Admin Columns
        add_filter( hook_name: 'manage_fart_detail_posts_columns', callback: array( $this, 'fc_set_custom_fart_detail_columns' ) );
        add_action( hook_name: 'manage_fart_detail_posts_custom_column' , callback: array( $this, 'fc_custom_fart_detail_column' ), priority: 10, accepted_args: 2 );

        // Enqueue Styles
        add_action( hook_name: 'wp_enqueue_scripts', callback: array( $this, 'fc_enqueue_styles' ) );
        add_action( hook_name: 'wp_enqueue_scripts', callback: array( $this, 'fc_enqueue_voting_script' ) );
        

        // Shortcodes
        add_shortcode( tag: 'fart_calculator', callback: array( $this, 'fc_display_fart_calculator' ) );//[fart_calculator]
        add_shortcode( tag: 'fart_details_list', callback: array( $this, 'fc_display_fart_details_list' ) );//[fart_details_list]
        add_shortcode( tag: 'submit_fart_detail', callback: array( $this, 'fc_display_submission_form' ) );//[submit_fart_detail]
        add_shortcode( tag: 'fart_ranker', callback: array( $this, 'fc_display_fart_ranker' ) );//[fart_ranker]
        add_shortcode( tag: 'fart_jokes', callback: array( $this, 'fc_display_fart_jokes' ) );//[fart_jokes]
        add_shortcode( tag: 'submit_fart_joke', callback: array( $this, 'fc_fart_joke_submission_form' ) );//[submit_fart_joke]
        add_shortcode( tag: 'fart_joke_leaderboard', callback: array( $this, 'fc_fart_joke_leaderboard' ) );//[fart_joke_leaderboard]


        // Template Loader
        add_filter( hook_name: 'template_include', callback: array( $this, 'fc_load_fart_detail_template' ) );

        // Initialize Rating Functionality
        add_action( hook_name: 'wp_ajax_fc_submit_fart_rating', callback: array( $this, 'fc_handle_fart_rating' ) );
        add_action( hook_name: 'wp_ajax_nopriv_fc_submit_fart_rating', callback: array( $this, 'fc_handle_fart_rating' ) );
        add_action( hook_name: 'wp_ajax_fc_fart_joke_vote', callback: array( $this, 'fc_handle_fart_joke_vote' ) );
        add_action( hook_name: 'wp_ajax_nopriv_fc_fart_joke_vote', callback: array( $this, 'fc_handle_fart_joke_vote' ) );
        add_action( 'wp_ajax_fc_fart_detail_vote', array( $this, 'fc_handle_fart_detail_vote' ) );
        add_action( 'wp_ajax_nopriv_fc_fart_detail_vote', array( $this, 'fc_handle_fart_detail_vote' ) );

    }

    /**
     * Registers the Custom Post Type 'fart_detail'.
     */
    public function fc_register_fart_detail_cpt() {
        $labels = array(
            'name'                  => _x( 'Fart Brands', 'Post Type General Name', 'fart-calculator' ),
            'singular_name'         => _x( 'Fart Brand', 'Post Type Singular Name', 'fart-calculator' ),
            'menu_name'             => __( 'Fart Brands', 'fart-calculator' ),
            'name_admin_bar'        => __( 'Fart Brand', 'fart-calculator' ),
            'archives'              => __( 'Fart Archives', 'fart-calculator' ),
            'attributes'            => __( 'Fart Attributes', 'fart-calculator' ),
            'parent_item_colon'     => __( 'Parent Fart:', 'fart-calculator' ),
            'all_items'             => __( 'All Fart Brands', 'fart-calculator' ),
            'add_new_item'          => __( 'Add New Fart Brand', 'fart-calculator' ),
            'add_new'               => __( 'Add New Brand', 'fart-calculator' ),
            'new_item'              => __( 'New Fart Brand', 'fart-calculator' ),
            'edit_item'             => __( 'Edit Fart Brand', 'fart-calculator' ),
            'update_item'           => __( 'Update Fart Brand', 'fart-calculator' ),
            'view_item'             => __( 'View Fart Brand', 'fart-calculator' ),
            'view_items'            => __( 'View Fart Brands', 'fart-calculator' ),
            'search_items'          => __( 'Search Fart Brand', 'fart-calculator' ),
            'not_found'             => __( 'Not found', 'fart-calculator' ),
            'not_found_in_trash'    => __( 'Not found in Trash', 'fart-calculator' ),
            'featured_image'        => __( 'Fart Image', 'fart-calculator' ),
            'set_featured_image'    => __( 'Set fart image', 'fart-calculator' ),
            'remove_featured_image' => __( 'Remove fart image', 'fart-calculator' ),
            'use_featured_image'    => __( 'Use as fart image', 'fart-calculator' ),
            'insert_into_item'      => __( 'Insert into fart', 'fart-calculator' ),
            'uploaded_to_this_item' => __( 'Uploaded to this fart', 'fart-calculator' ),
            'items_list'            => __( 'Fart brands list', 'fart-calculator' ),
            'items_list_navigation' => __( 'Fart brands list navigation', 'fart-calculator' ),
            'filter_items_list'     => __( 'Filter fart brands list', 'fart-calculator' ),
        );
        $args = array(
            'label'                 => __( 'Fart Brand', 'fart-calculator' ),
            'description'           => __( 'Brands and rankings for farts', 'fart-calculator' ),
            'labels'                => $labels,
            'supports'              => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
            'hierarchical'          => false,
            'public'                => true,
            'show_ui'               => true,
            'show_in_menu'          => true,
            'menu_position'         => 25.5,
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

    // Register Category Taxonomy for Fart Brands
    public function fc_register_fart_detail_category_taxonomy() {
        $labels = array(
            'name'              => _x( 'Fart Brand Categories', 'taxonomy general name', 'fart-calculator' ),
            'singular_name'     => _x( 'Fart Brand Category', 'taxonomy singular name', 'fart-calculator' ),
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
    // Register Tag Taxonomy for Fart Brands
    public function fc_register_fart_detail_tag_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Fart Brand Tags', 'taxonomy general name', 'fart-calculator' ),
            'singular_name'              => _x( 'Fart Brand Tag', 'taxonomy singular name', 'fart-calculator' ),
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


    // Register Fart Joke CPT// Register Fart Jokes CPT
    public function fc_register_fart_joke_cpt() {
        $labels = array(
            'name'               => _x( 'Fart Jokes', 'Post Type General Name', 'fart-calculator' ),
            'singular_name'      => _x( 'Fart Joke', 'Post Type Singular Name', 'fart-calculator' ),
            'menu_name'          => __( 'Fart Jokes', 'fart-calculator' ),
            'name_admin_bar'     => __( 'Fart Joke', 'fart-calculator' ),
            'add_new_item'       => __( 'Add New Fart Joke', 'fart-calculator' ),
            'add_new'            => __( 'Add New', 'fart-calculator' ),
            'new_item'           => __( 'New Fart Joke', 'fart-calculator' ),
            'edit_item'          => __( 'Edit Fart Joke', 'fart-calculator' ),
            'view_item'          => __( 'View Fart Joke', 'fart-calculator' ),
            'search_items'       => __( 'Search Fart Jokes', 'fart-calculator' ),
            'not_found'          => __( 'No Fart Jokes found.', 'fart-calculator' ),
            'not_found_in_trash' => __( 'No Fart Jokes found in Trash.', 'fart-calculator' ),
        );

        $args = array(
            'label'              => __( 'Fart Jokes', 'fart-calculator' ),
            'description'        => __( 'Fart Jokes submitted by users.', 'fart-calculator' ),
            'labels'             => $labels,
            'supports'           => array( 'title', 'editor' ),
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 25.4,
            'menu_icon'          => 'dashicons-smiley',
            'show_in_admin_bar'  => true,
            'can_export'         => true,
            'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'fart_joke' ),
            'capability_type'    => 'post',
        );

        register_post_type( 'fart_joke', $args );
    }
    // Register Category Taxonomy for Fart Jokes
    public function fc_register_fart_joke_category_taxonomy() {
        $labels = array(
            'name'              => _x( 'Fart Joke Categories', 'taxonomy general name', 'fart-calculator' ),
            'singular_name'     => _x( 'Fart Joke Category', 'taxonomy singular name', 'fart-calculator' ),
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
            'rewrite'           => array( 'slug' => 'fart-joke-category' ),
        );

        register_taxonomy( 'fart_joke_category', array( 'fart_joke' ), $args );
    }
    // Register Tag Taxonomy for Fart Jokes
    public function fc_register_fart_joke_tag_taxonomy() {
        $labels = array(
            'name'                       => _x( 'Fart Joke Tags', 'taxonomy general name', 'fart-calculator' ),
            'singular_name'              => _x( 'Fart Joke Tag', 'taxonomy singular name', 'fart-calculator' ),
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
            'rewrite'               => array( 'slug' => 'fart-joke-tag' ),
        );

        register_taxonomy( 'fart_joke_tag', array( 'fart_joke' ), $args );
    }




    /**
     * Adds Meta Boxes for Fart Brands.
     */
    public function fc_add_fart_detail_meta_boxes() {
        // Volume Meta Box
        add_meta_box(
            'fc_fart_volume',
            __( 'Fart Volume', 'fart-calculator' ),
            array( $this, 'fc_fart_volume_callback' ),
            'fart_detail',
            'side',
            'default'
        );

        // Smell Meta Box
        add_meta_box(
            'fc_fart_smell',
            __( 'Fart Smell', 'fart-calculator' ),
            array( $this, 'fc_fart_smell_callback' ),
            'fart_detail',
            'normal',
            'default'
        );

        // Duration Meta Box
        add_meta_box(
            'fc_fart_duration',
            __( 'Fart Duration (seconds)', 'fart-calculator' ),
            array( $this, 'fc_fart_duration_callback' ),
            'fart_detail',
            'normal',
            'default'
        );
    }

    /**
     * Callback for Volume Meta Box.
     */
    public function fc_fart_volume_callback( $post ) {
        // Add a nonce field for security
        wp_nonce_field( 'fc_save_fart_detail', 'fc_fart_detail_nonce' );

        $value = get_post_meta( $post->ID, '_fc_fart_volume', true );

        echo '<label for="fc_fart_volume_field">';
        _e( 'Select the volume of the fart:', 'fart-calculator' );
        echo '</label> ';
        echo '<select name="fc_fart_volume_field" id="fc_fart_volume_field">';
        $options = array( 'High', 'Medium', 'Low' );
        foreach ( $options as $option ) {
            echo '<option value="' . esc_attr( $option ) . '"' . selected( $value, $option, false ) . '>' . esc_html( $option ) . '</option>';
        }
        echo '</select>';
    }

    /**
     * Callback for Smell Meta Box.
     */
    public function fc_fart_smell_callback( $post ) {
        $value = get_post_meta( $post->ID, '_fc_fart_smell', true );

        echo '<label for="fc_fart_smell_field">';
        _e( 'Describe the smell of the fart:', 'fart-calculator' );
        echo '</label> ';
        echo '<input type="text" id="fc_fart_smell_field" name="fc_fart_smell_field" value="' . esc_attr( $value ) . '" size="25" />';
    }

    /**
     * Callback for Duration Meta Box.
     */
    public function fc_fart_duration_callback( $post ) {
        $value = get_post_meta( $post->ID, '_fc_fart_duration', true );

        echo '<label for="fc_fart_duration_field">';
        _e( 'Enter the duration of the fart in seconds:', 'fart-calculator' );
        echo '</label> ';
        echo '<input type="number" id="fc_fart_duration_field" name="fc_fart_duration_field" value="' . esc_attr( $value ) . '" min="1" />';
    }

    /**
     * Saves the Meta Box Data.
     */
    public function fc_save_fart_detail_meta_boxes( $post_id ) {
        // Check if nonce is set
        if ( ! isset( $_POST['fc_fart_detail_nonce'] ) ) {
            return;
        }

        // Verify the nonce
        if ( ! wp_verify_nonce( $_POST['fc_fart_detail_nonce'], 'fc_save_fart_detail' ) ) {
            return;
        }

        // Check if it's an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        // Check user permissions
        if ( isset( $_POST['post_type'] ) && 'fart_detail' == $_POST['post_type'] ) {
            if ( ! current_user_can( 'edit_post', $post_id ) ) {
                return;
            }
        }

        // Save Volume
        if ( isset( $_POST['fc_fart_volume_field'] ) ) {
            $volume = sanitize_text_field( $_POST['fc_fart_volume_field'] );
            update_post_meta( $post_id, '_fc_fart_volume', $volume );
        }

        // Save Smell
        if ( isset( $_POST['fc_fart_smell_field'] ) ) {
            $smell = sanitize_text_field( $_POST['fc_fart_smell_field'] );
            update_post_meta( $post_id, '_fc_fart_smell', $smell );
        }

        // Save Duration
        if ( isset( $_POST['fc_fart_duration_field'] ) ) {
            $duration = intval( $_POST['fc_fart_duration_field'] );
            update_post_meta( $post_id, '_fc_fart_duration', $duration );
        }
    }

    // Add Meta Box for Joke Ratings
    public function fc_add_fart_joke_meta_boxes(): void {
        add_meta_box(
            id: 'fc_fart_joke_rating',
            title: __( 'Fart Joke Rating', 'fart-calculator' ),
            callback: array( $this, 'fc_fart_joke_rating_callback' ),
            screen: 'fart_joke',
            context: 'side',
            priority: 'high'
        );
    }

    // Meta Box Callback
    // Update the Fart Joke Rating Meta Box
    public function fc_fart_joke_rating_callback( $post ) {
        // Add nonce field for security
        wp_nonce_field( 'fc_save_fart_joke_rating', 'fc_fart_joke_rating_nonce' );

        // Retrieve the current upvote and downvote values
        $upvotes   = get_post_meta( $post->ID, '_fc_fart_joke_upvotes', true );
        $downvotes = get_post_meta( $post->ID, '_fc_fart_joke_downvotes', true );

        // Set default values if they don't exist
        $upvotes   = $upvotes ? $upvotes : 0;
        $downvotes = $downvotes ? $downvotes : 0;

        // Display fields for upvotes and downvotes
        echo '<label for="fc_fart_joke_upvotes_field">' . __( 'Upvotes:', 'fart-calculator' ) . '</label> ';
        echo '<input type="number" name="fc_fart_joke_upvotes_field" value="' . esc_attr( $upvotes ) . '" size="25" /><br><br>';

        echo '<label for="fc_fart_joke_downvotes_field">' . __( 'Downvotes:', 'fart-calculator' ) . '</label> ';
        echo '<input type="number" name="fc_fart_joke_downvotes_field" value="' . esc_attr( $downvotes ) . '" size="25" />';
    }


   // Save Meta Box Data for Upvotes and Downvotes
public function fc_save_fart_joke_meta_boxes( $post_id ) {
    // Check if nonce is set
    if ( ! isset( $_POST['fc_fart_joke_rating_nonce'] ) ) {
        return;
    }

    // Verify the nonce for security
    if ( ! wp_verify_nonce( $_POST['fc_fart_joke_rating_nonce'], 'fc_save_fart_joke_rating' ) ) {
        return;
    }

    // Check if it's an autosave
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
        return;
    }

    // Check user permissions
    if ( isset( $_POST['post_type'] ) && 'fart_joke' == $_POST['post_type'] ) {
        if ( ! current_user_can( 'edit_post', $post_id ) ) {
            return;
        }
    }

    // Save Upvotes
    if ( isset( $_POST['fc_fart_joke_upvotes_field'] ) ) {
        $upvotes = intval( $_POST['fc_fart_joke_upvotes_field'] );
        update_post_meta( $post_id, '_fc_fart_joke_upvotes', $upvotes );
    }

    // Save Downvotes
    if ( isset( $_POST['fc_fart_joke_downvotes_field'] ) ) {
        $downvotes = intval( $_POST['fc_fart_joke_downvotes_field'] );
        update_post_meta( $post_id, '_fc_fart_joke_downvotes', $downvotes );
    }
}

    // Add Meta Fields for Voting (Upvotes and Downvotes)
    function fc_add_fart_joke_votes_meta(): void {
        register_meta( object_type: 'post', meta_key: '_fc_fart_joke_upvotes', args: array(
            'type'         => 'number',
            'description'  => 'Fart Joke Upvotes',
            'single'       => true,
            'show_in_rest' => true,
        ));

        register_meta( object_type: 'post', meta_key: '_fc_fart_joke_downvotes', args: array(
            'type'         => 'number',
            'description'  => 'Fart Joke Downvotes',
            'single'       => true,
            'show_in_rest' => true,
        ));
    }

    // Enqueue the Voting script and localize the AJAX URL and nonce
    public function fc_enqueue_voting_script() {
        wp_enqueue_script( 'fc-voting-script', plugins_url( '/js/fart-voting.js', __FILE__ ), array( 'jquery' ), null, true );
    
        wp_localize_script( 'fc-voting-script', 'fc_ajax_object', array(
            'ajax_url'      => admin_url( 'admin-ajax.php' ),
            'fc_ajax_nonce' => wp_create_nonce( 'fc_vote_nonce' )  // Create the nonce
        ));
    }

     //
     public function fc_record_user_vote( $user_id, $joke_id ): void {
        // Get the user's existing votes
        $user_votes = get_user_meta( user_id: $user_id, key: '_fc_fart_joke_votes', single: true );
    
        // If no votes exist, create a new array
        if ( ! is_array( value: $user_votes ) ) {
            $user_votes = array();
        }
    
        // Add the joke ID to the list of jokes the user has voted on
        $user_votes[] = $joke_id;
    
        // Update the user meta
        update_user_meta( user_id: $user_id, meta_key: '_fc_fart_joke_votes', meta_value: $user_votes );
    }
    
    // Handle AJAX Upvote/Downvote for Fart Jokes
    function fc_handle_fart_joke_vote() {
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to vote.', 'fart-calculator' ) );
        }

        check_ajax_referer( 'fc_vote_nonce', 'nonce', false );

        $joke_id = intval( $_POST['fart_id'] );
        $vote_type = sanitize_text_field( $_POST['vote_type'] );
        $user_id = get_current_user_id();

        if ( ! in_array( $vote_type, array( 'upvote', 'downvote' ), true ) || ! $joke_id ) {
            wp_send_json_error( __( 'Invalid vote type or joke ID.', 'fart-calculator' ) );
        }

        // Check if the user has already voted on this joke
        $voted_users = get_post_meta( $joke_id, '_fc_fart_joke_voted_users', true );
        if ( ! is_array( $voted_users ) ) {
            $voted_users = array();
        }

        if ( in_array( $user_id, $voted_users ) ) {
            wp_send_json_error( __( 'You have already voted on this joke.', 'fart-calculator' ) );
        }

        // Process the vote
        $meta_key = $vote_type === 'upvote' ? '_fc_fart_joke_upvotes' : '_fc_fart_joke_downvotes';
        $current_votes = get_post_meta( $joke_id, $meta_key, true );
        $new_votes = $current_votes ? intval( $current_votes ) + 1 : 1;

        update_post_meta( $joke_id, $meta_key, $new_votes );

        // Add the user ID to the list of voters
        $voted_users[] = $user_id;
        update_post_meta( $joke_id, '_fc_fart_joke_voted_users', $voted_users );

        wp_send_json_success( __( 'Vote recorded successfully.', 'fart-calculator' ) );
    }


    /**
     * Sets Custom Columns for Fart Brands CPT.
     */
    public function fc_set_custom_fart_detail_columns( $columns ) {
        unset( $columns['date'] );
        $columns['volume']   = __( 'Volume', 'fart-calculator' );
        $columns['smell']    = __( 'Smell', 'fart-calculator' );
        $columns['duration'] = __( 'Duration (s)', 'fart-calculator' );
        $columns['date']     = __( 'Date', 'fart-calculator' );

        return $columns;
    }

    /**
     * Populates Custom Columns with Meta Data.
     */
    public function fc_custom_fart_detail_column( $column, $post_id ) {
        switch ( $column ) {
            case 'volume':
                $volume = get_post_meta( $post_id, '_fc_fart_volume', true );
                echo esc_html( $volume );
                break;

            case 'smell':
                $smell = get_post_meta( $post_id, '_fc_fart_smell', true );
                echo esc_html( $smell );
                break;

            case 'duration':
                $duration = get_post_meta( $post_id, '_fc_fart_duration', true );
                echo esc_html( $duration );
                break;
        }
    }

    // Add Meta Boxes for Voting in Fart Brands
    public function fc_add_fart_detail_votes_meta() {
        add_meta_box(
            'fc_fart_detail_votes_meta',
            __( 'Fart Brand Votes', 'fart-calculator' ),
            array( $this, 'fc_fart_detail_votes_meta_callback' ),
            'fart_detail', // Make sure this is the correct post type
            'side',
            'high'
        );
    }

    // Callback to display voting meta data in the admin
    public function fc_fart_detail_votes_meta_callback( $post ) {
        // Add a nonce field for security
        wp_nonce_field( 'fc_save_fart_detail', 'fc_fart_detail_nonce' );

        // Get current upvotes and downvotes
        $upvotes = get_post_meta( $post->ID, '_fc_fart_detail_upvotes', true );
        $downvotes = get_post_meta( $post->ID, '_fc_fart_detail_downvotes', true );

        // Display the upvotes and downvotes
        echo '<p><strong>' . __( 'Upvotes:', 'fart-calculator' ) . '</strong> ';
        echo '<input type="number" name="fc_fart_detail_upvotes" value="' . esc_attr( $upvotes ) . '" /></p>';
        echo '<p><strong>' . __( 'Downvotes:', 'fart-calculator' ) . '</strong> ';
        echo '<input type="number" name="fc_fart_detail_downvotes" value="' . esc_attr( $downvotes ) . '" /></p>';
    }

    // Save Meta Box Data
    public function fc_save_fart_detail_votes( $post_id ) {
        // Check if nonce is set
        if ( ! isset( $_POST['fc_fart_detail_nonce'] ) ) return;

        // Verify the nonce
        if ( ! wp_verify_nonce( $_POST['fc_fart_detail_nonce'], 'fc_save_fart_detail' ) ) return;

        // Check if it's an autosave
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        // Save Upvotes and Downvotes
        if ( isset( $_POST['fc_fart_detail_upvotes'] ) ) {
            update_post_meta( $post_id, '_fc_fart_detail_upvotes', intval( $_POST['fc_fart_detail_upvotes'] ) );
        }

        if ( isset( $_POST['fc_fart_detail_downvotes'] ) ) {
            update_post_meta( $post_id, '_fc_fart_detail_downvotes', intval( $_POST['fc_fart_detail_downvotes'] ) );
        }
    }
    // Handle AJAX Upvote/Downvote for Fart Brands
    public function fc_handle_fart_detail_vote() {
        // Check if the user is logged in
        if ( ! is_user_logged_in() ) {
            wp_send_json_error( __( 'You must be logged in to vote.', 'fart-calculator' ) );
        }

        if ( ! check_ajax_referer( 'fc_vote_nonce', 'nonce', false ) ) {
            wp_send_json_error( __( 'Nonce verification failed for Fart Brand.', 'fart-calculator' ) );
        }
        $detail_id = intval( $_POST['fart_id'] );
        $vote_type = sanitize_text_field( $_POST['vote_type'] );
        $user_id = get_current_user_id(); // Get the logged-in user's ID

        if ( ! in_array( $vote_type, array( 'upvote', 'downvote' ), true ) || ! $detail_id ) {
            wp_send_json_error( __( 'Invalid vote type or brand ID.', 'fart-calculator' ) );
        }

        // Check if the user has already voted on this fart brand
        $voters = get_post_meta( $detail_id, '_fc_fart_detail_voters', true );
        if ( ! is_array( $voters ) ) {
            $voters = array();
        }

        if ( in_array( $user_id, $voters ) ) {
            wp_send_json_error( __( 'You have already voted on this fart brand.', 'fart-calculator' ) );
        }

        // Record the user's vote
        $meta_key = $vote_type === 'upvote' ? '_fc_fart_detail_upvotes' : '_fc_fart_detail_downvotes';
        $current_votes = get_post_meta( $detail_id, $meta_key, true );
        $new_votes = $current_votes ? intval( $current_votes ) + 1 : 1;

        update_post_meta( $detail_id, $meta_key, $new_votes );

        // Store the user ID in the voters array to prevent multiple votes
        $voters[] = $user_id;
        update_post_meta( $detail_id, '_fc_fart_detail_voters', $voters );

        wp_send_json_success( __( 'Vote recorded successfully.', 'fart-calculator' ) );
    }



    /**
     * Enqueues the Plugin's CSS Stylesheet.
     */
    public function fc_enqueue_styles() {
        wp_enqueue_style( 'fc-fart-calculator-style', plugins_url( '/css/style.css', __FILE__ ) );
        // Enqueue jQuery for AJAX
        wp_enqueue_script( 'jquery' );
    }

    /**
     * Shortcode to Display the Fart Calculator.
     */
    public function fc_display_fart_calculator() {
        ob_start();
        ?>
        <form id="fc-fart-calculator-form" method="post">
            <label for="fc_age"><?php _e( 'Age:', 'fart-calculator' ); ?></label>
            <input type="number" name="fc_age" id="fc_age" required><br><br>

            <label for="fc_beans"><?php _e( 'Cups of Beans Consumed per Week:', 'fart-calculator' ); ?></label>
            <input type="number" name="fc_beans" id="fc_beans" required><br><br>

            <label for="fc_broccoli"><?php _e( 'Servings of Broccoli per Week:', 'fart-calculator' ); ?></label>
            <input type="number" name="fc_broccoli" id="fc_broccoli" required><br><br>

            <label for="fc_soda"><?php _e( 'Cans of Soda Consumed per Week:', 'fart-calculator' ); ?></label>
            <input type="number" name="fc_soda" id="fc_soda" required><br><br>

            <input type="submit" name="fc_calculate" value="<?php _e( 'Calculate', 'fart-calculator' ); ?>">
        </form>

        <?php
        if ( isset( $_POST['fc_calculate'] ) ) {
            $age        = intval( $_POST['fc_age'] );
            $beans      = intval( $_POST['fc_beans'] );
            $broccoli   = intval( $_POST['fc_broccoli'] );
            $soda       = intval( $_POST['fc_soda'] );

            // Example formula: base calculation
            $base_fart_frequency = ($beans * 2) + ($broccoli * 1.5) + ($soda * 1.2) + ($age * 0.1);
            $fart_frequency      = $base_fart_frequency; // No Fart Type adjustment

            // Display the results
            echo '<div class="fc-calculation-results">';
            echo '<h2>' . __( 'Estimated Fart Frequency:', 'fart-calculator' ) . ' ' . round( $fart_frequency ) . ' ' . __( 'times per week', 'fart-calculator' ) . '</h2>';
            echo '</div>';
        }

        return ob_get_clean();
    }

    /**
     * Shortcode to Display a List of Fart Brands.
     */
   public function fc_display_fart_details_list( $atts ) {
    ob_start();

    // Attributes and defaults
    $atts = shortcode_atts( array(
        'columns' => '3',
    ), $atts, 'fart_details_list' );

    // Query Fart Brands
    $fart_details = get_posts( array(
        'post_type'      => 'fart_detail',
        'posts_per_page' => -1,
        'orderby'        => 'date',
        'order'          => 'DESC',
    ) );

    if ( ! empty( $fart_details ) ) {
        echo '<div class="fc-fart-details-list">';
        foreach ( $fart_details as $fart ) {
            $volume    = get_post_meta( $fart->ID, '_fc_fart_volume', true );
            $smell     = get_post_meta( $fart->ID, '_fc_fart_smell', true );
            $duration  = get_post_meta( $fart->ID, '_fc_fart_duration', true );
            $fart_link = get_permalink( $fart->ID );
            $fart_description = get_post_field('post_content', $fart->ID);
            $fart_date = get_the_date( 'F j, Y', $fart->ID );

            // Improved HTML structure
            echo '<div class="fc-fart-detail">';
            echo '<h3><a href="' . esc_url( $fart_link ) . '">' . esc_html( get_the_title( $fart->ID ) ) . '</a></h3>';
            echo '<p class="fc-fart-date"><strong>' . __( 'Date:', 'fart-calculator' ) . '</strong> ' . esc_html( $fart_date ) . '</p>';
            echo '<p class="fc-fart-volume"><strong>' . __( 'Volume:', 'fart-calculator' ) . '</strong> ' . esc_html( $volume ) . '</p>';
            echo '<p class="fc-fart-smell"><strong>' . __( 'Smell:', 'fart-calculator' ) . '</strong> ' . esc_html( $smell ) . '</p>';
            echo '<p class="fc-fart-duration"><strong>' . __( 'Duration:', 'fart-calculator' ) . '</strong> ' . esc_html( $duration ) . ' ' . __( 'seconds', 'fart-calculator' ) . '</p>';
            echo '<p class="fc-fart-description"><strong>' . __( 'Description:', 'fart-calculator' ) . '</strong> ' . esc_html( $fart_description ) . '</p>';
            echo '</div>';
        }
        echo '</div>';
        }

        return ob_get_clean();
    }


    /**
     * Shortcode to Display Front-End Fart Brand Submission Form.
     */
    public function fc_display_submission_form() {
        // Only show the form to logged-in users (optional)
        // Uncomment the following lines if you want to restrict to logged-in users
        
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You need to be logged in to submit a Fart Brand.', 'fart-calculator' ) . '</p>';
        }
    

        ob_start();

        // Handle form submission
        if ( isset( $_POST['fc_fart_detail_submit'] ) ) {
            // Verify nonce
            if ( ! isset( $_POST['fc_fart_detail_nonce'] ) || ! wp_verify_nonce( $_POST['fc_fart_detail_nonce'], 'fc_submit_fart_detail' ) ) {
                echo '<p style="color:red;">' . __( 'Security check failed. Please try again.', 'fart-calculator' ) . '</p>';
            } else {
                // Sanitize and validate input
                $title    = sanitize_text_field( $_POST['fc_fart_title'] );
                $volume   = sanitize_text_field( $_POST['fc_fart_volume'] );
                $smell    = sanitize_text_field( $_POST['fc_fart_smell'] );
                $duration = intval( $_POST['fc_fart_duration'] );
                $content  = sanitize_textarea_field( $_POST['fc_fart_content'] );

                // Basic validation
                $errors = array();
                if ( empty( $title ) ) {
                    $errors[] = __( 'Title is required.', 'fart-calculator' );
                }
                if ( empty( $volume ) || ! in_array( $volume, array( 'High', 'Medium', 'Low' ), true ) ) {
                    $errors[] = __( 'Please select a valid volume.', 'fart-calculator' );
                }
                if ( empty( $smell ) ) {
                    $errors[] = __( 'Smell description is required.', 'fart-calculator' );
                }
                if ( empty( $duration ) || $duration <= 0 ) {
                    $errors[] = __( 'Please enter a valid duration in seconds.', 'fart-calculator' );
                }

                if ( ! empty( $errors ) ) {
                    echo '<div class="fc-submission-errors" style="color:red;"><ul>';
                    foreach ( $errors as $error ) {
                        echo '<li>' . esc_html( $error ) . '</li>';
                    }
                    echo '</ul></div>';
                } else {
                    // Create new Fart Brand post
                    $new_fart = array(
                        'post_title'   => $title,
                        'post_content' => $content,
                        'post_status'  => 'pending', // Change to 'publish' if you want to auto-publish
                        'post_type'    => 'fart_detail',
                    );

                    $post_id = wp_insert_post( $new_fart );

                    if ( ! is_wp_error( $post_id ) ) {
                        // Save meta fields
                        update_post_meta( $post_id, '_fc_fart_volume', $volume );
                        update_post_meta( $post_id, '_fc_fart_smell', $smell );
                        update_post_meta( $post_id, '_fc_fart_duration', $duration );

                        echo '<p class="fc-submission-success" style="color:green;">' . __( 'Thank you! Your Fart Brand has been submitted and is awaiting approval.', 'fart-calculator' ) . '</p>';
                    } else {
                        echo '<p class="fc-submission-error" style="color:red;">' . __( 'An error occurred while submitting your Fart Brand. Please try again.', 'fart-calculator' ) . '</p>';
                    }
                }
            }
        }

        // Display the form
        ?>
        <form id="fc-fart-detail-form" method="post">
            <?php wp_nonce_field( 'fc_submit_fart_detail', 'fc_fart_detail_nonce' ); ?>
            
            <label for="fc_fart_title"><?php _e( 'Fart Title:', 'fart-calculator' ); ?></label>
            <input type="text" id="fc_fart_title" name="fc_fart_title" required><br><br>
            
            <label for="fc_fart_volume"><?php _e( 'Volume:', 'fart-calculator' ); ?></label>
            <select id="fc_fart_volume" name="fc_fart_volume" required>
                <option value="High"><?php _e( 'High', 'fart-calculator' ); ?></option>
                <option value="Medium"><?php _e( 'Medium', 'fart-calculator' ); ?></option>
                <option value="Low"><?php _e( 'Low', 'fart-calculator' ); ?></option>
            </select><br><br>
            
            <label for="fc_fart_smell"><?php _e( 'Smell Description:', 'fart-calculator' ); ?></label>
            <input type="text" id="fc_fart_smell" name="fc_fart_smell" required><br><br>
            
            <label for="fc_fart_duration"><?php _e( 'Duration (seconds):', 'fart-calculator' ); ?></label>
            <input type="number" id="fc_fart_duration" name="fc_fart_duration" min="1" required><br><br>
            
            <label for="fc_fart_content"><?php _e( 'Fart Description:', 'fart-calculator' ); ?></label>
            <textarea id="fc_fart_content" name="fc_fart_content" rows="5" required></textarea><br><br>
            
            <input type="submit" name="fc_fart_detail_submit" value="<?php _e( 'Submit Fart Brand', 'fart-calculator' ); ?>">
        </form>
        <?php

        return ob_get_clean();
    }

    /**
     * Shortcode to Display the Fart Ranker.
     */
    public function fc_display_fart_ranker() {
        ob_start();

        // Query Fart Brands with average ratings
        $fart_details = get_posts( array(
            'post_type'      => 'fart_detail',
            'posts_per_page' => -1,
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_fc_fart_average_rating',
            'order'          => 'DESC',
        ) );

        if ( ! empty( $fart_details ) ) {
            echo '<div class="fc-fart-ranker">';
            foreach ( $fart_details as $fart ) {
                $volume    = get_post_meta( $fart->ID, '_fc_fart_volume', true );
                $smell     = get_post_meta( $fart->ID, '_fc_fart_smell', true );
                $duration  = get_post_meta( $fart->ID, '_fc_fart_duration', true );
                $fart_link = get_permalink( $fart->ID );

                // Get average rating
                $average_rating = get_post_meta( $fart->ID, '_fc_fart_average_rating', true );
                $average_rating = $average_rating ? floatval( $average_rating ) : 0;

                echo '<div class="fc-fart-ranker-item">';
                echo '<h3>' . esc_html( get_the_title( $fart->ID ) ) . '</h3>';
                echo '<p><strong>' . __( 'Volume:', 'fart-calculator' ) . '</strong> ' . esc_html( $volume ) . '</p>';
                echo '<p><strong>' . __( 'Smell:', 'fart-calculator' ) . '</strong> ' . esc_html( $smell ) . '</p>';
                echo '<p><strong>' . __( 'Duration:', 'fart-calculator' ) . '</strong> ' . esc_html( $duration ) . ' ' . __( 'seconds', 'fart-calculator' ) . '</p>';
                echo '<p><strong>' . __( 'Average Rating:', 'fart-calculator' ) . '</strong> ' . number_format( $average_rating, 1 ) . ' / 5</p>';
                echo '<button class="fc-rate-button" data-fart-id="' . esc_attr( $fart->ID ) . '">' . __( 'Rate This Fart', 'fart-calculator' ) . '</button>';
                echo '</div>';
            }
            echo '</div>';

            // Add Rating Modal (hidden by default)
            ?>
            <div id="fc-rating-modal" style="display:none;">
                <div class="fc-rating-content">
                    <span class="fc-close-modal">&times;</span>
                    <h2><?php _e( 'Rate This Fart', 'fart-calculator' ); ?></h2>
                    <form id="fc-rating-form">
                        <?php wp_nonce_field( 'fc_submit_fart_rating', 'fc_fart_rating_nonce' ); ?>
                        <input type="hidden" id="fc_fart_id" name="fc_fart_id" value="">
                        <label for="fc_rating"><?php _e( 'Your Rating (1-5):', 'fart-calculator' ); ?></label>
                        <select id="fc_rating" name="fc_rating" required>
                            <option value="1"><?php _e( '1', 'fart-calculator' ); ?></option>
                            <option value="2"><?php _e( '2', 'fart-calculator' ); ?></option>
                            <option value="3"><?php _e( '3', 'fart-calculator' ); ?></option>
                            <option value="4"><?php _e( '4', 'fart-calculator' ); ?></option>
                            <option value="5"><?php _e( '5', 'fart-calculator' ); ?></option>
                        </select><br><br>
                        <input type="submit" value="<?php _e( 'Submit Rating', 'fart-calculator' ); ?>">
                    </form>
                    <div id="fc-rating-response"></div>
                </div>
            </div>

            <script>
                jQuery(document).ready(function($) {
                    // Open Modal
                    $('.fc-rate-button').on('click', function() {
                        var fartId = $(this).data('fart-id');
                        $('#fc_fart_id').val(fartId);
                        $('#fc-rating-modal').fadeIn();
                    });

                    // Close Modal
                    $('.fc-close-modal').on('click', function() {
                        $('#fc-rating-modal').fadeOut();
                        $('#fc-rating-response').html('');
                        $('#fc_rating').val('1');
                    });

                    // Handle Rating Form Submission
                    $('#fc-rating-form').on('submit', function(e) {
                        e.preventDefault();
                        var fartId = $('#fc_fart_id').val();
                        var rating = $('#fc_rating').val();
                        var nonce = $('#fc_fart_rating_nonce').val();

                        $.ajax({
                            url: '<?php echo admin_url('admin-ajax.php'); ?>',
                            type: 'POST',
                            data: {
                                action: 'fc_submit_fart_rating',
                                fart_id: fartId,
                                rating: rating,
                                fc_fart_rating_nonce: nonce
                            },
                            success: function(response) {
                                if(response.success) {
                                    $('#fc-rating-response').html('<p style="color:green;">' + response.data + '</p>');
                                    // Optionally, refresh the average rating displayed
                                    setTimeout(function(){
                                        location.reload();
                                    }, 1000);
                                } else {
                                    $('#fc-rating-response').html('<p style="color:red;">' + response.data + '</p>');
                                }
                            }
                        });
                    });
                });
            </script>

            <?php
        } else {
            echo '<p>' . __( 'No Fart Brands found. Please add some from the admin panel.', 'fart-calculator' ) . '</p>';
        }

        return ob_get_clean();
    }

    // Shortcode to display fart jokes
    public function fc_display_fart_jokes() {
        ob_start();
    
        $fart_jokes = new WP_Query( array(
            'post_type'      => 'fart_joke',
            'posts_per_page' => -1,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
    
        if ( $fart_jokes->have_posts() ) {
            echo '<div class="fc-fart-jokes-list">'; // Container for all jokes
            while ( $fart_jokes->have_posts() ) {
                $fart_jokes->the_post();
                $upvotes   = get_post_meta( get_the_ID(), '_fc_fart_joke_upvotes', true );
                $downvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_downvotes', true );
    
                // Default values if upvotes/downvotes aren't set yet
                $upvotes   = $upvotes ? $upvotes : 0;
                $downvotes = $downvotes ? $downvotes : 0;
    
                // Output the joke with some HTML structure for styling
                echo '<div class="fc-fart-joke-item">'; // Individual joke container
                echo '<h3 class="fc-fart-joke-title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>'; // Title with link
                echo '<div class="fc-fart-joke-excerpt">' . esc_html( get_the_excerpt() ) . '</div>'; // Excerpt for a brief preview
                echo '<div class="fc-fart-joke-votes">';
                echo '<span class="fc-fart-joke-upvotes"><strong>👍 Upvotes:</strong> ' . esc_html( $upvotes ) . '</span>';
                echo '<span class="fc-fart-joke-downvotes"><strong>👎 Downvotes:</strong> ' . esc_html( $downvotes ) . '</span>';
                echo '</div>';
                echo '</div>'; // End of individual joke container
            }
            echo '</div>'; // End of jokes list container
        } else {
            echo '<p>' . __( 'No jokes found.', 'fart-calculator' ) . '</p>';
        }
    
        return ob_get_clean();
    }
    
    //Adds a default upvote value of 0 to fart jokes
    function fc_initialize_fart_joke_upvotes( $post_id, $post, $update ): void {
        if ( $post->post_type == 'fart_joke' && ! get_post_meta( post_id: $post_id, key: '_fc_fart_joke_upvotes', single: true ) ) {
            update_post_meta( post_id: $post_id, meta_key: '_fc_fart_joke_upvotes', meta_value: 1 ); // Initialize upvotes if not set
        }
    }
    // Shortcode for frontend joke submission
    public function fc_fart_joke_submission_form() {
        ob_start();

        if ( isset( $_POST['fc_fart_joke_submit'] ) ) {
            // Nonce check
            if ( ! isset( $_POST['fc_fart_joke_nonce'] ) || ! wp_verify_nonce( $_POST['fc_fart_joke_nonce'], 'fc_submit_fart_joke' ) ) {
                echo '<p style="color:red;">' . __( 'Security check failed. Please try again.', 'fart-calculator' ) . '</p>';
            } else {
                // Sanitize and submit post
                $title   = sanitize_text_field( $_POST['fc_fart_joke_title'] );
                $content = sanitize_textarea_field( $_POST['fc_fart_joke_content'] );

                // Create new post
                $new_joke = array(
                    'post_title'   => $title,
                    'post_content' => $content,
                    'post_status'  => 'pending',
                    'post_type'    => 'fart_joke',
                );

                $post_id = wp_insert_post( $new_joke );

                if ( ! is_wp_error( $post_id ) ) {
                    echo '<p style="color:green;">' . __( 'Thank you! Your joke has been submitted and is awaiting approval.', 'fart-calculator' ) . '</p>';
                } else {
                    echo '<p style="color:red;">' . __( 'There was an error submitting your joke. Please try again.', 'fart-calculator' ) . '</p>';
                }
            }
        }

        ?>
        <form method="post">
            <?php wp_nonce_field( 'fc_submit_fart_joke', 'fc_fart_joke_nonce' ); ?>
            <label for="fc_fart_joke_title"><?php _e( 'Joke Title', 'fart-calculator' ); ?></label>
            <input type="text" id="fc_fart_joke_title" name="fc_fart_joke_title" required><br><br>

            <label for="fc_fart_joke_content"><?php _e( 'Joke Content', 'fart-calculator' ); ?></label>
            <textarea id="fc_fart_joke_content" name="fc_fart_joke_content" rows="5" required></textarea><br><br>
            <input type="submit" name="fc_fart_joke_submit" value="<?php _e( 'Submit Fart Joke', 'fart-calculator' ); ?>">
        </form>
        <?php

        return ob_get_clean();
    }
    // Check if the user has already voted on a joke
    function fc_user_has_voted( $user_id, $joke_id ): bool {
        // Get the user's votes meta field
        $user_votes = get_user_meta( $user_id, '_fc_fart_joke_votes', true );
    
        // Check if the user has already voted for this joke
        if ( is_array( $user_votes ) && in_array( $joke_id, $user_votes ) ) {
            return true; // User has already voted
        }
    
        return false; // User has not voted
    }
    
   
    // Shortcode to Display Fart Joke Leaderboard
    public function fc_fart_joke_leaderboard() {
        ob_start();
    
        // Query for top fart jokes
        $top_jokes = new WP_Query( array(
            'post_type'      => 'fart_joke',
            'posts_per_page' => 10, // Show top 10
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_fc_fart_joke_upvotes',
            'order'          => 'DESC',
            'post_status'    => 'publish',
        ) );
    
        if ( $top_jokes->have_posts() ) {
            echo '<div class="fc-fart-joke-leaderboard">';
            echo '<h2 class="fc-leaderboard-title">' . __( 'Top 10 Fart Jokes', 'fart-calculator' ) . '</h2>';
            echo '<ul class="fc-leaderboard-list">';
            while ( $top_jokes->have_posts() ) {
                $top_jokes->the_post();
                $upvotes   = get_post_meta( get_the_ID(), '_fc_fart_joke_upvotes', true );
                $downvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_downvotes', true );
    
                // Default values if not set
                $upvotes   = $upvotes ? $upvotes : 0;
                $downvotes = $downvotes ? $downvotes : 0;
    
                echo '<li class="fc-leaderboard-item">';
                echo '<h3 class="fc-joke-title"><a href="' . esc_url( get_permalink() ) . '">' . esc_html( get_the_title() ) . '</a></h3>';
                echo '<div class="fc-joke-votes">';
                echo '<span class="fc-upvotes"><strong>👍 Upvotes:</strong> ' . esc_html( $upvotes ) . '</span>';
                echo '<span class="fc-downvotes"><strong>👎 Downvotes:</strong> ' . esc_html( $downvotes ) . '</span>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
            echo '</div>';
        } else {
            echo '<p>' . __( 'No jokes found.', 'fart-calculator' ) . '</p>';
        }
    
        return ob_get_clean();
    }

    /**
     * Handles AJAX Submission of Fart Ratings.
     */
    public function fc_handle_fart_rating() {
        // Check nonce
        check_ajax_referer( 'fc_submit_fart_rating', 'fc_fart_rating_nonce' );

        // Get and sanitize input
        $fart_id = intval( $_POST['fart_id'] );
        $rating  = intval( $_POST['rating'] );

        // Validate rating
        if ( $rating < 1 || $rating > 5 ) {
            wp_send_json_error( __( 'Invalid rating value.', 'fart-calculator' ) );
        }

        // Check if Fart Detail exists
        $fart = get_post( $fart_id );
        if ( ! $fart || $fart->post_type !== 'fart_detail' ) {
            wp_send_json_error( __( 'Fart Brand not found.', 'fart-calculator' ) );
        }

        // Get existing ratings
        $ratings = get_post_meta( $fart_id, '_fc_fart_ratings', true );
        if ( ! is_array( $ratings ) ) {
            $ratings = array();
        }

        // Add new rating
        $ratings[] = $rating;
        update_post_meta( $fart_id, '_fc_fart_ratings', $ratings );

        // Calculate new average rating
        $average = array_sum( $ratings ) / count( $ratings );
        update_post_meta( $fart_id, '_fc_fart_average_rating', $average );

        wp_send_json_success( __( 'Thank you for rating!', 'fart-calculator' ) );
    }

    /**
     * Loads the Custom Templates for Single Fart Brand and Single Fart Joke.
     */
    public function fc_load_fart_detail_template( $template ) {
        if ( is_singular( 'fart_detail' ) ) {
            // Check if the template exists in the theme first
            $theme_template = locate_template( array( 'single-fart_detail.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            } else {
                return plugin_dir_path( __FILE__ ) . 'templates/single-fart_detail.php';
            }
        } elseif ( is_singular( 'fart_joke' ) ) {
            // Check if a custom template for fart jokes exists in the theme
            $theme_template = locate_template( array( 'single-fart_joke.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            } else {
                return plugin_dir_path( __FILE__ ) . 'templates/single-fart_joke.php';
            }
        }
        return $template;
    }

    function fc_register_fart_rest_api_routes() {
        // Route for getting all fart brands
        register_rest_route( 'fart-calculator/v1', '/farts/', array(
            'methods' => 'GET',
            'callback' => 'fc_get_all_fart_details',
            'permission_callback' => '__return_true', // No authentication required
        ) );

        // Route for getting a single fart brand by ID
        register_rest_route( 'fart-calculator/v1', '/farts/(?P<id>\d+)', array(
            'methods' => 'GET',
            'callback' => 'fc_get_single_fart_detail',
            'permission_callback' => '__return_true', // No authentication required
        ) );

        // Route for submitting a vote (upvote/downvote)
        register_rest_route( 'fart-calculator/v1', '/farts/(?P<id>\d+)/vote/', array(
            'methods' => 'POST',
            'callback' => 'fc_submit_fart_vote',
            'permission_callback' => 'fc_validate_vote_request', // Optionally require authentication
        ) );
    }


} // End of Fart_Calculator class

// Initialize the plugin
new Fart_Calculator();

/**
 * Flush rewrite rules on plugin activation.
 */
register_activation_hook( __FILE__, 'fc_flush_rewrite_rules' );

function fc_flush_rewrite_rules() {
    // Trigger CPT registration
    $fart_calculator = new Fart_Calculator();
    $fart_calculator->fc_register_fart_detail_cpt();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}

/**
 * Flush rewrite rules on plugin deactivation.
 */
register_deactivation_hook( __FILE__, 'fc_deactivate_flush_rewrite_rules' );

function fc_deactivate_flush_rewrite_rules() {
    flush_rewrite_rules();
}
endif; // End of class_exists check


//Todo:
//Create a fart analytics based on fart calculator submissions 