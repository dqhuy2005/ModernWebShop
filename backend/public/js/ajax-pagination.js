/**
 * AJAX Pagination Manager
 * Handles AJAX-based pagination with race condition prevention
 * 
 * Usage:
 * const userPagination = new AjaxPagination({
 *     containerId: 'users-table-container',
 *     paginationSelector: 'nav[aria-label="Users pagination"]',
 *     onCountsUpdate: function(counts) {
 *         $('#totalUsersCount').text(counts.total);
 *         $('#activeUsersCount').text(counts.active);
 *     }
 * });
 */

class AjaxPagination {
    constructor(options) {
        // Required options
        this.containerId = options.containerId;
        this.paginationSelector = options.paginationSelector || 'nav[aria-label="Pagination"]';
        
        // Optional callbacks
        this.onCountsUpdate = options.onCountsUpdate || null;
        this.onBeforeLoad = options.onBeforeLoad || null;
        this.onAfterLoad = options.onAfterLoad || null;
        this.onError = options.onError || null;
        
        // Configuration
        this.scrollOffset = options.scrollOffset || 100;
        this.scrollDuration = options.scrollDuration || 300;
        this.enableHistory = options.enableHistory !== false; // Default true
        
        // State management
        this.currentRequest = null; // Track current AJAX request
        this.isLoading = false; // Loading state flag
        
        // Initialize
        this.init();
    }
    
    init() {
        this.attachPaginationHandlers();
        this.attachPopStateHandler();
    }
    
    /**
     * Load page with race condition prevention
     */
    loadPage(url) {
        // Abort previous request if still pending
        if (this.currentRequest && this.currentRequest.readyState !== 4) {
            console.log('[AjaxPagination] Aborting previous request');
            this.currentRequest.abort();
        }
        
        // Prevent multiple simultaneous requests
        if (this.isLoading) {
            console.log('[AjaxPagination] Request already in progress, skipping');
            return;
        }
        
        this.isLoading = true;
        const container = $('#' + this.containerId);
        
        // Call before load callback
        if (this.onBeforeLoad) {
            this.onBeforeLoad();
        }
        
        // Store reference to current request
        this.currentRequest = $.ajax({
            url: url,
            type: 'GET',
            beforeSend: function() {
                container.addClass('loading');
            },
            success: (response) => {
                // Extract and update container content
                // Check if response already contains the target container
                const $response = $(response);
                let newContent;
                
                // Try to find the container in the response
                const foundContainer = $response.find('#' + this.containerId);
                if (foundContainer.length > 0) {
                    // Container found in response, extract its HTML
                    newContent = foundContainer.html();
                } else {
                    // Container not found, use response as-is (likely a partial view)
                    newContent = response;
                }
                
                container.html(newContent);
                
                // Update browser history
                if (this.enableHistory) {
                    window.history.pushState({ url: url }, '', url);
                }
                
                // Smooth scroll to container
                this.scrollToContainer();
                
                // Re-attach pagination handlers
                this.attachPaginationHandlers();
                
                // Update counts if callback provided
                if (this.onCountsUpdate) {
                    const countsData = this.extractCounts(response);
                    if (countsData) {
                        this.onCountsUpdate(countsData);
                    }
                }
                
                // Call after load callback
                if (this.onAfterLoad) {
                    this.onAfterLoad(response);
                }
            },
            error: (xhr) => {
                // Ignore aborted requests
                if (xhr.statusText === 'abort') {
                    console.log('[AjaxPagination] Request aborted');
                    return;
                }
                
                console.error('[AjaxPagination] Failed to load page:', xhr);
                
                // Call error callback or show default error
                if (this.onError) {
                    this.onError(xhr);
                } else {
                    this.showError('Failed to load page');
                }
            },
            complete: () => {
                container.removeClass('loading');
                this.isLoading = false;
                this.currentRequest = null;
            }
        });
    }
    
    /**
     * Attach click handlers to pagination links
     */
    attachPaginationHandlers() {
        const self = this;
        
        // Use event delegation to handle dynamically loaded pagination links
        $(document).off('click', `#${this.containerId} ${this.paginationSelector} a.page-link`);
        $(document).on('click', `#${this.containerId} ${this.paginationSelector} a.page-link`, function(e) {
            e.preventDefault();
            
            const $link = $(this);
            const url = $link.attr('href');
            const $parent = $link.parent();
            
            // Ignore disabled and active links
            if (!url || $parent.hasClass('disabled') || $parent.hasClass('active')) {
                return;
            }
            
            // Prevent clicking if already loading
            if (self.isLoading) {
                console.log('[AjaxPagination] Loading in progress, ignoring click');
                return;
            }
            
            self.loadPage(url);
        });
    }
    
    /**
     * Handle browser back/forward buttons
     */
    attachPopStateHandler() {
        if (!this.enableHistory) return;
        
        const self = this;
        window.addEventListener('popstate', function(event) {
            if (event.state && event.state.url) {
                self.loadPage(event.state.url);
            }
        });
    }
    
    /**
     * Smooth scroll to container
     */
    scrollToContainer() {
        const container = $('#' + this.containerId);
        if (container.length) {
            $('html, body').animate({
                scrollTop: container.offset().top - this.scrollOffset
            }, this.scrollDuration);
        }
    }
    
    /**
     * Extract counts from response (if present in data attributes or meta)
     */
    extractCounts(response) {
        try {
            const $response = $(response);
            const countsElement = $response.find('[data-counts]');
            
            if (countsElement.length) {
                return JSON.parse(countsElement.attr('data-counts'));
            }
        } catch (e) {
            console.warn('[AjaxPagination] Failed to extract counts:', e);
        }
        return null;
    }
    
    /**
     * Show error message
     */
    showError(message) {
        if (typeof toastr !== 'undefined') {
            toastr.error(message);
        } else {
            alert(message);
        }
    }
    
    /**
     * Destroy instance and cleanup
     */
    destroy() {
        // Abort any pending request
        if (this.currentRequest && this.currentRequest.readyState !== 4) {
            this.currentRequest.abort();
        }
        
        // Remove event handlers
        $(document).off('click', `#${this.containerId} ${this.paginationSelector} a.page-link`);
        
        this.isLoading = false;
        this.currentRequest = null;
    }
}

// Export for module usage
if (typeof module !== 'undefined' && module.exports) {
    module.exports = AjaxPagination;
}
