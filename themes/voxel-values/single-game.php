<?php
/**
 * Template Name: Cosmic Game Hub Router
 * Template Post Type: game
 * 
 * "TRAFFIC COP" ROUTER
 * 
 * 1. Global Header (Game Context)
 * 2. Game-Specific Content (Grid, Filters)
 * 3. Global Footer
 */

get_header('game'); 

// 1. Resolve Game Data
$slug = $post->post_name; 
$subpage = get_query_var('voxel_subpage'); // 'feed', 'create', 'my-trades', or ''

// 2. Router Logic
if ( $subpage === 'feed' ) {
    // Load Trade Feed
    if ( locate_template( 'templates/trades/archive-' . $slug . '.php' ) ) {
        get_template_part( 'templates/trades/archive', $slug );
    } else {
        get_template_part( 'templates/trades/archive', 'default' );
    }

} elseif ( $subpage === 'create' ) {
    // Load Create Trade Form
    if ( locate_template( 'templates/trades/create-' . $slug . '.php' ) ) {
        get_template_part( 'templates/trades/create', $slug );
    } else {
        get_template_part( 'templates/trades/create', 'default' );
    }

} elseif ( $subpage === 'my-trades' ) {
    // Load User's Trades
    if ( locate_template( 'templates/trades/my-trades-' . $slug . '.php' ) ) {
        get_template_part( 'templates/trades/my-trades', $slug );
    } else {
        get_template_part( 'templates/trades/my-trades', 'default' );
    }

} else {
    // Default: Load Game Hub Content
    if ( locate_template( 'templates/games/content-' . $slug . '.php' ) ) {
        get_template_part( 'templates/games/content', $slug );
    } else {
        get_template_part( 'templates/games/content', 'default' );
    }
}

get_footer();
?>
