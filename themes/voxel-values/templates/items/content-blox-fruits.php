<?php
/**
 * Blox Fruits Item Content
 * 
 * Specific overrides:
 * - Shows Fruit Type
 * - Shows Item Tier
 */
?>

<div id="primary" class="content-area grid-container cosmic-container">
    <main id="main" class="site-main" style="padding-top: 40px;">

        <?php while ( have_posts() ) : the_post(); 
            // Core Stats
            $value = get_field('item_value');
            $price = get_field('beli_price'); 
            $demand = get_field('demand_score');
            
            // SPECIFIC Stats
            $fruit_type = get_field('fruit_type');
            $item_tier = get_field('item_tier');
        ?>

            <article id="post-<?php the_ID(); ?>" class="voxel-item-card">
                
                <header class="entry-header" style="margin-bottom: 2rem;">
                    <div class="voxel-game-badge" style="margin-bottom: 0.5rem; background:#8b5cf6;">
                        Blox Fruits
                    </div>
                    <h1 class="entry-title"><?php the_title(); ?></h1>
                </header>

                <div class="voxel-item-grid">
                    
                    <!-- Left: Image -->
                    <div class="voxel-item-image">
                        <?php if ( has_post_thumbnail() ) : ?>
                            <?php the_post_thumbnail( 'large' ); ?>
                        <?php endif; ?>
                    </div>

                    <!-- Right: Stats -->
                    <div class="voxel-item-stats">
                        <div class="stat-box value-box">
                            <span class="label">Value</span>
                            <span class="value"><?php echo voxel_num_format( (float)$value ); ?></span>
                        </div>

                        <?php if($price): ?>
                        <div class="stat-box price-box">
                            <span class="label">Beli Price</span>
                            <span class="value"><?php echo voxel_num_format( (float)$price ); ?></span>
                        </div>
                        <?php endif; ?>
                        
                        <?php if($fruit_type): ?>
                        <div class="stat-box">
                            <span class="label">Fruit Type</span>
                            <span class="value"><?php echo esc_html( $fruit_type ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if($item_tier): ?>
                        <div class="stat-box">
                            <span class="label">Tier</span>
                            <span class="value"><?php echo esc_html( $item_tier ); ?></span>
                        </div>
                        <?php endif; ?>

                        <?php if($demand): ?>
                        <div class="stat-box">
                            <span class="label">Demand</span>
                            <span class="value"><?php echo $demand; ?>/10</span>
                        </div>
                        <?php endif; ?>
                    </div>

                </div>

                <!-- Price History Chart -->
                <div class="voxel-chart-container">
                    <h3 style="margin-bottom:1rem; font-size:1rem; color:#9ca3af; text-transform:uppercase;">Price History</h3>
                    <canvas id="priceHistoryChart"></canvas>
                </div>

            </article>

        <?php endwhile; ?>

    </main>
</div>
