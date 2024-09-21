<?php
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
