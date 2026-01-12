<?php
/**
 * Single Trade Router
 * Displays individual trade details with real-time chat
 */

get_header('game'); // Load game-specific header

if ( have_posts() ) : while ( have_posts() ) : the_post();
    
    // Get the game this trade belongs to
    $game_terms = get_the_terms( get_the_ID(), 'game_ref' );
    $game_slug = ( $game_terms && ! is_wp_error( $game_terms ) ) ? $game_terms[0]->slug : 'default';
    
    // Try to load game-specific template
    $template_path = get_stylesheet_directory() . '/templates/trades/single-' . $game_slug . '.php';
    $default_path = get_stylesheet_directory() . '/templates/trades/single-default.php';
    
    if ( file_exists( $template_path ) ) {
        include( $template_path );
    } elseif ( file_exists( $default_path ) ) {
        include( $default_path );
    } else {
        // Fallback if no templates exist
        echo '<div class="cosmic-container">';
        echo '<h1>' . get_the_title() . '</h1>';
        echo '<p>Trade template not found.</p>';
        echo '</div>';
    }
    
endwhile; endif;

get_footer();
