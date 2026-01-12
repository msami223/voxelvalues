<?php
/**
 * Blox Fruits Game Header - App Style
 */

$current_game_slug = 'blox-fruits';
$current_game_name = 'Blox Fruits';

// Links (Pretty)
$base_url = home_url( '/games/' . $current_game_slug ); 

$home_link   = $base_url;
$feed_link   = $base_url . '/feed/';
$create_link = $base_url . '/create/';
$my_link     = $base_url . '/my-trades/';
$site_home   = home_url('/');

// Helper for Icons (Same as default)
function voxel_icon_bf($name) {
    if($name == 'clipboard') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>';
    if($name == 'arrows') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M7 10l5-6 5 6"></path><path d="M12 4v20"></path></svg>';
    if($name == 'plus') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>';
    if($name == 'user') return '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>';
    if($name == 'logo') return '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>';
    return '';
}
?>

<header class="app-header blox-fruits-context">
    <div class="app-header-container">
        
        <!-- LEFT: Logo + Game Switcher -->
        <div class="header-section left">
            <a href="<?php echo $site_home; ?>" class="brand-logo">
                <?php echo voxel_icon_bf('logo'); ?>
            </a>
            
            <div class="game-switcher-pill">
                <div class="gs-content">
                    <span class="gs-current"><?php echo esc_html($current_game_name); ?></span>
                    <span class="gs-label">Switch Game</span>
                </div>
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#666" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
            </div>
        </div>

        <!-- CENTER: Navigation Icons -->
        <nav class="header-section center">
            
            <!-- 1. All Values -->
            <a href="<?php echo $home_link; ?>" class="nav-icon-link <?php echo (is_singular('game') && !get_query_var('voxel_subpage')) ? 'active' : ''; ?>">
                <span class="icon-box"><?php echo voxel_icon_bf('clipboard'); ?></span>
                <span class="link-text">All Values</span>
            </a>

            <!-- 2. Live Trading Feed -->
            <a href="<?php echo $feed_link; ?>" class="nav-icon-link <?php echo (get_query_var('voxel_subpage') == 'feed') ? 'active' : ''; ?>">
                <span class="icon-box"><?php echo voxel_icon_bf('arrows'); ?></span>
                <span class="link-text">Live Trading Feed</span>
            </a>

            <!-- 3. Create Trade -->
            <a href="<?php echo $create_link; ?>" class="nav-icon-link <?php echo (get_query_var('voxel_subpage') == 'create') ? 'active' : ''; ?>">
                <span class="icon-box"><?php echo voxel_icon_bf('plus'); ?></span>
                <span class="link-text">Create Trade</span>
            </a>

            <!-- 4. My Trades -->
            <a href="<?php echo $my_link; ?>" class="nav-icon-link <?php echo (get_query_var('voxel_subpage') == 'my-trades') ? 'active' : ''; ?>">
                <span class="icon-box"><?php echo voxel_icon_bf('user'); ?></span>
                <span class="link-text">My Trades</span>
            </a>

        </nav>

        <!-- RIGHT: Login/Actions -->
        <div class="header-section right">
            
            <!-- Discord -->
            <a href="#" class="discord-btn">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="#5865F2"><path d="M20.317 4.37a19.791 19.791 0 0 0-4.885-1.515.074.074 0 0 0-.079.037c-.21.375-.444.864-.608 1.25a18.27 18.27 0 0 0-5.487 0 12.64 12.64 0 0 0-.617-1.25.077.077 0 0 0-.079-.037A19.736 19.736 0 0 0 3.677 4.37a.07.07 0 0 0-.032.027C.533 9.046-.32 13.58.099 18.057a.082.082 0 0 0 .031.057 19.9 19.9 0 0 0 5.993 3.03.078.078 0 0 0 .084-.028 14.09 14.09 0 0 0 1.226-1.994.076.076 0 0 0-.041-.106 13.107 13.107 0 0 1-1.872-.892.077.077 0 0 1-.008-.128 10.2 10.2 0 0 0 .372-.292.074.074 0 0 1 .077-.01c3.928 1.793 8.18 1.793 12.062 0a.074.074 0 0 1 .078.01c.12.098.246.198.373.292a.077.077 0 0 1-.006.127 12.299 12.299 0 0 1-1.873.892.077.077 0 0 0-.041.107c.36.698.772 1.362 1.225 1.993a.076.076 0 0 0 .084.028 19.839 19.839 0 0 0 6.002-3.03.077.077 0 0 0 .032-.054c.5-5.177-.838-9.674-3.549-13.66a.061.061 0 0 0-.031-.03zM8.02 15.33c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.956-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.956 2.418-2.157 2.418zm7.975 0c-1.183 0-2.157-1.085-2.157-2.419 0-1.333.955-2.419 2.157-2.419 1.21 0 2.176 1.096 2.157 2.42 0 1.333-.946 2.418-2.157 2.418z"/></svg> 
            </a>

            <a href="#" class="login-btn">Login</a>

        </div>

    </div>
</header>
