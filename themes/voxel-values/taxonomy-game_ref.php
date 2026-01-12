<?php
/**
 * Template Name: Cosmic Game Hub
 * Template Post Type: game
 * 
 * A fully custom "App View" template for Games.
 * This bypasses most standard theme classes to ensure full control.
 */

get_header(); ?>

<div class="cosmic-app-wrapper">
    
    <!-- Game Hero Section -->
    <header class="cosmic-hero">
        <div class="cosmic-container">
            <?php 
            // Determine Context (Game Post vs Taxonomy Archive)
            $target_slug = '';
            $page_title = '';
            $bg_image = '';

            if ( is_tax('game_ref') ) {
                $term = get_queried_object();
                $target_slug = $term->slug;
                $page_title = $term->name;
                // Try to get image from a linked game post if available, or fallback
            } else {
                $target_slug = $post->post_name;
                $page_title = get_the_title();
                $bg_image = get_the_post_thumbnail_url(null, 'full');
            }
            ?>

            <?php if ( $bg_image ) : ?>
                <div class="hero-bg-image" style="background-image: url('<?php echo $bg_image; ?>');"></div>
            <?php endif; ?>
            
            <div class="hero-content">
                <h1 class="hero-title"><?php echo $page_title; ?> Value List</h1>
                <div class="hero-meta">
                    <span class="meta-tag">Updated Hourly</span>
                    <span class="meta-tag">Verified Values</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Section -->
    <div class="cosmic-search-section">
        <div class="cosmic-container">
            <div class="search-wrapper">
                <input type="text" placeholder="Search items..." class="main-search-input" id="itemSearch">
                <svg class="search-icon-svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Item Grid -->
    <main class="cosmic-main">
        <div class="cosmic-container">
            
            <?php
            // Custom Item Query - Simple, no filtering yet
            $items_query = new WP_Query( array(
                'post_type'      => 'item',
                'posts_per_page' => -1, 
                'tax_query'      => array(
                    array(
                        'taxonomy' => 'game_ref',
                        'field'    => 'slug',
                        'terms'    => $target_slug,
                    ),
                ),
                'orderby'        => 'meta_value_num',
                'meta_key'       => 'item_value',
                'order'          => 'DESC',
            ));

            if ( $items_query->have_posts() ) : ?>
                <div class="cosmic-grid">
                    <?php while ( $items_query->have_posts() ) : $items_query->the_post(); 
                        $value = get_field('item_value');
                        $price = get_field('beli_price');
                        // ONLY use real data. No placeholders.
                        $demand = get_field('demand_score'); 
                        $trend = get_field('trend_status'); // Assuming field exists or returns null
                        $is_new = (strtotime($post->post_date) > strtotime('-7 days')); // Logic: Posted in last 7 days
                    ?>
                        <div class="cosmic-card rarity-mythical"> <!-- Dynamic Rarity Class TBD -->
                            
                            <!-- Badges -->
                            <div class="card-badges">
                                <?php if($is_new): ?><span class="badge new">New</span><?php endif; ?>
                            </div>

                            <!-- Image -->
                            <div class="card-visual">
                                <?php if(has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                } ?>
                            </div>

                            <!-- Content -->
                            <div class="card-info">
                                <h3 class="info-title"><?php the_title(); ?></h3>
                                
                                <div class="info-row highlight-row">
                                    <span class="label">Value</span>
                                    <span class="data val"><?php echo voxel_num_format($value); ?></span>
                                </div>
                                
                                <?php if($demand): ?>
                                <div class="info-row">
                                    <span class="label">Demand</span>
                                    <span class="data demand-high"><?php echo $demand; ?>/10</span>
                                </div>
                                <?php endif; ?>

                                <div class="info-grid">
                                    <?php if($price): ?>
                                    <div class="mini-stat">
                                        <span class="l">Price</span>
                                        <span class="d"><?php echo voxel_num_format($price); ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($trend): ?>
                                    <div class="mini-stat">
                                        <span class="l">Trend</span>
                                        <span class="d trend-stable"><?php echo $trend; ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="card-link-overlay"></a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="empty-state">
                    <h3>No items found</h3>
                    <p>This game doesn't have any items listed yet.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</div>

<?php get_footer(); ?>
