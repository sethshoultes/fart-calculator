<?php
// Enqueue scripts and styles
function fc_enqueue_assets() {
    wp_enqueue_style( 'fc-style', plugins_url( '/css/style.css', __FILE__ ) );
    wp_enqueue_script( 'fc-voting-script', plugins_url( '/js/fart-voting.js', __FILE__ ), array( 'jquery' ), null, true );

    wp_localize_script( 'fc-voting-script', 'fc_ajax_object', array(
        'ajax_url'      => admin_url( 'admin-ajax.php' ),
        'fc_ajax_nonce' => wp_create_nonce( 'fc_vote_nonce' )
    ));
}
