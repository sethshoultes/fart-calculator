<?php
/*Testing the Endpoints
You can test the endpoints using tools like Postman or directly in the browser for the GET requests. For example:

To get all farts: https://your-site.com/wp-json/fart-calculator/v1/farts/
To get a specific fart detail: https://your-site.com/wp-json/fart-calculator/v1/farts/123
*/

// Register the REST API routes
add_action( 'rest_api_init', 'fc_register_fart_rest_api_routes' );

function fc_register_fart_rest_api_routes() {
    // Route for getting all fart details
    register_rest_route( 'fart-calculator/v1', '/farts/', array(
        'methods' => 'GET',
        'callback' => 'fc_get_all_fart_details',
        'permission_callback' => '__return_true', // No authentication required
    ) );

    // Route for getting a single fart detail by ID
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

function fc_get_all_fart_details( $request ) {
    $args = array(
        'post_type' => 'fart_detail',
        'posts_per_page' => -1,
        'post_status' => 'publish',
    );
    $fart_details = get_posts( $args );

    $data = array();
    foreach ( $fart_details as $fart ) {
        $data[] = array(
            'id' => $fart->ID,
            'title' => $fart->post_title,
            'content' => $fart->post_content,
            'upvotes' => get_post_meta( $fart->ID, '_fc_fart_detail_upvotes', true ),
            'downvotes' => get_post_meta( $fart->ID, '_fc_fart_detail_downvotes', true ),
        );
    }

    return new WP_REST_Response( $data, 200 );
}

function fc_get_single_fart_detail( $request ) {
    $fart_id = $request['id'];

    $fart = get_post( $fart_id );
    if ( empty( $fart ) || $fart->post_type !== 'fart_detail' ) {
        return new WP_Error( 'no_fart', 'Fart detail not found', array( 'status' => 404 ) );
    }

    $data = array(
        'id' => $fart->ID,
        'title' => $fart->post_title,
        'content' => $fart->post_content,
        'upvotes' => get_post_meta( $fart->ID, '_fc_fart_detail_upvotes', true ),
        'downvotes' => get_post_meta( $fart->ID, '_fc_fart_detail_downvotes', true ),
    );

    return new WP_REST_Response( $data, 200 );
}

function fc_submit_fart_vote( $request ) {
    $fart_id = $request['id'];
    $vote_type = $request->get_param( 'vote_type' ); // 'upvote' or 'downvote'

    if ( ! in_array( $vote_type, array( 'upvote', 'downvote' ), true ) ) {
        return new WP_Error( 'invalid_vote', 'Invalid vote type', array( 'status' => 400 ) );
    }

    // Fetch existing vote count and update it
    $meta_key = $vote_type === 'upvote' ? '_fc_fart_detail_upvotes' : '_fc_fart_detail_downvotes';
    $current_votes = get_post_meta( $fart_id, $meta_key, true );
    $new_votes = $current_votes ? intval( $current_votes ) + 1 : 1;

    update_post_meta( $fart_id, $meta_key, $new_votes );

    return new WP_REST_Response( array(
        'message' => ucfirst( $vote_type ) . ' recorded successfully.',
        'total_votes' => $new_votes,
    ), 200 );
}

function fc_validate_vote_request( $request ) {
    // Example: Only logged-in users can vote
    if ( is_user_logged_in() ) {
        return true;
    }
    return new WP_Error( 'not_logged_in', 'You must be logged in to vote', array( 'status' => 403 ) );
}

