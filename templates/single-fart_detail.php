<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $volume    = get_post_meta( get_the_ID(), '_fc_fart_volume', true );
        $smell     = get_post_meta( get_the_ID(), '_fc_fart_smell', true );
        $duration  = get_post_meta( get_the_ID(), '_fc_fart_duration', true );
        $fart_description = get_post_field( 'post_content', get_the_ID() );
        $fart_date = get_the_date( 'F j, Y', get_the_ID() );
        ?>
        <div class="single-fart-detail">
            <h1><?php the_title(); ?></h1>
            <p><strong><?php _e( 'Date:', 'fart-calculator' ); ?></strong> <?php echo esc_html( $fart_date ); ?></p>
            <p><strong><?php _e( 'Volume:', 'fart-calculator' ); ?></strong> <?php echo esc_html( $volume ); ?></p>
            <p><strong><?php _e( 'Smell:', 'fart-calculator' ); ?></strong> <?php echo esc_html( $smell ); ?></p>
            <p><strong><?php _e( 'Duration:', 'fart-calculator' ); ?></strong> <?php echo esc_html( $duration ); ?> <?php _e( 'seconds', 'fart-calculator' ); ?></p>
            <p><strong><?php _e( 'Description:', 'fart-calculator' ); ?></strong> <?php echo esc_html( $fart_description ); ?></p>
            <div class="fc-fart-detail-votes">
                <?php
                $upvotes = get_post_meta( get_the_ID(), '_fc_fart_detail_upvotes', true );
                $downvotes = get_post_meta( get_the_ID(), '_fc_fart_detail_downvotes', true );
                $voters = get_post_meta( get_the_ID(), '_fc_fart_detail_voters', true );
                $user_id = get_current_user_id();

                $upvotes = $upvotes ? $upvotes : 0;
                $downvotes = $downvotes ? $downvotes : 0;
                ?>
                <p><strong>👍 Upvotes:</strong> <?php echo esc_html( $upvotes ); ?></p>
                <p><strong>👎 Downvotes:</strong> <?php echo esc_html( $downvotes ); ?></p>

                <?php if ( ! in_array( $user_id, (array) $voters ) ) : ?>
                    <button class="fc-vote-button fc-upvote" data-detail-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="upvote">👍 Upvote</button>
                    <button class="fc-vote-button fc-downvote" data-detail-id="<?php echo esc_attr( get_the_ID() ); ?>" data-vote-type="downvote">👎 Downvote</button>
                <?php else : ?>
                    <p><?php _e( 'You have already voted on this fart detail.', 'fart-calculator' ); ?></p>
                <?php endif; ?>
            </div>
        </div>
       
        <div class="fc-fart-detail-meta">
            <p class="fc-fart-detail-categories">
                <strong>Categories: </strong>
                <?php echo get_the_term_list( get_the_ID(), 'fart_detail_category', '', ', ' ); ?>
            </p>
            
            <p class="fc-fart-detail-tags">
                <strong>Tags: </strong>
                <?php echo get_the_term_list( get_the_ID(), 'fart_detail_tag', '', ', ' ); ?>
            </p>
        </div>
        <?php
    endwhile;
endif;

get_footer();
