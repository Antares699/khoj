<?php
// claim.php
require_once 'includes/header.php';
?>

<div class="split-layout">
    <!-- Left Side: Search Form -->
    <div class="split-left">
        <div class="claim-box">
            <h1>Hello! Let's start with your business name!</h1>
            <p class="claim-subtitle">Search or add your business name.</p>

            <div class="claim-search-wrapper">
                <div class="input-with-label">
                    <label>Your business name</label>
                    <input type="text" id="business-search" placeholder="" autocomplete="off">
                    <div class="search-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                    </div>
                </div>

                <!-- Dropdown Results -->
                <div id="claim-results" class="autocomplete-results" style="display: none;">
                    <a href="#" id="add-new-option" class="autocomplete-item add-new">
                        <div class="ac-icon-box">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect>
                                <line x1="12" y1="8" x2="12" y2="16"></line>
                                <line x1="8" y1="12" x2="16" y2="12"></line>
                            </svg>
                            <div class="plus-overlay">+</div>
                        </div>
                        <div class="ac-text-box">
                            <span class="ac-main-text" id="search-term-display">Loading...</span>
                            <span class="ac-sub-text">Add business with this name</span>
                        </div>
                    </a>

                    <div class="ac-separator"></div>

                    <div id="existing-results"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side: Content -->
    <div class="split-right">
        <div class="content-wrapper">
            <div class="stat-card">
                <div class="stat-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path>
                        <circle cx="9" cy="7" r="4"></circle>
                        <path d="M23 21v-2a4 4 0 0 0-3-3.87"></path>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"></path>
                    </svg>
                </div>
                <h2>Connect with millions of customers</h2>
                <p>Join the community of local businesses growing on Khoj every day.</p>
            </div>

            <div class="feature-list">
                <div class="feature-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                    </svg>
                    <span>Track your performance</span>
                </div>
                <div class="feature-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5z">
                        </path>
                    </svg>
                    <span>Respond to reviews</span>
                </div>
                <div class="feature-item">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                        stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                        <line x1="16" y1="2" x2="16" y2="6"></line>
                        <line x1="8" y1="2" x2="8" y2="6"></line>
                        <line x1="3" y1="10" x2="21" y2="10"></line>
                    </svg>
                    <span>Manage appointments</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Claimed Modal (Icon Removed) -->
<div id="claimed-modal" class="modal-overlay" style="display: none;">
    <div class="modal-content">
        <button class="close-modal">&times;</button>
        <!-- Icon removed -->
        <h3 id="modal-biz-name">Business Name</h3>
        <p>has already been claimed.</p>
        <p class="modal-sub">Wasn't you? Please contact customer support.</p>
    </div>
</div>

<style>
    /* Page Specific Styles */
    .split-layout {
        display: flex;
        min-height: 100vh;
        /* Fill full screen height, pushing footer down */
    }

    .split-left {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px;
        background: #fff;
    }

    .split-right {
        flex: 1;
        background: #f4f4f5;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 60px;
        border-left: 1px solid #eee;
    }

    .claim-box {
        width: 100%;
        max-width: 500px;
    }

    .content-wrapper {
        max-width: 450px;
        color: var(--charcoal);
    }

    .stat-card {
        background: #fff;
        padding: 32px;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        margin-bottom: 32px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        background: #EFF6FF;
        color: var(--blue);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 20px;
    }

    .stat-card h2 {
        font-size: 24px;
        font-weight: 800;
        margin-bottom: 12px;
        line-height: 1.3;
    }

    .stat-card p {
        color: #666;
        font-size: 15px;
        line-height: 1.5;
    }

    .feature-list {
        display: flex;
        flex-direction: column;
        gap: 16px;
    }

    .feature-item {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
        color: #444;
    }

    .feature-item svg {
        color: var(--blue);
    }

    .claim-box h1 {
        font-size: 36px;
        font-weight: 900;
        margin-bottom: 12px;
        color: var(--charcoal);
        line-height: 1.1;
    }

    .claim-subtitle {
        color: var(--gray-500);
        font-size: 18px;
        margin-bottom: 40px;
    }

    .claim-search-wrapper {
        position: relative;
        text-align: left;
    }

    .input-with-label {
        position: relative;
    }

    .input-with-label label {
        position: absolute;
        top: -10px;
        left: 12px;
        background: #fff;
        padding: 0 4px;
        font-size: 12px;
        color: var(--blue);
        font-weight: 600;
    }

    .input-with-label input {
        width: 100%;
        padding: 16px 40px 16px 16px;
        border: 2px solid var(--blue);
        border-radius: 4px;
        font-size: 16px;
        outline: none;
        font-family: inherit;
    }

    .search-icon {
        position: absolute;
        right: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--charcoal);
    }



    /* Modal */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        display: flex;
        justify-content: center;
        align-items: center;
        z-index: 1000;
    }

    .modal-content {
        background: #fff;
        padding: 40px;
        border-radius: 12px;
        max-width: 400px;
        width: 90%;
        text-align: center;
        position: relative;
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    }

    .close-modal {
        position: absolute;
        top: 12px;
        right: 12px;
        background: none;
        border: none;
        font-size: 24px;
        cursor: pointer;
        color: #888;
    }

    .modal-content h3 {
        font-size: 22px;
        margin-bottom: 8px;
        color: var(--charcoal);
        font-weight: 800;
        margin-top: 10px;
        /* Spacing since icon is gone */
    }

    .modal-content p {
        color: var(--charcoal);
        font-weight: 500;
        font-size: 16px;
    }

    .modal-sub {
        color: var(--gray-500) !important;
        font-weight: 400 !important;
        font-size: 14px;
        margin-top: 8px;
    }

    @media (max-width: 900px) {
        .split-layout {
            flex-direction: column;
        }

        .split-right {
            padding: 40px 20px;
        }

        .split-left {
            padding: 60px 20px;
        }
    }
</style>

<script>
    const searchInput = document.getElementById('business-search');
    const resultsContainer = document.getElementById('claim-results');
    const existingResults = document.getElementById('existing-results');
    const searchTermDisplay = document.getElementById('search-term-display');
    const addNewOption = document.getElementById('add-new-option');
    const modal = document.getElementById('claimed-modal');
    const modalBizName = document.getElementById('modal-biz-name');
    const closeModal = document.querySelector('.close-modal');

    // Debounce function
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    const fetchBusinesses = async (query) => {
        if (query.length < 2) {
            resultsContainer.style.display = 'none';
            return;
        }

        try {
            const response = await fetch(`api/search_businesses.php?q=${encodeURIComponent(query)}`);
            const data = await response.json();

            // Show results container
            resultsContainer.style.display = 'block';
            searchTermDisplay.textContent = query;

            // Set Add New Link
            addNewOption.href = `business-signup.php?biz_name=${encodeURIComponent(query)}`;

            // Render existing
            existingResults.innerHTML = '';

            if (data.length > 0) {
                document.querySelector('.ac-separator').style.display = 'block';

                data.forEach(biz => {
                    const div = document.createElement('div');
                    div.className = 'autocomplete-item existing-item';
                    div.innerHTML = `
                        <div class="ac-text-box" style="margin-left: 0;">
                            <span class="ac-main-text">${biz.name}</span>
                            <span class="ac-sub-text">${biz.location}</span>
                        </div>
                    `;
                    div.onclick = () => showClaimedModal(biz.name);
                    existingResults.appendChild(div);
                });
            } else {
                document.querySelector('.ac-separator').style.display = 'none';
            }

        } catch (error) {
            console.error('Error:', error);
        }
    };

    searchInput.addEventListener('input', debounce((e) => {
        fetchBusinesses(e.target.value);
    }, 300));

    // Handle closing modal
    closeModal.onclick = () => modal.style.display = 'none';
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }

    function showClaimedModal(name) {
        modalBizName.textContent = name;
        modal.style.display = 'flex';
        // Hide Dropdown
        resultsContainer.style.display = 'none';
    }
</script>

<?php require_once 'includes/footer.php'; ?>
