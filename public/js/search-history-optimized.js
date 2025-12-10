(function ($) {
    "use strict";
    const CacheManager = {
        data: null,
        timestamp: null,
        duration: 5 * 60 * 1000,
        storageKey: "mwshop_search_history_cache",

        get: function () {
            if (this.isValid()) {
                return this.data;
            }

            try {
                const cached = localStorage.getItem(this.storageKey);
                if (cached) {
                    const parsed = JSON.parse(cached);
                    if (Date.now() - parsed.timestamp < this.duration) {
                        this.data = parsed.data;
                        this.timestamp = parsed.timestamp;
                        return this.data;
                    }
                }
            } catch (e) {
                console.warn("LocalStorage error:", e);
            }

            return null;
        },

        set: function (data) {
            this.data = data;
            this.timestamp = Date.now();

            try {
                localStorage.setItem(
                    this.storageKey,
                    JSON.stringify({
                        data: data,
                        timestamp: this.timestamp,
                    })
                );
            } catch (e) {
                console.warn("LocalStorage save failed:", e);
            }
        },

        invalidate: function () {
            this.data = null;
            this.timestamp = null;
            try {
                localStorage.removeItem(this.storageKey);
            } catch (e) {
                console.warn("LocalStorage clear failed:", e);
            }
        },

        isValid: function () {
            if (!this.data || !this.timestamp) {
                return false;
            }
            return Date.now() - this.timestamp < this.duration;
        },
    };

    function debounce(func, delay) {
        let timeoutId;
        return function (...args) {
            const context = this;
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => {
                func.apply(context, args);
            }, delay);
        };
    }

    function throttle(func, limit) {
        let inThrottle;
        return function (...args) {
            const context = this;
            if (!inThrottle) {
                func.apply(context, args);
                inThrottle = true;
                setTimeout(() => (inThrottle = false), limit);
            }
        };
    }

    const SearchHistoryModule = {
        config: {
            debounceDelay: 400,
            throttleLimit: 500,
            maxRetries: 2,
            retryDelay: 1000,
        },

        state: {
            isLoading: false,
            isInitialized: false,
            currentRequest: null,
            retryCount: 0,
        },

        elements: {},

        init: function () {
            if (this.state.isInitialized) {
                console.warn("SearchHistoryModule already initialized");
                return;
            }

            this.cacheElements();

            this.bindEvents();

            this.state.isInitialized = true;
        },

        cacheElements: function () {
            this.elements = {
                searchInput: $("#headerSearchInput"),
                searchForm: $("#headerSearchForm"),
                suggestionsDropdown: $("#searchSuggestions"),
                historyList: $("#historyList"),
                historySection: $("#searchHistorySection"),
            };
        },

        bindEvents: function () {
            const self = this;
            const { searchInput, searchForm, suggestionsDropdown } =
                this.elements;

            searchInput.click("focus", function () {
                self.loadHistory(true);
            });

            const debouncedLoad = debounce(function () {
                self.loadHistory(false);
            }, this.config.debounceDelay);

            searchInput.on("input", function () {
                self.showDropdown();
                debouncedLoad();
            });

            $(document).on("click", ".history-item", function (e) {
                if (!$(e.target).closest(".btn-delete-history").length) {
                    const keyword = $(this).data("keyword");
                    searchInput.val(keyword);
                    searchForm.submit();
                }
            });

            $(document).on("click", ".btn-delete-history", function (e) {
                e.stopPropagation();
                const id = $(this).data("id");
                self.deleteHistoryItem(id, $(this).closest(".history-item"));
            });

            $(document).on("click", function (e) {
                if (!$(e.target).closest(".search-wrapper").length) {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on("keydown", function (e) {
                if (e.key === "Escape") {
                    suggestionsDropdown.hide();
                }
            });

            searchInput.on(
                "keyup",
                throttle(function (e) {
                    if (
                        $(this).val().trim().length === 0 &&
                        e.key === "Backspace"
                    ) {
                        self.loadHistory(false);
                    }
                }, this.config.throttleLimit)
            );
        },

        loadHistory: function (forceRefresh = false) {
            const self = this;

            if (this.state.isLoading) {
                return;
            }

            if (!forceRefresh) {
                const cachedData = CacheManager.get();
                if (cachedData !== null) {
                    this.renderHistory(cachedData);
                    this.showDropdown();

                    this.backgroundRefresh();
                    return;
                }
            }

            this.showLoadingState();

            if (this.state.currentRequest) {
                this.state.currentRequest.abort();
            }

            this.state.isLoading = true;

            this.state.currentRequest = $.ajax({
                url: "/api/search-history",
                method: "GET",
                timeout: 5000,
                success: function (response) {
                    self.state.isLoading = false;
                    self.state.retryCount = 0;

                    if (response.success && response.data) {
                        CacheManager.set(response.data);

                        self.renderHistory(response.data);
                        self.showDropdown();
                    } else {
                        self.showEmptyState();
                    }
                },
                error: function (xhr, status, error) {
                    self.state.isLoading = false;

                    if (status === "abort") {
                        return;
                    }

                    if (self.state.retryCount < self.config.maxRetries) {
                        self.state.retryCount++;
                        setTimeout(function () {
                            self.loadHistory(forceRefresh);
                        }, self.config.retryDelay);
                    } else {
                        console.error("✗ Failed to load history:", error);
                        self.showErrorState();
                        self.state.retryCount = 0;
                    }
                },
                complete: function () {
                    self.state.currentRequest = null;
                },
            });
        },

        backgroundRefresh: function () {
            const self = this;

            $.ajax({
                url: "/api/search-history",
                method: "GET",
                success: function (response) {
                    if (response.success && response.data) {
                        CacheManager.set(response.data);
                    }
                },
                error: function () {},
            });
        },

        deleteHistoryItem: function (id, $element) {
            const self = this;

            if (!id) {
                console.error("Invalid history ID");
                return;
            }

            $element.fadeOut(200, function () {
                $(this).remove();

                if (
                    self.elements.historyList.children(".history-item")
                        .length === 0
                ) {
                    self.showEmptyState();
                }
            });

            CacheManager.invalidate();

            $.ajax({
                url: "/api/search-history/" + id,
                method: "DELETE",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr(
                        "content"
                    ),
                },
                success: function (response) {},
                error: function (xhr) {
                    console.error("✗ Delete failed:", xhr);

                    if (xhr.status !== 404) {
                        self.loadHistory(true);
                    }
                },
            });
        },

        renderHistory: function (history) {
            const self = this;
            const { historyList, historySection } = this.elements;

            if (!history || history.length === 0) {
                this.showEmptyState();
                return;
            }

            let html = "";
            history.forEach(function (item) {
                if (item && item.keyword && item.id) {
                    html += `
                        <div class="history-item" data-keyword="${
                            item.keyword
                        }">
                            <div class="history-item-content">
                                <i class="bi bi-search" style="color: #6c757d;"></i>
                                <span class="history-keyword">${self.escapeHtml(
                                    item.keyword
                                )}</span>
                            </div>
                            <button type="button" class="btn-delete-history" data-id="${
                                item.id
                            }" title="Xóa">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    `;
                }
            });

            historyList.html(html);
            historySection.show();
        },

        escapeHtml: function (text) {
            const map = {
                "&": "&amp;",
                "<": "&lt;",
                ">": "&gt;",
                '"': "&quot;",
                "'": "&#039;",
            };
            return text.replace(/[&<>"']/g, (m) => map[m]);
        },

        showDropdown: function () {
            this.elements.suggestionsDropdown.show();
            this.elements.historySection.show();
        },

        showLoadingState: function () {
            this.elements.historyList.html(
                '<div class="text-center py-3"><i class="bi bi-hourglass-split"></i> Đang tải...</div>'
            );
            this.showDropdown();
        },

        showEmptyState: function () {
            this.elements.historyList.html(
                '<div class="text-center py-3 text-muted"><i class="bi bi-inbox"></i> Chưa có lịch sử tìm kiếm</div>'
            );
            this.showDropdown();
        },

        showErrorState: function () {
            this.elements.historyList.html(
                '<div class="text-center py-3 text-danger"><i class="bi bi-exclamation-triangle"></i> Không thể tải lịch sử</div>'
            );
            this.showDropdown();
        },
    };

    $(document).ready(function () {
        SearchHistoryModule.init();
    });

    window.SearchHistoryModule = SearchHistoryModule;
    window.CacheManager = CacheManager;
})(jQuery);
