<?php
/**
 * Blox Fruits Game Content
 * 
 * Specific overrides:
 * - Rarity Filter (Common, Uncommon, Mythical...)
 * - Fruit Type Badges
 * - Specific Sorting
 */
?>

<div class="cosmic-app-wrapper">
    
    <!-- Hero Section -->
    <header class="cosmic-hero">
        <div class="cosmic-container">
            <?php 
            $bg_image = get_the_post_thumbnail_url(null, 'full'); 
            $page_title = get_the_title();
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
                <input type="text" placeholder="Search fruits..." class="main-search-input" id="itemSearch">
                <svg class="search-icon-svg" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M9 17A8 8 0 1 0 9 1a8 8 0 0 0 0 16zM18 18l-4-4" stroke="currentColor" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters & Sort Section (BLOX FRUITS SPECIFIC) -->
    <div class="cosmic-filters-section">
        <div class="cosmic-container">
            <div class="filters-wrapper">
                <!-- Rarity Filters -->
                <div class="filter-group">
                    <span class="filter-label">Rarity:</span>
                    <div class="filter-chips">
                        <button class="chip active" data-rarity="all">All</button>
                        <button class="chip" data-rarity="common">Common</button>
                        <button class="chip" data-rarity="uncommon">Uncommon</button>
                        <button class="chip" data-rarity="rare">Rare</button>
                        <button class="chip" data-rarity="legendary">Legendary</button>
                        <button class="chip" data-rarity="mythical">Mythical</button>
                        <button class="chip" data-rarity="premium">Premium</button>
                    </div>
                </div>
                
                <!-- Sort Dropdown -->
                <div class="sort-group">
                    <label for="sortSelect">Sort:</label>
                    <select id="sortSelect" class="sort-dropdown">
                        <option value="value-desc">Value: High to Low</option>
                        <option value="value-asc">Value: Low to High</option>
                        <option value="name-asc">Name: A-Z</option>
                        <option value="name-desc">Name: Z-A</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Item Grid -->
    <main class="cosmic-main">
        <div class="cosmic-container">
            
            <?php
            // Custom Item Query for Blox Fruits
            // We can hardcode the slug 'blox-fruits' or use $post->post_name
            $target_slug = 'blox-fruits';

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
                        $trend = get_field('trend_status');
                        
                        // BLOX FRUITS SPECIFIC FIELDS
                        $fruit_type = get_field('fruit_type'); 
                        $item_tier = get_field('item_tier');
                        
                        $is_new = (strtotime($post->post_date) > strtotime('-7 days'));
                    ?>
                        <div class="cosmic-card" 
                             data-value="<?php echo esc_attr($value); ?>" 
                             data-rarity="<?php echo esc_attr(strtolower($fruit_type)); ?>"
                             data-title="<?php echo esc_attr(strtolower(get_the_title())); ?>">
                            
                            <div class="card-badges">
                                <?php if($is_new): ?><span class="badge new">New</span><?php endif; ?>
                            </div>

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
                                
                                <div class="info-grid">
                                    <?php if($demand): ?>
                                    <div class="mini-stat">
                                        <span class="l">Demand</span>
                                        <span class="d"><?php echo $demand; ?>/10</span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($trend): ?>
                                    <div class="mini-stat">
                                        <span class="l">Trend</span>
                                        <span class="d"><?php echo $trend; ?></span>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <?php if($fruit_type): ?>
                                    <div class="mini-stat">
                                        <span class="l">Type</span>
                                        <span class="d"><?php echo esc_html($fruit_type); ?></span>
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

    <!-- BLOX FRUITS SPECIFIC SCRIPT -->
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const cards = document.querySelectorAll('.cosmic-card');
        const filterBtns = document.querySelectorAll('[data-rarity]');
        const sortSelect = document.getElementById('sortSelect');
        const searchInput = document.getElementById('itemSearch');
        
        if (!cards.length) return; 
        
        let activeRarity = 'all';
        let activeSort = 'value-desc';
        let searchQuery = '';
        
        // Filter by rarity
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                activeRarity = btn.dataset.rarity;
                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                applyFilters();
            });
        });
        
        // Sort
        if (sortSelect) {
            sortSelect.addEventListener('change', () => {
                activeSort = sortSelect.value;
                applyFilters();
            });
        }
        
        // Search
        if (searchInput) {
            searchInput.addEventListener('input', () => {
                searchQuery = searchInput.value.toLowerCase();
                applyFilters();
            });
        }
        
        function applyFilters() {
            const cardsArray = Array.from(cards);
            
            cardsArray.forEach(card => {
                const rarity = card.dataset.rarity || '';
                const title = card.dataset.title || '';
                const matchesRarity = activeRarity === 'all' || rarity === activeRarity;
                const matchesSearch = !searchQuery || title.includes(searchQuery);
                
                card.style.display = (matchesRarity && matchesSearch) ? 'flex' : 'none';
            });
            
            const visibleCards = cardsArray.filter(c => c.style.display !== 'none');
            visibleCards.sort((a, b) => {
                const aVal = Number(a.dataset.value) || 0;
                const bVal = Number(b.dataset.value) || 0;
                const aTitle = a.dataset.title || '';
                const bTitle = b.dataset.title || '';
                
                if (activeSort === 'value-desc') return bVal - aVal;
                if (activeSort === 'value-asc') return aVal - bVal;
                if (activeSort === 'name-asc') return aTitle.localeCompare(bTitle);
                if (activeSort === 'name-desc') return bTitle.localeCompare(aTitle);
                return 0;
            });
            
            const grid = document.querySelector('.cosmic-grid');
            if (grid) {
                visibleCards.forEach(card => grid.appendChild(card));
            }
        }
        
        applyFilters();
    });
    </script>
</div>
