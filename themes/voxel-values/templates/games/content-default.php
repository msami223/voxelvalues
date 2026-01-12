<?php
/**
 * Default Game Content Template
 * 
 * Minimal generic layout for any game that doesn't have a specific override.
 */
?>

<div class="cosmic-app-wrapper">
    
    <!-- Header is already loaded by the Router -->

    <!-- Basic Hero -->
    <header class="cosmic-hero" style="padding: 40px 0;">
        <div class="cosmic-container">
            <h1 class="hero-title"><?php the_title(); ?> Value List</h1>
            <p style="color:var(--color-voxel-subtext);">Browse all item values and stats.</p>
        </div>
    </header>

    <!-- Search -->
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

    <!-- Generic Grid -->
    <main class="cosmic-main">
        <div class="cosmic-container">
            
            <?php
            $target_slug = $post->post_name;
            
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
                        $demand = get_field('demand_score'); 
                    ?>
                        <div class="cosmic-card" 
                             data-value="<?php echo esc_attr($value); ?>" 
                             data-title="<?php echo esc_attr(strtolower(get_the_title())); ?>">
                            
                            <div class="card-visual">
                                <?php if(has_post_thumbnail()) {
                                    the_post_thumbnail('medium');
                                } ?>
                            </div>

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
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="card-link-overlay"></a>
                        </div>
                    <?php endwhile; ?>
                </div>
                <?php wp_reset_postdata(); ?>
            <?php else : ?>
                <div class="empty-state">
                    <h3>No items found</h3>
                    <p>No items added yet for this game.</p>
                </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
    // Include BASIC Generic Filter Script inline or enqueue it
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.cosmic-card');
        const searchInput = document.getElementById('itemSearch');
        if(!searchInput) return;

        searchInput.addEventListener('input', () => {
            const query = searchInput.value.toLowerCase();
            cards.forEach(card => {
                const title = card.dataset.title || '';
                card.style.display = title.includes(query) ? 'flex' : 'none';
            });
        });
    });
    </script>

</div>
