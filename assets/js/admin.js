/**
 * Admin JavaScript for SAM Newsletter
 * Handles search and filter functionality
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        const $searchInput = $('#sam-newsletter-search');
        const $searchBtn = $('#sam-newsletter-search-btn');
        const $resetBtn = $('#sam-newsletter-reset-btn');
        const $results = $('#sam-newsletter-results');
        const $loading = $('#sam-newsletter-loading');
        const $tbody = $('#sam-newsletter-tbody');
        
        /**
         * Search button click handler
         */
        $searchBtn.on('click', function() {
            performSearch();
        });
        
        /**
         * Enter key on search input
         */
        $searchInput.on('keypress', function(e) {
            if (e.which === 13) {
                e.preventDefault();
                performSearch();
            }
        });
        
        /**
         * Reset button click handler
         */
        $resetBtn.on('click', function() {
            $searchInput.val('');
            location.reload();
        });
        
        /**
         * Perform search via AJAX
         */
        function performSearch() {
            const searchTerm = $searchInput.val().trim();
            
            // Show loading state
            $results.hide();
            $loading.show();
            $searchBtn.prop('disabled', true);
            
            $.ajax({
                url: samNewsletterAdmin.ajax_url,
                type: 'POST',
                data: {
                    action: 'sam_newsletter_search',
                    nonce: samNewsletterAdmin.nonce,
                    search: searchTerm
                },
                success: function(response) {
                    if (response.success) {
                        renderResults(response.data.subscribers);
                    } else {
                        showError(response.data.message);
                    }
                },
                error: function() {
                    showError('An error occurred while searching. Please try again.');
                },
                complete: function() {
                    $loading.hide();
                    $results.show();
                    $searchBtn.prop('disabled', false);
                }
            });
        }
        
        /**
         * Render search results
         */
        function renderResults(subscribers) {
            if (subscribers.length === 0) {
                $tbody.html(
                    '<tr><td colspan="4" style="text-align: center; padding: 20px;">' +
                    'No subscribers found matching your search.' +
                    '</td></tr>'
                );
                return;
            }
            
            let html = '';
            
            subscribers.forEach(function(subscriber) {
                const date = formatDate(subscriber.created_at);
                
                html += '<tr>' +
                    '<td class="column-id">' + escapeHtml(subscriber.id) + '</td>' +
                    '<td class="column-name">' + escapeHtml(subscriber.name) + '</td>' +
                    '<td class="column-email">' +
                        '<a href="mailto:' + escapeHtml(subscriber.email) + '">' +
                            escapeHtml(subscriber.email) +
                        '</a>' +
                    '</td>' +
                    '<td class="column-date">' + escapeHtml(date) + '</td>' +
                    '</tr>';
            });
            
            $tbody.html(html);
        }
        
        /**
         * Show error message
         */
        function showError(message) {
            $tbody.html(
                '<tr><td colspan="4" style="text-align: center; padding: 20px; color: #d63638;">' +
                escapeHtml(message) +
                '</td></tr>'
            );
        }
        
        /**
         * Format date string
         */
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleString();
        }
        
        /**
         * Escape HTML to prevent XSS
         */
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
        }
        
    });
    
})(jQuery);