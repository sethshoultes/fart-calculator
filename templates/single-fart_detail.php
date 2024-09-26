<?php get_header(); ?>

<div class="fc-single-fart-detail" style="background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 8px; padding: 20px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);">
    <?php
    if (have_posts()) :
        while (have_posts()) : the_post();
            ?>
            <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <header class="entry-header">
                    <h1 class="fc-single-fart-detail-title" style="font-size: 2em; margin-bottom: 10px;"><?php the_title(); ?></h1>
                </header><!-- .entry-header -->

                <div class="entry-content">
                    <?php
                    // Display the featured image if it exists
                    if (has_post_thumbnail()) {
                        echo '<div class="fc-single-fart-detail-thumbnail" style="margin-bottom: 10px;">';
                        the_post_thumbnail('large');
                        echo '</div>';
                    }
                    ?>

                    <div class="fc-single-fart-detail-content" style="font-size: 1em; margin-bottom: 10px;">
                        <?php the_content(); ?>
                    </div>

                    <?php
                    // Get upvotes and downvotes
                    $upvotes = get_post_meta(get_the_ID(), '_fc_fart_detail_upvotes', true);
                    $downvotes = get_post_meta(get_the_ID(), '_fc_fart_detail_downvotes', true);

                    // Default to 0 if no upvotes/downvotes yet
                    $upvotes = $upvotes ? $upvotes : 0;
                    $downvotes = $downvotes ? $downvotes : 0;
                    ?>

                    <div class="fc-single-fart-detail-voting" style="display: flex; justify-content: space-between; align-items: center;">
                        <div class="fc-single-fart-detail-votes" style="display: flex; flex-direction: column; align-items: center;">
                            <p><strong>👍 Upvotes:</strong> <?php echo esc_html($upvotes); ?> <br><button class="fc-vote-button fc-upvote" style="background-color: #0073aa; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-top: 5px;" data-detail-id="<?php echo esc_attr(get_the_ID()); ?>" data-vote-type="upvote">👍 Upvote</button></p>
                            <p><strong>👎 Downvotes:</strong> <?php echo esc_html($downvotes); ?> <br><button class="fc-vote-button fc-downvote" style="background-color: #0073aa; color: #fff; border: none; padding: 5px 10px; border-radius: 4px; cursor: pointer; margin-top: 5px;" data-detail-id="<?php echo esc_attr(get_the_ID()); ?>" data-vote-type="downvote">👎 Downvote</button></p>
                        </div>
                    </div>
                </div><!-- .entry-content -->
            </article><!-- #post-## -->
            <?php
        endwhile;
    else :
        echo '<p>' . __('No fart details found.', 'fart-calculator') . '</p>';
    endif;
    ?>
</div><!-- .fc-single-fart-detail -->

<?php get_footer(); ?>