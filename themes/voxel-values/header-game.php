<?php
/**
 * Game Context Header
 * 
 * Replaces the standard GeneratePress header for Game/App pages.
 * Loads the Game-Specific Navigation Bar immediately.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; 
}
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?> <?php generate_do_microdata( 'body' ); ?>>
	<?php
	do_action( 'wp_body_open' );
    
    // Skip standard GeneratePress Header hooks
    // do_action( 'generate_before_header' );
    // do_action( 'generate_header' );
    // do_action( 'generate_after_header' );

    // INSTEAD: Load our Custom Game Header Logic
    
    // 1. Resolve Context (Same logic as Router, but specifically for Header part)
    // We need to know which header to load.
    
    $slug = 'default';
    if ( is_singular('game') ) {
        $slug = $post->post_name;
    } elseif ( is_tax('game_ref') ) {
        $slug = get_queried_object()->slug;
    } elseif ( is_singular( array('item', 'trade') ) ) {
        $terms = get_the_terms( get_the_ID(), 'game_ref' );
        if ( $terms && ! is_wp_error( $terms ) ) $slug = $terms[0]->slug;
    }

    // 2. Load the Template
    if ( locate_template( 'templates/headers/header-' . $slug . '.php' ) ) {
        get_template_part( 'templates/headers/header', $slug );
    } else {
        // Safe Fallback
        include( get_stylesheet_directory() . '/templates/headers/header-default.php' ); 
    }
	?>

	<div <?php generate_do_attr( 'page' ); ?> style="margin-top:0 !important; padding-top:0 !important;">
		<div <?php generate_do_attr( 'site-content' ); ?>>
