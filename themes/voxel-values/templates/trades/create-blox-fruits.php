<?php
/**
 * Create Trade Interface - Blox Fruits
 * 
 * Layout:
 * - Header (Title)
 * - Two Column Grid (Your items vs Request)
 * - Value Comparison Bar
 * - Tags & Note
 * - Action Footer
 */

 // Enqueue scripts for trade logic later?
 // For now, we build the visual structure.
?>

<div class="trade-interface-wrapper">
    <div class="cosmic-container">
        
        <!-- Page Header -->
        <header class="trade-header">
            <h1 class="page-title">Create Trade Ad</h1>
            <p class="page-subtitle">Set up your trade offer and find the perfect match for your items.</p>
        </header>

        <!-- Main Trading Grid -->
        <div class="trade-sides-container">
            
            <!-- LEFT: I HAVE -->
            <div class="trade-side side-have">
                <div class="side-badge badge-have">I Have</div>
                
                <div class="items-grid" id="grid-have">
                    <!-- Add Button Slot -->
                    <button class="item-slot add-slot" onclick="openItemSelector('have')">
                        <div class="add-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </div>
                        <span>Add Item</span>
                    </button>
                    <!-- Empty Slots (x8) -->
                    <?php for($i=0; $i<8; $i++): ?>
                        <div class="item-slot empty-slot"></div>
                    <?php endfor; ?>
                </div>
            </div>

            <!-- RIGHT: I WANT -->
            <div class="trade-side side-want">
                <div class="side-badge badge-want">I Want</div>
                
                <div class="items-grid" id="grid-want">
                    <!-- Add Button Slot -->
                    <button class="item-slot add-slot" onclick="openItemSelector('want')">
                        <div class="add-icon">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
                        </div>
                        <span>Add Item</span>
                    </button>
                     <!-- Empty Slots (x8) -->
                     <?php for($i=0; $i<8; $i++): ?>
                        <div class="item-slot empty-slot"></div>
                    <?php endfor; ?>
                </div>
            </div>

        </div>

        <!-- Value Comparison Bar -->
        <div class="value-comparison-bar">
            <!-- Your Offer -->
            <div class="value-side">
                <span class="label">YOUR OFFER</span>
                <span class="value main-val">0</span>
                <span class="demand">Demand: 0/10</span>
            </div>

            <div class="vs-badge">VS</div>

            <!-- You Want -->
            <div class="value-side">
                <span class="label">YOU WANT</span>
                <span class="value main-val">0</span>
                <span class="demand">Demand: 0/10</span>
            </div>
        </div>

        <!-- Note Section -->
        <div class="trade-note-section">
            <div class="input-header">
                <label>Add a note (optional)</label>
                <span class="char-count">0/100</span>
            </div>
            <textarea class="trade-note-input" placeholder="e.g., Looking for quick trade, Collector items only, etc."></textarea>
            <p class="help-text">Help other traders understand what you're looking for</p>
        </div>

        <!-- Sticky Footer for Actions -->
        <div class="trade-actions-footer">
            <button class="btn-reset" onclick="resetTrade()">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"></polyline><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"></path></svg>
                Reset
            </button>
            <button class="btn-publish" onclick="publishTrade()">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg>    
                Publish Trade Ad
            </button>
        </div>

    </div>
</div>

<!-- ITEM SELECTION MODAL -->
<div id="item-selector-modal" class="voxel-modal hidden">
    <div class="modal-content">
        <header class="modal-header">
            <h2>Select Item</h2>
            <button class="close-modal" onclick="closeItemSelector()">&times;</button>
        </header>
        <div class="modal-search">
            <input type="text" placeholder="Search items..." id="modal-search-input" onkeyup="filterItems()">
        </div>
        <div class="modal-items-grid" id="modal-items-list">
            <div class="modal-loading">Loading items...</div>
        </div>
    </div>
</div>

<?php
// Get All Items for this Game to pass to JS
$args = array(
    'post_type' => 'item',
    'posts_per_page' => -1,
    'tax_query' => array(
        array(
            'taxonomy' => 'game_ref',
            'field'    => 'slug',
            'terms'    => 'blox-fruits',
        ),
    ),
);
$items_query = new WP_Query( $args );
$items_data = array();
if ( $items_query->have_posts() ) {
    while ( $items_query->have_posts() ) {
        $items_query->the_post();
        $val = get_field('item_value');
        if(!$val) $val = 0;
        $items_data[] = array(
            'id' => get_the_ID(),
            'name' => get_the_title(),
            'img' => get_the_post_thumbnail_url(null, 'thumbnail'),
            'value' => (int)$val,
            'formatted_value' => voxel_num_format((int)$val)
        );
    }
    wp_reset_postdata();
}
?>

<script>
// Data from PHP
const allItems = <?php echo json_encode($items_data); ?>;
let currentSide = null;
let tradeData = { have: [], want: [] };

function openItemSelector(side) {
    currentSide = side;
    const modal = document.getElementById('item-selector-modal');
    modal.classList.remove('hidden');
    renderModalItems(allItems);
    document.getElementById('modal-search-input').focus();
}

function closeItemSelector() {
    document.getElementById('item-selector-modal').classList.add('hidden');
}

function renderModalItems(items) {
    const list = document.getElementById('modal-items-list');
    list.innerHTML = '';
    
    items.forEach(item => {
        const div = document.createElement('div');
        div.className = 'modal-item-card';
        div.innerHTML = `
            <img src="${item.img || 'https://placehold.co/60x60?text=?'}" class="modal-item-img">
            <div class="modal-item-name">${item.name}</div>
            <div class="modal-item-val">${item.formatted_value}</div>
        `;
        div.onclick = () => selectItem(item);
        list.appendChild(div);
    });
}

function filterItems() {
    const query = document.getElementById('modal-search-input').value.toLowerCase();
    const filtered = allItems.filter(i => i.name.toLowerCase().includes(query));
    renderModalItems(filtered);
}

function selectItem(item) {
    if(tradeData[currentSide].length < 8) {
        tradeData[currentSide].push(item);
        updateGrid(currentSide);
        updateTotals();
        closeItemSelector();
    } else {
        alert("Slot full!");
    }
}

function updateGrid(side) {
    const container = document.getElementById('grid-' + side);
    const addButton = container.children[0]; 
    
    while (container.children.length > 1) {
        container.removeChild(container.lastChild);
    }

    tradeData[side].forEach((item, index) => {
        const slot = document.createElement('div');
        slot.className = 'item-slot filled-slot';
        slot.innerHTML = `
            <img src="${item.img}" class="slot-img">
            <div class="slot-val">${item.formatted_value}</div>
            <button onclick="removeItem('${side}', ${index})" class="remove-btn">&times;</button>
        `;
        container.appendChild(slot);
    });

    const remaining = 8 - tradeData[side].length;
    for(let i=0; i<remaining; i++) {
        const empty = document.createElement('div');
        empty.className = 'item-slot empty-slot';
        container.appendChild(empty);
    }
}

function removeItem(side, index) {
    tradeData[side].splice(index, 1);
    updateGrid(side);
    updateTotals();
}

function updateTotals() {
    const haveTotal = tradeData.have.reduce((sum, i) => sum + i.value, 0);
    const wantTotal = tradeData.want.reduce((sum, i) => sum + i.value, 0);

    const sides = document.querySelectorAll('.value-side');
    sides[0].querySelector('.main-val').innerText = formatNum(haveTotal);
    sides[1].querySelector('.main-val').innerText = formatNum(wantTotal);
}

function formatNum(n) {
    if (n < 1000) return n;
    if (n < 1000000) return (n / 1000).toFixed(1) + 'K';
    if (n < 1000000000) return (n / 1000000).toFixed(1) + 'M';
    return (n / 1000000000).toFixed(1) + 'B';
}

function resetTrade() {
    tradeData.have = [];
    tradeData.want = [];
    updateGrid('have');
    updateGrid('want');
    updateTotals();
}

// PUBLISH TRADE
function publishTrade() {
    // Validate
    if (tradeData.have.length === 0 || tradeData.want.length === 0) {
        showNotification('Please add items to both "I Have" and "I Want" sections.', 'error');
        return;
    }

    const btn = document.querySelector('.btn-publish');
    btn.disabled = true;
    btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle></svg> Publishing...';

    const formData = new FormData();
    formData.append('action', 'voxel_create_trade');
    formData.append('nonce', voxelTrade.nonce);
    formData.append('have_items', JSON.stringify(tradeData.have.map(i => i.id)));
    formData.append('want_items', JSON.stringify(tradeData.want.map(i => i.id)));
    formData.append('note', document.querySelector('.trade-note-input').value);
    formData.append('game_slug', 'blox-fruits');

    fetch(voxelTrade.ajax_url, {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotification('Trade created successfully! Redirecting...', 'success');
            setTimeout(() => {
                window.location.href = data.data.trade_url;
            }, 1500);
        } else {
            showNotification('Error: ' + data.data.message, 'error');
            btn.disabled = false;
            btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg> Publish Trade Ad';
        }
    })
    .catch(err => {
        showNotification('Network error. Please try again.', 'error');
        btn.disabled = false;
        btn.innerHTML = '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 2L11 13"></path><path d="M22 2l-7 20-4-9-9-4 20-7z"></path></svg> Publish Trade Ad';
    });
}

// Show notification
function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = 'trade-notification notification-' + type;
    notification.textContent = message;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.classList.add('show');
    }, 100);
    
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

window.onclick = function(event) {
    const modal = document.getElementById('item-selector-modal');
    if (event.target == modal) {
        closeItemSelector();
    }
}
</script>
