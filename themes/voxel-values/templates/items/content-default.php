<?php
/**
 * Default Item Content Template
 * 
 * Generic layout for item details.
 */
?>

<div id="primary" class="content-area grid-container cosmic-container">
    <main id="main" class="site-main" style="padding-top: 40px;">

        <?php while ( have_posts() ) : the_post(); 
            $value = get_field('item_value');
            $price = get_field('beli_price'); 
            $demand = get_field('demand_score');
        ?>

            <article id="post-<?php the_ID(); ?>" class="voxel-item-card">
                
                <header class="entry-header" style="margin-bottom: 2rem;">
                    <div class="voxel-game-badge" style="margin-bottom: 0.5rem;">
                        <?php 
                        $terms = get_the_terms( get_the_ID(), 'game_ref' );
                        if ( $terms && ! is_wp_error( $terms ) ) {
                            echo esc_html( $terms[0]->name );
                        }
                        ?>
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
                            <span class="label">Shop Price</span>
                            <span class="value"><?php echo voxel_num_format( (float)$price ); ?></span>
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
