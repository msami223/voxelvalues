<?php
/**
 * Trade Feed Archive - Blox Fruits
 * Displays all active trades for this game
 */

get_header('game'); // Load game-specific header

// Get current game context
global $wp_query;
$game_slug = get_query_var('game', '');
$game = get_page_by_path($game_slug, OBJECT, 'game');
?>

<div class="trade-feed-wrapper">
    <div class="cosmic-container">
        
        <!-- Page Header -->
        <header class="feed-header">
            <h1 class="page-title">Live Trading Feed</h1>
            <p class="page-subtitle">Browse active trade offers for <?php echo $game ? $game->post_title : 'all games'; ?></p>
        </header>

        <!-- Trade Feed Grid -->
        <div class="trade-feed-grid">
            <?php
            // Query trades for this game
            $args = array(
                'post_type' => 'trade',
                'posts_per_page' => 20,
                'orderby' => 'date',
                'order' => 'DESC',
            );

            // Filter by game if we have one
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
                    $trade_id = get_the_ID();
                    $have_items = get_field('have_items');
                    $want_items = get_field('want_items');
                    $trade_status = get_field('trade_status') ?: 'pending';
                    $creator_id = get_field('trade_creator');
                    $creator = get_userdata($creator_id);

                    // Calculate totals
                    $have_total = 0;
                    if ($have_items) {
                        foreach ($have_items as $item) {
                            $have_total += get_field('item_value', $item->ID) ?: 0;
                        }
                    }
                    $want_total = 0;
                    if ($want_items) {
                        foreach ($want_items as $item) {
                            $want_total += get_field('item_value', $item->ID) ?: 0;
                        }
                    }
            ?>
                <a href="<?php echo get_permalink(); ?>" class="trade-card">
                    <div class="trade-card-header">
                        <span class="status-badge status-<?php echo esc_attr($trade_status); ?>">
                            <?php echo ucfirst($trade_status); ?>
                        </span>
                        <span class="trade-time"><?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?></span>
                    </div>

                    <div class="trade-card-content">
                        <!-- Offering Side -->
                        <div class="trade-card-side">
                            <div class="side-label">Offering</div>
                            <div class="items-preview">
                                <?php 
                                if ($have_items):
                                    $count = 0;
                                    foreach(array_slice($have_items, 0, 3) as $item):
                                        if (has_post_thumbnail($item->ID)):
                                            $count++;
                                ?>
                                    <img src="<?php echo get_the_post_thumbnail_url($item->ID, 'thumbnail'); ?>" class="preview-img">
                                <?php 
                                        endif;
                                    endforeach;
                                    if (count($have_items) > 3):
                                ?>
                                    <div class="more-items">+<?php echo count($have_items) - 3; ?></div>
                                <?php endif; endif; ?>
                            </div>
                            <div class="side-value"><?php echo voxel_num_format($have_total); ?></div>
                        </div>

                        <div class="trade-vs">⇄</div>

                        <!-- Requesting Side -->
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
                                    if (count($want_items) > 3):
                                ?>
                                    <div class="more-items">+<?php echo count($want_items) - 3; ?></div>
                                <?php endif; endif; ?>
                            </div>
                            <div class="side-value"><?php echo voxel_num_format($want_total); ?></div>
                        </div>
                    </div>

                    <div class="trade-card-footer">
                        <span class="trade-creator">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
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
                    <p>No active trades found. <a href="<?php echo home_url('/games/' . $game_slug . '/create/'); ?>">Create the first one!</a></p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Pagination -->
        <?php if ($trades_query->max_num_pages > 1): ?>
        <div class="trade-pagination">
            <?php
            echo paginate_links(array(
                'total' => $trades_query->max_num_pages,
                'current' => max(1, get_query_var('paged')),
            ));
            ?>
        </div>
        <?php endif; ?>

    </div>
</div>

<?php
get_footer();
