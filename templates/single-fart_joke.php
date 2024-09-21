<?php
get_header();
?>

<div class="fc-fart-joke-single">
    <h1 class="fc-fart-joke-title"><?php the_title(); ?></h1>
    
    <div class="fc-fart-joke-content">
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

    <div class="fc-fart-joke-voting">
        
        <div class="fc-fart-joke-votes">
            <?php
            $upvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_upvotes', true );
            $downvotes = get_post_meta( get_the_ID(), '_fc_fart_joke_downvotes', true );

            $upvotes = $upvotes ? $upvotes : 0;
            $downvotes = $downvotes ? $downvotes : 0;
            ?>
            <p><strong>👍 Upvotes:</strong> <?php echo esc_html( $upvotes ); ?> <br><button class="fc-vote-button fc-upvote" data-joke-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="upvote">👍 Upvote</button></p>
            <p><strong>👎 Downvotes:</strong> <?php echo esc_html( $downvotes ); ?> <br><button class="fc-vote-button fc-downvote" data-joke-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="downvote">👎 Downvote</button></p>

            
            
        </div>
        <div class="fc-fart-joke-meta">
            <p class="fc-fart-joke-categories">
                <strong>Categories: </strong>
                <?php echo get_the_term_list( get_the_ID(), 'fart_joke_category', '', ', ' ); ?>
            </p>
            
            <p class="fc-fart-joke-tags">
                <strong>Tags: </strong>
                <?php echo get_the_term_list( get_the_ID(), 'fart_joke_tag', '', ', ' ); ?>
            </p>
        </div>
    </div>
</div>

<?php
get_footer();
