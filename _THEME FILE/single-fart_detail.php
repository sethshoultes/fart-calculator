<?php
get_header();

if ( have_posts() ) :
    while ( have_posts() ) : the_post();
        $volume   = get_post_meta( get_the_ID(), '_fc_fart_volume', true );
        $smell    = get_post_meta( get_the_ID(), '_fc_fart_smell', true );
        $duration = get_post_meta( get_the_ID(), '_fc_fart_duration', true );
        ?>
        <div class="fc-single-fart-detail" style="max-width: 800px; margin: 0 auto; padding: 20px;">
            <h1><?php the_title(); ?></h1>
            <div class="fc-fart-meta" style="margin-bottom: 20px;">
                <p><strong>Volume:</strong> <?php echo esc_html( $volume ); ?></p>
                <p><strong>Smell:</strong> <?php echo esc_html( $smell ); ?></p>
                <p><strong>Duration:</strong> <?php echo esc_html( $duration ); ?> seconds</p>
            </div>
            <div class="fc-fart-content">
                <?php the_content(); ?>
            </div>
        </div>
        <?php
    endwhile;
endif;

get_footer();
?>
