<?php
/**
 * ITEM ROUTER
 * 
 * "TRAFFIC COP" ROUTER
 * Routes to the correct Item Detail template based on the Game Reference.
 */

get_header('game');

// 1. Resolve Game Context
$terms = get_the_terms( get_the_ID(), 'game_ref' );
$slug = ( $terms && ! is_wp_error( $terms ) ) ? $terms[0]->slug : 'default';

// 2. Load Item Content
if ( locate_template( 'templates/items/content-' . $slug . '.php' ) ) {
    get_template_part( 'templates/items/content', $slug );
} else {
    get_template_part( 'templates/items/content', 'default' );
}

get_footer();
?>
