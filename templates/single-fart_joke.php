<?php
get_header();
?>

<div class="fart-joke-single">
    <h1><?php the_title(); ?></h1>
    
    <div class="fart-joke-content">
        <?php the_content(); ?>
    </div>

    <?php
    // Get upvotes and downvotes
    $upvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_upvotes', true );
    $downvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_downvotes', true );

    // Default to 0 if no upvotes/downvotes yet
    $upvotes = $upvotes ? $upvotes : 0;
    $downvotes = $downvotes ? $downvotes : 0;
    ?>

    <div class="fart-joke-voting">
        <p><strong>Upvotes:</strong> <?php echo esc_html( $upvotes ); ?></p>
        <p><strong>Downvotes:</strong> <?php echo esc_html( $downvotes ); ?></p>

        <?php if ( is_user_logged_in() ) : ?>
            <button class="fc-vote-button fc-upvote" data-joke-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="upvote">
                👍 Upvote
            </button>
            <button class="fc-vote-button fc-downvote" data-joke-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="downvote">
                👎 Downvote
            </button>
        <?php else : ?>
            <p>You must be logged in to vote.</p>
        <?php endif; ?>
    </div>
</div>

<?php
get_footer();
