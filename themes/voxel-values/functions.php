<?php
/**
 * Voxel Values functions and definitions
 */

// 0. Theme Setup - Enable Post Thumbnails
add_action( 'after_setup_theme', function() {
    add_theme_support( 'post-thumbnails' );
} );

// 1. Enqueue Parent & Child Styles + Scripts
add_action( 'wp_enqueue_scripts', function() {
    wp_enqueue_style( 'generatepress-parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'voxel-values-child-style', get_stylesheet_directory_uri() . '/style.css', array('generatepress-parent-style') );

    // Chart.js (CDN)
    if( is_singular('item') ) {
        wp_enqueue_script( 'chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), '4.4.1', true );
        
        // Inline Script to Render Chart (Dummy Data for now)
        wp_add_inline_script( 'chart-js', "
            document.addEventListener('DOMContentLoaded', function() {
                const ctx = document.getElementById('priceHistoryChart');
                if(ctx) {
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                            datasets: [{
                                label: 'Value Trend',
                                data: [12000000, 13500000, 12800000, 14000000, 15000000, 16000000],
                                borderColor: '#8b5cf6',
                                backgroundColor: 'rgba(139, 92, 246, 0.1)',
                                tension: 0.4,
                                fill: true
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: { legend: { display: false } },
                            scales: {
                                y: { grid: { color: '#333' } },
                                x: { grid: { color: '#333' } }
                            }
                        }
                    });
                }
            });
        " );
    }
} );

// 2. Register Custom Post Types (Games, Items, Trades)
add_action( 'init', function() {
    
    // A. Games (The "Hubs")
    register_post_type( 'game', array(
        'labels' => array(
            'name' => 'Games',
            'singular_name' => 'Game',
            'add_new_item' => 'Add New Game',
            'edit_item' => 'Edit Game',
            'new_item' => 'New Game',
            'view_item' => 'View Game',
            'search_items' => 'Search Games',
            'not_found' => 'No games found',
            'not_found_in_trash' => 'No games found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-admin-site-alt3', // Globe icon
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'rewrite' => array( 'slug' => 'games' ),
        'show_in_rest' => true, // Enable Gutenberg editor
    ));

    // B. Items (The "Assets")
    register_post_type( 'item', array(
        'labels' => array(
            'name' => 'Items',
            'singular_name' => 'Item',
            'add_new_item' => 'Add New Item',
            'edit_item' => 'Edit Item',
            'new_item' => 'New Item',
            'view_item' => 'View Item',
            'search_items' => 'Search Items',
            'not_found' => 'No items found',
            'not_found_in_trash' => 'No items found in Trash',
        ),
        'public' => true,
        'has_archive' => false, // We will use the Game page as the archive
        'menu_icon' => 'dashicons-products', 
        'supports' => array( 'title', 'editor', 'thumbnail', 'excerpt', 'custom-fields' ), // Added custom-fields just in case
        'rewrite' => array( 'slug' => '%game_ref%', 'with_front' => false ), // DYNAMIC SLUG
        'show_in_rest' => true,
    ));

    // C. Trades (The "Listings")
    register_post_type( 'trade', array(
        'labels' => array(
            'name' => 'Trades',
            'singular_name' => 'Trade',
            'add_new_item' => 'Create Trade',
            'edit_item' => 'Edit Trade',
            'new_item' => 'New Trade',
            'view_item' => 'View Trade',
            'search_items' => 'Search Trades',
            'not_found' => 'No trades found',
            'not_found_in_trash' => 'No trades found in Trash',
        ),
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-randomize',
        'supports' => array( 'title', 'editor', 'comments', 'author' ),
        'rewrite' => array( 'slug' => 'games/%game_ref%/trades' ), // Changed to plural "trades"
        'show_in_rest' => true,
    ));
});

// Add custom rewrite rule for game-based trade URLs
add_action( 'init', function() {
    add_rewrite_rule(
        '^games/([^/]+)/trades/([^/]+)/?$',
        'index.php?trade=$matches[2]',
        'top'
    );
});

// Filter trade permalinks to replace %game_ref% with actual game slug
add_filter( 'post_type_link', function( $post_link, $post ) {
    if ( $post->post_type === 'trade' ) {
        $terms = get_the_terms( $post->ID, 'game_ref' );
        if ( $terms && ! is_wp_error( $terms ) ) {
            $game_slug = $terms[0]->slug;
            $post_link = str_replace( '%game_ref%', $game_slug, $post_link );
        } else {
            // Fallback if no game assigned
            $post_link = str_replace( '%game_ref%', 'ungrouped', $post_link );
        }
    }
    return $post_link;
}, 10, 2 );

// 2b. Ensure Thumbnail Support for Custom Post Types (runs after registration)
add_action( 'init', function() {
    add_post_type_support( 'game', 'thumbnail' );
    add_post_type_support( 'item', 'thumbnail' );
    add_post_type_support( 'trade', 'thumbnail' );
}, 20 ); // Priority 20 ensures this runs AFTER post type registration (which is priority 10)

// 3. Register Taxonomies
add_action( 'init', function() {
    // Game Taxonomy (connects Items and Trades to a Game)
    register_taxonomy( 'game_ref', array( 'item', 'trade' ), array(
        'labels' => array(
            'name' => 'Game References',
            'singular_name' => 'Game Reference',
            'search_items' => 'Search Game Refs',
            'all_items' => 'All Game Refs',
            'edit_item' => 'Edit Game Ref',
            'update_item' => 'Update Game Ref',
            'add_new_item' => 'Add New Game Ref',
            'new_item_name' => 'New Game Ref Name',
            'menu_name' => 'Game Ref',
        ),
        'hierarchical' => true,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array( 'slug' => 'game-ref' ),
        'show_in_rest' => true,
    ));

    // Item Type and Rarity removed as they are game-specific and handled via ACF.
});

// 3a. Register Custom Query Vars for Sub-pages
add_filter( 'query_vars', function( $vars ) {
    $vars[] = 'voxel_subpage'; // e.g. 'feed', 'create', 'my-trades'
    return $vars;
});

// 3b. Add Rewrite Rules for Game Sub-pages
add_action( 'init', function() {
    // 1. Live Feed: /games/blox-fruits/feed/ -> game=blox-fruits & voxel_subpage=feed
    add_rewrite_rule(
        '^games/([^/]+)/feed/?$',
        'index.php?game=$matches[1]&voxel_subpage=feed',
        'top'
    );
    
    // 2. Create Trade: /games/blox-fruits/create/ -> game=blox-fruits & voxel_subpage=create
    add_rewrite_rule(
        '^games/([^/]+)/create/?$',
        'index.php?game=$matches[1]&voxel_subpage=create',
        'top'
    );

    // 3. My Trades: /games/blox-fruits/my-trades/ -> game=blox-fruits & voxel_subpage=my-trades
    add_rewrite_rule(
        '^games/([^/]+)/my-trades/?$',
        'index.php?game=$matches[1]&voxel_subpage=my-trades',
        'top'
    );
    
    // IMPORTANT: You must flush permalinks for these to take effect.
});

// 4. Dynamic Permalinks Logic
add_filter( 'post_type_link', function( $post_link, $post ) {
    if ( is_object( $post ) && $post->post_type == 'item' ) {
        $terms = wp_get_object_terms( $post->ID, 'game_ref' );
        if ( $terms ) {
            return str_replace( '%game_ref%', $terms[0]->slug, $post_link );
        } else {
            // Fallback if no game is selected
            return str_replace( '%game_ref%', 'uncategorized', $post_link );
        }
    }
    return $post_link;
}, 1, 2 );

// Flush rules on theme switch (safety measure)
add_action( 'after_switch_theme', 'flush_rewrite_rules' );

// One-time flush to update post type capabilities
add_action( 'init', function() {
    if ( !get_option( 'voxel_values_flushed_v2' ) ) {
        flush_rewrite_rules();
        update_option( 'voxel_values_flushed_v2', true );
    }
}, 30 );

// 5. Force No Sidebar for Custom Post Types
add_filter( 'generate_sidebar_layout', function( $layout ) {
    if ( is_singular( array( 'game', 'item' ) ) || is_post_type_archive( array( 'game', 'item' ) ) ) {
        return 'no-sidebar';
    }
    return $layout;
} );

// 6. Force Full Width Container for Custom Post Types
add_filter( 'generate_container_width', function( $width ) {
    if ( is_singular( array( 'game', 'item' ) ) || is_post_type_archive( array( 'game', 'item' ) ) ) {
        return 900; // User requested width
    }
    return $width;
} );

// 7. Helper: Number Formatting (K, M, B)
function voxel_num_format($n, $precision = 1) {
    if ($n < 1000) {
        return number_format($n);
    } else if ($n < 1000000) {
        return number_format($n / 1000, $precision) . 'K';
    } else if ($n < 1000000000) {
        return number_format($n / 1000000, $precision) . 'M';
    } else {
        return number_format($n / 1000000000, $precision) . 'B';
    }
}

// 8. Make Featured Image Required and Prominent for Games and Items
add_action('admin_notices', function() {
    global $post, $typenow;
    
    if ( in_array( $typenow, array( 'game', 'item' ) ) ) {
        if ( isset($post) && !has_post_thumbnail($post->ID) ) {
            echo '<div class="notice notice-warning">
                <p><strong>⚠️ Featured Image Required:</strong> Please add a featured image for this ' . $typenow . ' to display properly on the site.</p>
            </div>';
        }
    }
});

// 9. Add Featured Image Column in Admin List
add_filter('manage_game_posts_columns', function($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'title') {
            $new_columns['featured_image'] = 'Image';
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
});

add_filter('manage_item_posts_columns', function($columns) {
    $new_columns = array();
    foreach ($columns as $key => $value) {
        if ($key == 'title') {
            $new_columns['featured_image'] = 'Image';
        }
        $new_columns[$key] = $value;
    }
    return $new_columns;
});

// 10. Display Featured Image in Admin Column
add_action('manage_game_posts_custom_column', function($column_name, $post_id) {
    if ($column_name == 'featured_image') {
        $thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
        echo $thumbnail ? $thumbnail : '—';
    }
}, 10, 2);

add_action('manage_item_posts_custom_column', function($column_name, $post_id) {
    if ($column_name == 'featured_image') {
        $thumbnail = get_the_post_thumbnail($post_id, array(50, 50));
        echo $thumbnail ? $thumbnail : '—';
    }
}, 10, 2);

// 11. Customize Featured Image Meta Box Title
add_action('do_meta_boxes', function() {
    remove_meta_box('postimagediv', 'game', 'side');
    add_meta_box('postimagediv', '🎮 Game Image (Required)', 'post_thumbnail_meta_box', 'game', 'side', 'high');
    
    remove_meta_box('postimagediv', 'item', 'side');
    add_meta_box('postimagediv', '📦 Item Image (Required)', 'post_thumbnail_meta_box', 'item', 'side', 'high');
});

// ===============================================
// TRADING SYSTEM - Database & AJAX Handlers
// ===============================================

// 1. Create Custom Database Tables for Trading System
register_activation_hook( __FILE__, 'voxel_create_trade_tables' );
function voxel_create_trade_tables() {
    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    // Trade Messages Table
    $table_messages = $wpdb->prefix . 'trade_messages';
    $sql_messages = "CREATE TABLE IF NOT EXISTS $table_messages (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        trade_id bigint(20) NOT NULL,
        user_id bigint(20) NOT NULL,
        message text NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY trade_id (trade_id),
        KEY user_id (user_id)
    ) $charset_collate;";

    // Trade Status History Table
    $table_status = $wpdb->prefix . 'trade_status_history';
    $sql_status = "CREATE TABLE IF NOT EXISTS $table_status (
        id bigint(20) NOT NULL AUTO_INCREMENT,
        trade_id bigint(20) NOT NULL,
        status varchar(50) NOT NULL,
        changed_by bigint(20) NOT NULL,
        created_at datetime DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (id),
        KEY trade_id (trade_id)
    ) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
    dbDelta( $sql_messages );
    dbDelta( $sql_status );
}

// Run table creation on theme activation (manual trigger for now)
add_action( 'after_setup_theme', function() {
    if ( ! get_option( 'voxel_trade_tables_created' ) ) {
        voxel_create_trade_tables();
        update_option( 'voxel_trade_tables_created', '1' );
    }
});

//2. AJAX: Create Trade
add_action( 'wp_ajax_voxel_create_trade', 'voxel_ajax_create_trade' );
function voxel_ajax_create_trade() {
    check_ajax_referer( 'voxel_trade_nonce', 'nonce' );
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'You must be logged in to create a trade.' ) );
    }
    
    // Decode JSON arrays
    $have_items_json = isset( $_POST['have_items'] ) ? $_POST['have_items'] : '[]';
    $want_items_json = isset( $_POST['want_items'] ) ? $_POST['want_items'] : '[]';
    
    $have_items_raw = json_decode( stripslashes( $have_items_json ), true );
    $want_items_raw = json_decode( stripslashes( $want_items_json ), true );
    
    // Extract just the IDs (ACF expects array of post IDs)
    $have_items = array();
    $want_items = array();
    
    if ( is_array( $have_items_raw ) ) {
        foreach ( $have_items_raw as $item ) {
            if ( is_numeric( $item ) ) {
                $have_items[] = intval( $item );
            } elseif ( is_array( $item ) && isset( $item['id'] ) ) {
                $have_items[] = intval( $item['id'] );
            }
        }
    }
    
    if ( is_array( $want_items_raw ) ) {
        foreach ( $want_items_raw as $item ) {
            if ( is_numeric( $item ) ) {
                $want_items[] = intval( $item );
            } elseif ( is_array( $item ) && isset( $item['id'] ) ) {
                $want_items[] = intval( $item['id'] );
            }
        }
    }
    
    $note = isset( $_POST['note'] ) ? sanitize_textarea_field( $_POST['note'] ) : '';
    $game_slug = isset( $_POST['game_slug'] ) ? sanitize_text_field( $_POST['game_slug'] ) : '';
    
    if ( empty( $have_items ) || empty( $want_items ) ) {
        wp_send_json_error( array( 'message' => 'Please select items to offer and request.' ) );
    }
    
    $trade_id = wp_insert_post( array(
        'post_type' => 'trade',
        'post_title' => 'Trade #' . time(),
        'post_content' => $note,
        'post_status' => 'publish',
        'post_author' => $user_id,
    ));
    
    if ( is_wp_error( $trade_id ) ) {
        wp_send_json_error( array( 'message' => 'Failed to create trade.' ) );
    }
    
    update_field( 'have_items', $have_items, $trade_id );
    update_field( 'want_items', $want_items, $trade_id );
    update_field( 'trade_status', 'pending', $trade_id );
    update_field( 'trade_creator', $user_id, $trade_id );
    
    if ( $game_slug ) {
        wp_set_object_terms( $trade_id, $game_slug, 'game_ref' );
    }
    
    // Build feed URL instead of trade URL
    $game_obj = get_page_by_path( $game_slug, OBJECT, 'game' );
    $feed_url = $game_obj ? get_permalink( $game_obj->ID ) . 'feed/' : home_url( '/games/' . $game_slug . '/feed/' );
    
    wp_send_json_success( array(
        'trade_id' => $trade_id,
        'trade_url' => $feed_url, // Redirect to feed instead of trade page
        'message' => 'Trade created successfully!'
    ));
}

// 3. AJAX: Send Message
add_action( 'wp_ajax_voxel_send_trade_message', 'voxel_ajax_send_trade_message' );
function voxel_ajax_send_trade_message() {
    global $wpdb;
    check_ajax_referer( 'voxel_trade_nonce', 'nonce' );
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Not logged in.' ) );
    }
    
    $trade_id = intval( $_POST['trade_id'] );
    $message = sanitize_textarea_field( $_POST['message'] );
    
    if ( empty( $message ) ) {
        wp_send_json_error( array( 'message' => 'Message cannot be empty.' ) );
    }
    
    $table = $wpdb->prefix . 'trade_messages';
    $inserted = $wpdb->insert( $table, array(
        'trade_id' => $trade_id,
        'user_id' => $user_id,
        'message' => $message,
    ));
    
    if ( $inserted ) {
        wp_send_json_success( array(
            'message_id' => $wpdb->insert_id,
            'message' => 'Message sent.'
        ));
    } else {
        wp_send_json_error( array( 'message' => 'Failed to send message.' ) );
    }
}

// 4. AJAX: Get Messages
add_action( 'wp_ajax_voxel_get_trade_messages', 'voxel_ajax_get_trade_messages' );
function voxel_ajax_get_trade_messages() {
    global $wpdb;
    check_ajax_referer( 'voxel_trade_nonce', 'nonce' );
    
    $trade_id = intval( $_POST['trade_id'] );
    $after_id = isset( $_POST['after_id'] ) ? intval( $_POST['after_id'] ) : 0;
    
    $table = $wpdb->prefix . 'trade_messages';
    $query = $wpdb->prepare(
        "SELECT * FROM $table WHERE trade_id = %d AND id > %d ORDER BY created_at ASC",
        $trade_id,
        $after_id
    );
    
    $messages = $wpdb->get_results( $query );
    
    $formatted = array();
    foreach ( $messages as $msg ) {
        $user = get_userdata( $msg->user_id );
        $formatted[] = array(
            'id' => $msg->id,
            'user_id' => $msg->user_id,
            'user_name' => $user ? $user->display_name : 'Unknown',
            'message' => $msg->message,
            'created_at' => $msg->created_at,
        );
    }
    
    wp_send_json_success( array( 'messages' => $formatted ) );
}

// 5. AJAX: Update Trade Status
add_action( 'wp_ajax_voxel_update_trade_status', 'voxel_ajax_update_trade_status' );
function voxel_ajax_update_trade_status() {
    global $wpdb;
    check_ajax_referer( 'voxel_trade_nonce', 'nonce' );
    
    $user_id = get_current_user_id();
    if ( ! $user_id ) {
        wp_send_json_error( array( 'message' => 'Not logged in.' ) );
    }
    
    $trade_id = intval( $_POST['trade_id'] );
    $new_status = sanitize_text_field( $_POST['status'] );
    
    $valid_statuses = array( 'pending', 'accepted', 'rejected', 'countered', 'completed' );
    if ( ! in_array( $new_status, $valid_statuses ) ) {
        wp_send_json_error( array( 'message' => 'Invalid status.' ) );
    }
    
    update_field( 'trade_status', $new_status, $trade_id );
    
    $table = $wpdb->prefix . 'trade_status_history';
    $wpdb->insert( $table, array(
        'trade_id' => $trade_id,
        'status' => $new_status,
        'changed_by' => $user_id,
    ));
    
    wp_send_json_success( array( 'message' => 'Trade status updated to: ' . $new_status ) );
}

// 6. Localize AJAX for frontend
add_action( 'wp_enqueue_scripts', function() {
    global $wp;
    $current_url = home_url( add_query_arg( array(), $wp->request ) );
    
    // Load on: trade pages, trade archives, game pages, and any URL containing /games/
    $is_trade_related = is_singular( 'trade' ) 
        || is_post_type_archive( 'trade' ) 
        || is_singular( 'game' )
        || strpos( $current_url, '/games/' ) !== false; // Catches all game subpages
    
    if ( $is_trade_related ) {
        // Ensure jQuery is enqueued first
        wp_enqueue_script( 'jquery' );
        
        wp_localize_script( 'jquery', 'voxelTrade', array(
            'ajax_url' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'voxel_trade_nonce' ),
            'current_user' => get_current_user_id(),
        ));
    }
});
