<?php
// Add meta boxes for Fart Details and Fart Jokes
function fc_add_fart_meta_boxes() {
    add_meta_box(
        'fc_fart_votes_meta',
        __( 'Fart Votes', 'fart-calculator' ),
        'fc_fart_votes_meta_callback',
        'fart_detail',
        'side'
    );
    add_meta_box(
        'fc_fart_votes_meta',
        __( 'Fart Votes', 'fart-calculator' ),
        'fc_fart_votes_meta_callback',
        'fart_joke',
        'side'
    );
}

// Meta box callback function
function fc_fart_votes_meta_callback( $post ) {
    $upvotes = get_post_meta( $post->ID, '_fc_fart_upvotes', true );
    $downvotes = get_post_meta( $post->ID, '_fc_fart_downvotes', true );

    echo '<p><strong>' . __( 'Upvotes:', 'fart-calculator' ) . '</strong> ' . esc_html( $upvotes ) . '</p>';
    echo '<p><strong>' . __( 'Downvotes:', 'fart-calculator' ) . '</strong> ' . esc_html( $downvotes ) . '</p>';
}

// Save meta box data
function fc_save_fart_meta_boxes( $post_id ) {
    if ( isset( $_POST['fc_fart_upvotes'] ) ) {
        update_post_meta( $post_id, '_fc_fart_upvotes', intval( $_POST['fc_fart_upvotes'] ) );
    }
    if ( isset( $_POST['fc_fart_downvotes'] ) ) {
        update_post_meta( $post_id, '_fc_fart_downvotes', intval( $_POST['fc_fart_downvotes'] ) );
    }
}
