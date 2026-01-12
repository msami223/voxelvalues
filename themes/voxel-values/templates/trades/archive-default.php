<?php
/**
 * Trade Feed Archive - Default Fallback
 */

get_header('game');

global $wp_query;
$game_slug = get_query_var('game', '');
$game = get_page_by_path($game_slug, OBJECT, 'game');
?>

<div class="trade-feed-wrapper">
    <div class="cosmic-container">
        
        <header class="feed-header">
            <h1 class="page-title">Live Trading Feed</h1>
            <p class="page-subtitle">Browse active trade offers</p>
        </header>

        <div class="trade-feed-grid">
            <?php
            $args = array(
                'post_type' => 'trade',
                'posts_per_page' => 20,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            if ($game_slug) {
                $args['tax_query'] = array(
                    array(
                        'taxonomy' => 'game_ref',
                        'field' => 'slug',
                        'terms' => $game_slug,
                    ),
                );
            }

            $trades_query = new WP_Query($args);

            if ($trades_query->have_posts()):
                while ($trades_query->have_posts()): $trades_query->the_post();
                    $have_items = get_field('have_items');
                    $want_items = get_field('want_items');
                    $trade_status = get_field('trade_status') ?: 'pending';
                    $creator = get_userdata(get_field('trade_creator'));
            ?>
                <a href="<?php echo get_permalink(); ?>" class="trade-card">
                    <div class="trade-card-header">
                        <span class="status-badge status-<?php echo esc_attr($trade_status); ?>">
                            <?php echo ucfirst($trade_status); ?>
                        </span>
                        <span class="trade-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                    </div>

                    <div class="trade-card-content">
                        <div class="trade-card-side">
                            <div class="side-label">Offering</div>
                            <div class="items-preview">
                                <?php 
                                if ($have_items):
                                    foreach(array_slice($have_items, 0, 3) as $item):
                                        if (has_post_thumbnail($item->ID)):
                                ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($item->ID, 'thumbnail'); ?>" class="preview-img">
                                <?php 
                                        endif;
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                        </div>

                        <div class="trade-vs">⇄</div>

                        <div class="trade-card-side">
                            <div class="side-label">Requesting</div>
                            <div class="items-preview">
                                <?php 
                                if ($want_items):
                                    foreach(array_slice($want_items, 0, 3) as $item):
                                        if (has_post_thumbnail($item->ID)):
                                ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($item->ID, 'thumbnail'); ?>" class="preview-img">
                                <?php 
                                        endif;
                                    endforeach;
                                endif; 
                                ?>
                            </div>
                        </div>
                    </div>

                    <div class="trade-card-footer">
                        <span class="trade-creator">
                            <?php echo $creator ? $creator->display_name : 'Unknown'; ?>
                        </span>
                    </div>
                </a>
            <?php
                endwhile;
                wp_reset_postdata();
            else:
            ?>
                <div class="no-trades">
                    <p>No active trades found.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php
get_footer();
