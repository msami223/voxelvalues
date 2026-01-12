<?php
/**
 * Single Trade Template - Default Fallback
 * Generic trade page for games without specific templates
 */

$trade_id = get_the_ID();
$have_items = get_field('have_items');
$want_items = get_field('want_items');
$trade_status = get_field('trade_status') ?: 'pending';
$trade_creator = get_field('trade_creator');
$current_user = get_current_user_id();
$creator_data = get_userdata($trade_creator);
?>

<div class="single-trade-wrapper">
    <div class="cosmic-container">
        
        <header class="trade-page-header">
            <div class="header-left">
                <h1 class="trade-title">Trade #<?php echo $trade_id; ?></h1>
                <p class="trade-meta">
                    Posted by <strong><?php echo $creator_data ? $creator_data->display_name : 'Unknown'; ?></strong>
                    • <?php echo human_time_diff(get_the_time('U'), current_time('timestamp')) . ' ago'; ?>
                </p>
            </div>
            <div class="header-right">
                <span class="status-badge status-<?php echo esc_attr($trade_status); ?>">
                    <?php echo ucfirst($trade_status); ?>
                </span>
            </div>
        </header>

        <div class="trade-display-container">
            <div class="trade-display-side">
                <h3 class="side-title">Offering</h3>
                <div class="trade-items-list">
                    <?php 
                    if ($have_items): 
                        foreach ($have_items as $item):
                    ?>
                        <div class="trade-item-card">
                            <?php if (has_post_thumbnail($item->ID)): ?>
                                <img src="<?php echo get_the_post_thumbnail_url($item->ID, 'thumbnail'); ?>" class="trade-item-img">
                            <?php endif; ?>
                            <div class="trade-item-name"><?php echo $item->post_title; ?></div>
                        </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>

            <div class="trade-display-side">
                <h3 class="side-title">Requesting</h3>
                <div class="trade-items-list">
                    <?php 
                    if ($want_items): 
                        foreach ($want_items as $item):
                    ?>
                        <div class="trade-item-card">
                            <?php if (has_post_thumbnail($item->ID)): ?>
                                <img src="<?php echo get_the_post_thumbnail_url($item->ID, 'thumbnail'); ?>" class="trade-item-img">
                            <?php endif; ?>
                            <div class="trade-item-name"><?php echo $item->post_title; ?></div>
                        </div>
                    <?php 
                        endforeach;
                    endif; 
                    ?>
                </div>
            </div>
        </div>

        <?php if (get_the_content()): ?>
        <div class="trade-note-display">
            <h4>Trade Note:</h4>
            <p><?php echo get_the_content(); ?></p>
        </div>
        <?php endif; ?>

        <?php if ($current_user): ?>
        <div class="trade-chat-section">
            <h3>Negotiate</h3>
            <div class="chat-messages" id="chat-messages">
                <div class="chat-loading">Loading messages...</div>
            </div>
            <div class="chat-input-container">
                <textarea id="chat-input" placeholder="Type your message..." rows="2"></textarea>
                <button class="btn-send-message" onclick="sendMessage()">Send</button>
            </div>
        </div>
        <?php else: ?>
        <div class="login-prompt">
            <p>Please <a href="<?php echo wp_login_url(get_permalink()); ?>">log in</a> to negotiate this trade.</p>
        </div>
        <?php endif; ?>

    </div>
</div>

<script>
const tradeId = <?php echo $trade_id; ?>;
let lastMessageId = 0;

document.addEventListener('DOMContentLoaded', function() {
    loadMessages();
    setInterval(loadMessages, 3000);
});

function loadMessages() {
    const formData = new FormData();
    formData.append('action', 'voxel_get_trade_messages');
    formData.append('nonce', voxelTrade.nonce);
    formData.append('trade_id', tradeId);
    formData.append('after_id', lastMessageId);

    fetch(voxelTrade.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success && data.data.messages.length > 0) {
            const container = document.getElementById('chat-messages');
            const loading = container.querySelector('.chat-loading');
            if (loading) loading.remove();

            data.data.messages.forEach(msg => {
                const isOwn = msg.user_id == voxelTrade.current_user;
                const bubble = document.createElement('div');
                bubble.className = 'message-bubble message-' + (isOwn ? 'sent' : 'received');
                bubble.innerHTML = `
                    <div class="message-author">${msg.user_name}</div>
                    <div class="message-text">${msg.message}</div>
                    <div class="message-time">${new Date(msg.created_at).toLocaleTimeString()}</div>
                `;
                container.appendChild(bubble);
                lastMessageId = msg.id;
            });

            container.scrollTop = container.scrollHeight;
        }
    });
}

function sendMessage() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    if (!message) return;

    const formData = new FormData();
    formData.append('action', 'voxel_send_trade_message');
    formData.append('nonce', voxelTrade.nonce);
    formData.append('trade_id', tradeId);
    formData.append('message', message);

    fetch(voxelTrade.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            input.value = '';
            loadMessages();
        } else {
            alert('Error: ' + data.data.message);
        }
    });
}
</script>
