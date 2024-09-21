<?php
/**
 * Plugin Name: Fart Calculator
 * Description: A fun calculator that estimates fart frequency based on user inputs, with detailed rankings and front-end submission.
 * Version: 1.5
 * Author: Your Name
 * License: GPL2
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Main Plugin Class
 */
if ( ! class_exists( 'Fart_Calculator' ) ) :

class Fart_Calculator {

    /**
     * Constructor: Initializes the plugin by setting up hooks.
     */
    public function __construct() {
        // Register Fart Details Custom Post Type
        add_action( 'init', array( $this, 'fc_register_fart_detail_cpt' ), 0 );
         
        // Register Fart Jokes Custom Post Type
        add_action( 'init', array( $this, 'fc_register_fart_joke_cpt' ) );
        add_action( 'init', array( $this, 'fc_add_fart_joke_votes_meta') );


        // Add Meta Boxes
        add_action( 'add_meta_boxes', array( $this, 'fc_add_fart_detail_meta_boxes' ) );
        add_action( 'add_meta_boxes', array( $this, 'fc_add_fart_joke_meta_boxes' ) );

        // Save Meta Box Data
        add_action( 'save_post', array( $this, 'fc_save_fart_detail_meta_boxes' ) );
        add_action( 'save_post', array( $this, 'fc_save_fart_joke_meta_boxes' ) );

        // Customize Admin Columns
        add_filter( 'manage_fart_detail_posts_columns', array( $this, 'fc_set_custom_fart_detail_columns' ) );
        add_action( 'manage_fart_detail_posts_custom_column' , array( $this, 'fc_custom_fart_detail_column' ), 10, 2 );

        // Enqueue Styles
        add_action( 'wp_enqueue_scripts', array( $this, 'fc_enqueue_styles' ) );

        // Shortcodes
        add_shortcode( tag: 'fart_calculator', callback: array( $this, 'fc_display_fart_calculator' ) );//[fart_calculator]
        add_shortcode( tag: 'fart_details_list', callback: array( $this, 'fc_display_fart_details_list' ) );//[fart_details_list]
        add_shortcode( tag: 'submit_fart_detail', callback: array( $this, 'fc_display_submission_form' ) );//[submit_fart_detail]
        add_shortcode( tag: 'fart_ranker', callback: array( $this, 'fc_display_fart_ranker' ) );//[fart_ranker]
        add_shortcode( tag: 'fart_jokes', callback: array( $this, 'fc_display_fart_jokes' ) );//[fart_jokes]
        add_shortcode( tag: 'submit_fart_joke', callback: array( $this, 'fc_fart_joke_submission_form' ) );//[submit_fart_joke]
        add_shortcode( tag: 'fart_joke_leaderboard', callback: array( $this, 'fc_fart_joke_leaderboard' ) );//[fart_joke_leaderboard]


        // Template Loader
        add_filter( 'template_include', array( $this, 'fc_load_fart_detail_template' ) );

        // Initialize Rating Functionality
        add_action( 'wp_ajax_fc_submit_fart_rating', array( $this, 'fc_handle_fart_rating' ) );
        add_action( 'wp_ajax_nopriv_fc_submit_fart_rating', array( $this, 'fc_handle_fart_rating' ) );
        add_action( 'wp_ajax_fc_fart_joke_vote', array( $this, 'fc_handle_fart_joke_vote' ) );
        add_action( 'wp_ajax_nopriv_fc_fart_joke_vote', array( $this, 'fc_handle_fart_joke_vote' ) );
    }

    /**
     * Registers the Custom Post Type 'fart_detail'.
     */
    public function fc_register_fart_detail_cpt() {
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

    // Register Fart Joke CPT
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
            'supports'           => array( 'title', 'editor', 'author' ),
            'public'             => true,
            'show_ui'            => true,
            'show_in_menu'       => true,
            'menu_position'      => 25,
            'menu_icon'          => 'dashicons-smiley',
            'show_in_admin_bar'  => true,
            'can_export'         => true,
            'publicly_queryable' => true,
            'rewrite'            => array( 'slug' => 'fart_joke' ),
            'capability_type'    => 'post',
        );
        register_post_type( 'fart_joke', $args );
    }


    /**
     * Adds Meta Boxes for Fart Details.
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
    public function fc_add_fart_joke_meta_boxes() {
        add_meta_box(
            'fc_fart_joke_rating',
            __( 'Fart Joke Rating', 'fart-calculator' ),
            array( $this, 'fc_fart_joke_rating_callback' ),
            'fart_joke',
            'side',
            'default'
        );
    }

    // Meta Box Callback
    public function fc_fart_joke_rating_callback( $post ) {
        wp_nonce_field( 'fc_save_fart_joke_rating', 'fc_fart_joke_rating_nonce' );

        $rating = get_post_meta( $post->ID, '_fc_fart_joke_rating', true );
        echo '<label for="fc_fart_joke_rating_field">';
        _e( 'Current Rating:', 'fart-calculator' );
        echo '</label> ';
        echo '<input type="number" name="fc_fart_joke_rating_field" value="' . esc_attr( $rating ) . '" size="25" />';
    }

    // Save Meta Box Data
    public function fc_save_fart_joke_meta_boxes( $post_id ) {
        if ( ! isset( $_POST['fc_fart_joke_rating_nonce'] ) ) return;
        if ( ! wp_verify_nonce( $_POST['fc_fart_joke_rating_nonce'], 'fc_save_fart_joke_rating' ) ) return;
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

        if ( isset( $_POST['fc_fart_joke_rating_field'] ) ) {
            $rating = sanitize_text_field( $_POST['fc_fart_joke_rating_field'] );
            update_post_meta( $post_id, '_fc_fart_joke_rating', $rating );
        }
    }

    // Add Meta Fields for Voting (Upvotes and Downvotes)
    function fc_add_fart_joke_votes_meta() {
        register_meta( 'post', '_fc_fart_joke_upvotes', array(
            'type'         => 'number',
            'description'  => 'Fart Joke Upvotes',
            'single'       => true,
            'show_in_rest' => true,
        ));

        register_meta( 'post', '_fc_fart_joke_downvotes', array(
            'type'         => 'number',
            'description'  => 'Fart Joke Downvotes',
            'single'       => true,
            'show_in_rest' => true,
        ));
    }
    // Display Voting Buttons for Each Joke
    public function fc_display_fart_joke_voting( $post_id ) {
        $upvotes   = get_post_meta( $post_id, '_fc_fart_joke_upvotes', true );
        $downvotes = get_post_meta( $post_id, '_fc_fart_joke_downvotes', true );

        $upvotes   = $upvotes ? $upvotes : 0;
        $downvotes = $downvotes ? $downvotes : 0;

        ?>
        <div class="fc-fart-joke-voting">
            <span class="fc-upvote" data-joke-id="<?php echo esc_attr( $post_id ); ?>">
                👍 <?php echo esc_html( $upvotes ); ?>
            </span>
            <span class="fc-downvote" data-joke-id="<?php echo esc_attr( $post_id ); ?>">
                👎 <?php echo esc_html( $downvotes ); ?>
            </span>
        </div>
        <script>
               jQuery(document).ready(function($) {
                $('.fc-upvote, .fc-downvote').on('click', function() {
                    var jokeId = $(this).data('joke-id');
                    var voteType = $(this).hasClass('fc-upvote') ? 'upvote' : 'downvote';

                    $.ajax({
                        url: fc_ajax_url,
                        type: 'POST',
                        data: {
                            action: 'fc_fart_joke_vote',
                            post_id: jokeId,
                            vote_type: voteType
                        },
                        success: function(response) {
                            if (response.success) {
                                location.reload(); // Optional: Reload page to update vote counts
                            } else {
                                alert(response.data);
                            }
                        }
                    });
                });
            });

        </script>
        <?php
    }

    // AJAX Upvote/Downvote Handlers
    public function fc_handle_fart_joke_vote() {
        if ( ! isset( $_POST['post_id'], $_POST['vote_type'] ) || ! is_numeric( $_POST['post_id'] ) ) {
            wp_send_json_error( __( 'Invalid request', 'fart-calculator' ) );
        }

        $post_id   = intval( $_POST['post_id'] );
        $vote_type = sanitize_text_field( $_POST['vote_type'] );

        if ( $vote_type === 'upvote' ) {
            $upvotes = get_post_meta( $post_id, '_fc_fart_joke_upvotes', true );
            $upvotes = $upvotes ? $upvotes + 1 : 1;
            update_post_meta( $post_id, '_fc_fart_joke_upvotes', $upvotes );
            wp_send_json_success( __( 'Upvote counted', 'fart-calculator' ) );
        } elseif ( $vote_type === 'downvote' ) {
            $downvotes = get_post_meta( $post_id, '_fc_fart_joke_downvotes', true );
            $downvotes = $downvotes ? $downvotes + 1 : 1;
            update_post_meta( $post_id, '_fc_fart_joke_downvotes', $downvotes );
            wp_send_json_success( __( 'Downvote counted', 'fart-calculator' ) );
        }

        wp_send_json_error( __( 'Invalid vote type', 'fart-calculator' ) );
    }



    /**
     * Sets Custom Columns for Fart Details CPT.
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
     * Shortcode to Display a List of Fart Details.
     */
   public function fc_display_fart_details_list( $atts ) {
    ob_start();

    // Attributes and defaults
    $atts = shortcode_atts( array(
        'columns' => '3',
    ), $atts, 'fart_details_list' );

    // Query Fart Details
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
     * Shortcode to Display Front-End Fart Detail Submission Form.
     */
    public function fc_display_submission_form() {
        // Only show the form to logged-in users (optional)
        // Uncomment the following lines if you want to restrict to logged-in users
        /*
        if ( ! is_user_logged_in() ) {
            return '<p>' . __( 'You need to be logged in to submit a Fart Detail.', 'fart-calculator' ) . '</p>';
        }
        */

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
                    // Create new Fart Detail post
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

                        echo '<p class="fc-submission-success" style="color:green;">' . __( 'Thank you! Your Fart Detail has been submitted and is awaiting approval.', 'fart-calculator' ) . '</p>';
                    } else {
                        echo '<p class="fc-submission-error" style="color:red;">' . __( 'An error occurred while submitting your Fart Detail. Please try again.', 'fart-calculator' ) . '</p>';
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
            
            <input type="submit" name="fc_fart_detail_submit" value="<?php _e( 'Submit Fart Detail', 'fart-calculator' ); ?>">
        </form>
        <?php

        return ob_get_clean();
    }

    /**
     * Shortcode to Display the Fart Ranker.
     */
    public function fc_display_fart_ranker() {
        ob_start();

        // Query Fart Details with average ratings
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
            echo '<p>' . __( 'No Fart Details found. Please add some from the admin panel.', 'fart-calculator' ) . '</p>';
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
            echo '<div class="fc-fart-jokes-list">';
            while ( $fart_jokes->have_posts() ) {
                $fart_jokes->the_post();
                $rating = get_post_meta( get_the_ID(), '_fc_fart_joke_rating', true );

                echo '<div class="fc-fart-joke">';
                echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
                echo '<p>' . esc_html( get_the_content() ) . '</p>';
                echo '<p><strong>' . __( 'Rating:', 'fart-calculator' ) . '</strong> ' . esc_html( $rating ) . '</p>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>' . __( 'No Fart Jokes found. Be the first to submit one!', 'fart-calculator' ) . '</p>';
        }

        return ob_get_clean();
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

    // Shortcode to Display Fart Joke Leaderboard
    public function fc_fart_joke_leaderboard() {
        ob_start();

        $top_jokes = new WP_Query( array(
            'post_type'      => 'fart_joke',
            'posts_per_page' => 10,
            'orderby'        => 'meta_value_num',
            'meta_key'       => '_fc_fart_joke_upvotes',
            'order'          => 'DESC',
        ) );

        if ( $top_jokes->have_posts() ) {
            echo '<div class="fc-fart-joke-leaderboard">';
            echo '<h2>' . __( 'Top 10 Fart Jokes', 'fart-calculator' ) . '</h2>';
            while ( $top_jokes->have_posts() ) {
                $top_jokes->the_post();
                $upvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_upvotes', true );
                echo '<div class="fc-fart-joke">';
                echo '<h3>' . esc_html( get_the_title() ) . '</h3>';
                echo '<p><strong>' . __( 'Upvotes:', 'fart-calculator' ) . '</strong> ' . esc_html( $upvotes ) . '</p>';
                echo '</div>';
            }
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
            wp_send_json_error( __( 'Fart Detail not found.', 'fart-calculator' ) );
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
     * Loads the Custom Template for Single Fart Detail.
     */
    public function fc_load_fart_detail_template( $template ) {
        if ( is_singular( 'fart_detail' ) ) {
            // Check if the template exists in the theme first
            $theme_template = locate_template( array( 'single-fart_detail.php' ) );
            if ( $theme_template ) {
                return $theme_template;
            } else {
                // Use the plugin's template
                return plugin_dir_path( __FILE__ ) . 'templates/single-fart_detail.php';
            }
        }
        return $template;
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
?>
