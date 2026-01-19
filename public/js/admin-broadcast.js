/**
 * Admin Panel - City-Wise Broadcast JavaScript
 * 
 * This script provides real-time user count preview when selecting cities
 * for broadcast messages.
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        userCountEndpoint: '/admin/users/count',
        broadcastStatsEndpoint: '/admin/broadcast-stats',
        debounceDelay: 300
    };

    // Utility: Debounce function
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

    // Utility: Format number with commas
    function formatNumber(num) {
        return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
    }

    // Fetch user count for selected city
    async function fetchUserCount(city) {
        try {
            const response = await fetch(`${CONFIG.userCountEndpoint}?city=${encodeURIComponent(city)}`);
            const data = await response.json();
            
            if (data.success) {
                return data;
            }
            throw new Error('Failed to fetch user count');
        } catch (error) {
            console.error('Error fetching user count:', error);
            return null;
        }
    }

    // Fetch broadcast statistics
    async function fetchBroadcastStats() {
        try {
            const response = await fetch(CONFIG.broadcastStatsEndpoint);
            const data = await response.json();
            
            if (data.success) {
                return data;
            }
            throw new Error('Failed to fetch broadcast stats');
        } catch (error) {
            console.error('Error fetching broadcast stats:', error);
            return null;
        }
    }

    // Display user count info
    function displayUserCount(data, targetElement) {
        if (!data || !targetElement) return;

        let html = '';

        if (data.city === 'all') {
            html = `
                <div class="alert alert-info mt-2">
                    <i class="fas fa-globe"></i> 
                    <strong>Broadcasting to All Cities</strong><br>
                    <span class="text-muted">Total Recipients: <strong>${formatNumber(data.total_users)}</strong> users across <strong>${data.cities.length}</strong> cities</span>
                </div>
            `;
        } else {
            const emoji = data.count > 0 ? '✅' : '⚠️';
            const alertType = data.count > 0 ? 'alert-success' : 'alert-warning';
            
            html = `
                <div class="alert ${alertType} mt-2">
                    ${emoji} <strong>${data.city}</strong><br>
                    <span class="text-muted">Recipients: <strong>${formatNumber(data.count)}</strong> users</span>
                    ${data.count === 0 ? '<br><small>No users found in this city</small>' : ''}
                </div>
            `;
        }

        targetElement.innerHTML = html;
        targetElement.style.display = 'block';
    }

    // Display city breakdown modal
    function showCityBreakdown(stats) {
        if (!stats || !stats.city_breakdown) return;

        let modalContent = `
            <div class="modal fade" id="cityBreakdownModal" tabindex="-1">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title">City-Wise User Distribution</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                        </div>
                        <div class="modal-body">
                            <div class="row mb-3">
                                <div class="col-md-4">
                                    <div class="card bg-primary text-white">
                                        <div class="card-body text-center">
                                            <h3>${formatNumber(stats.total_users)}</h3>
                                            <p class="mb-0">Total Users</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h3>${stats.total_cities}</h3>
                                            <p class="mb-0">Cities</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card bg-info text-white">
                                        <div class="card-body text-center">
                                            <h3>${formatNumber(stats.total_messages_sent)}</h3>
                                            <p class="mb-0">Messages Sent</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <h6 class="mb-3">User Distribution by City</h6>
                            <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                                <table class="table table-striped table-hover">
                                    <thead class="sticky-top bg-white">
                                        <tr>
                                            <th>City</th>
                                            <th class="text-end">Users</th>
                                            <th>Distribution</th>
                                        </tr>
                                    </thead>
                                    <tbody>
        `;

        const maxUsers = Math.max(...stats.city_breakdown.map(c => c.user_count));

        stats.city_breakdown.forEach(city => {
            const percentage = ((city.user_count / stats.total_users) * 100).toFixed(1);
            const barWidth = ((city.user_count / maxUsers) * 100).toFixed(1);
            
            modalContent += `
                <tr>
                    <td><strong>${city.city}</strong></td>
                    <td class="text-end">${formatNumber(city.user_count)}</td>
                    <td>
                        <div class="progress" style="height: 20px;">
                            <div class="progress-bar bg-success" role="progressbar" 
                                 style="width: ${barWidth}%" 
                                 aria-valuenow="${city.user_count}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="${maxUsers}">
                                ${percentage}%
                            </div>
                        </div>
                    </td>
                </tr>
            `;
        });

        modalContent += `
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Remove existing modal if present
        const existingModal = document.getElementById('cityBreakdownModal');
        if (existingModal) {
            existingModal.remove();
        }

        // Add new modal to body
        document.body.insertAdjacentHTML('beforeend', modalContent);

        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('cityBreakdownModal'));
        modal.show();
    }

    // Initialize city selector
    function initCitySelector() {
        const citySelect = document.getElementById('citySelect');
        const userCountDisplay = document.getElementById('userCountDisplay');
        const viewStatsBtn = document.getElementById('viewStatsBtn');

        if (!citySelect) {
            console.warn('City selector not found');
            return;
        }

        // Create user count display if it doesn't exist
        if (!userCountDisplay) {
            const displayDiv = document.createElement('div');
            displayDiv.id = 'userCountDisplay';
            displayDiv.style.display = 'none';
            citySelect.parentElement.appendChild(displayDiv);
        }

        // Debounced city change handler
        const handleCityChange = debounce(async () => {
            const selectedCity = citySelect.value;
            const displayElement = document.getElementById('userCountDisplay');

            if (!selectedCity) {
                displayElement.style.display = 'none';
                return;
            }

            // Show loading state
            displayElement.innerHTML = '<div class="alert alert-info mt-2"><i class="fas fa-spinner fa-spin"></i> Loading...</div>';
            displayElement.style.display = 'block';

            // Fetch and display user count
            const data = await fetchUserCount(selectedCity);
            displayUserCount(data, displayElement);
        }, CONFIG.debounceDelay);

        // Attach event listener
        citySelect.addEventListener('change', handleCityChange);

        // Initial load if city is pre-selected
        if (citySelect.value) {
            handleCityChange();
        }

        // View stats button handler
        if (viewStatsBtn) {
            viewStatsBtn.addEventListener('click', async (e) => {
                e.preventDefault();
                const stats = await fetchBroadcastStats();
                if (stats) {
                    showCityBreakdown(stats);
                }
            });
        }
    }

    // Form submission with confirmation
    function initFormConfirmation() {
        const broadcastForm = document.getElementById('broadcastForm');
        
        if (!broadcastForm) return;

        broadcastForm.addEventListener('submit', async function(e) {
            const citySelect = document.getElementById('citySelect');
            const selectedCity = citySelect ? citySelect.value : 'all';
            
            // Get user count for confirmation
            const data = await fetchUserCount(selectedCity);
            
            if (data) {
                const message = data.city === 'all' 
                    ? `Send this message to ${formatNumber(data.total_users)} users across ${data.cities.length} cities?`
                    : `Send this message to ${formatNumber(data.count)} users in ${data.city}?`;
                
                if (!confirm(message)) {
                    e.preventDefault();
                    return false;
                }
            }
        });
    }

    // Initialize live preview of message
    function initMessagePreview() {
        const descriptionInput = document.getElementById('description');
        const previewArea = document.getElementById('messagePreview');
        
        if (!descriptionInput || !previewArea) return;

        descriptionInput.addEventListener('input', debounce(() => {
            const content = descriptionInput.value;
            previewArea.innerHTML = content 
                ? `<div class="alert alert-light"><strong>Preview:</strong><br>${content}</div>`
                : '';
        }, 300));
    }

    // Initialize on DOM ready
    function init() {
        // Wait for DOM to be fully loaded
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                initCitySelector();
                initFormConfirmation();
                initMessagePreview();
            });
        } else {
            initCitySelector();
            initFormConfirmation();
            initMessagePreview();
        }
    }

    // Start initialization
    init();

    // Export for global access if needed
    window.BroadcastHelper = {
        fetchUserCount,
        fetchBroadcastStats,
        showCityBreakdown
    };

})();
