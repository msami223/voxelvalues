<?php
/**
 * Archive Template for Games
 * Displays a list of all available games
 */

get_header(); ?>

<div class="cosmic-app-wrapper">
    
    <!-- Hero Section -->
    <header class="cosmic-hero">
        <div class="cosmic-container">
            <div class="hero-content">
                <h1 class="hero-title">All Games</h1>
                <div class="hero-meta">
                    <span class="meta-tag">Trading Values</span>
                    <span class="meta-tag">Updated Hourly</span>
                </div>
            </div>
        </div>
    </header>

    <!-- Search Section -->
    <div class="cosmic-search-section">
        <div class="cosmic-container">
            <div class="search-wrapper">
                <input type="text" placeholder="Search games..." class="main-search-input" id="gameSearch">
                <svg class="search-icon-svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Main Games Grid -->
    <main class="cosmic-main">
        <div class="cosmic-container">
            
            <?php
            // Query all games
            $games_query = new WP_Query( array(
                'post_type'      => 'game',
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
            ));

            if ( $games_query->have_posts() ) : ?>
                <div class="cosmic-grid games-grid">
                    <?php while ( $games_query->have_posts() ) : $games_query->the_post(); ?>
                        <div class="cosmic-card game-card">
                            
                            <!-- Game Image -->
                            <div class="card-visual">
                                <?php if(has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                } ?>
                            </div>

                            <!-- Game Info -->
                            <div class="card-info">
                                <h3 class="info-title"><?php the_title(); ?></h3>
                                
                                <?php if(get_the_excerpt()): ?>
                                <p class="game-description"><?php echo wp_trim_words(get_the_excerpt(), 15); ?></p>
                                <?php endif; ?>
                                
                                <div class="game-meta">
                                    <?php
                                    // Count items for this game
                                    $game_slug = $post->post_name;
                                    $item_count = new WP_Query(array(
                                        'post_type' => 'item',
                                        'posts_per_page' => -1,
                                        'tax_query' => array(
                                            array(
                                                'taxonomy' => 'game_ref',
                                                'field'    => 'slug',
                                                'terms'    => $game_slug,
                                            ),
                                        ),
                                    ));
                                    ?>
                                    <span class="meta-tag"><?php echo $item_count->found_posts; ?> Items</span>
                                </div>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="card-link-overlay"></a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="empty-state">
                    <h3>No games found</h3>
                    <p>No games have been added yet.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

</div>

<?php get_footer(); ?>
