class AjaxPagination {
    constructor(options) {
        this.containerId = options.containerId;
        this.paginationSelector =
            options.paginationSelector || 'nav[aria-label="Pagination"]';

        this.onCountsUpdate = options.onCountsUpdate || null;
        this.onBeforeLoad = options.onBeforeLoad || null;
        this.onAfterLoad = options.onAfterLoad || null;
        this.onError = options.onError || null;

        this.scrollOffset = options.scrollOffset || 100;
        this.scrollDuration = options.scrollDuration || 300;
        this.enableHistory = options.enableHistory !== false;

        this.currentRequest = null;
        this.isLoading = false;

        this.init();
    }

    init() {
        this.attachPaginationHandlers();
        this.attachPopStateHandler();
    }

    loadPage(url) {
        if (this.currentRequest && this.currentRequest.readyState !== 4) {
            this.currentRequest.abort();
        }

        if (this.isLoading) {
            return;
        }

        this.isLoading = true;
        const container = $("#" + this.containerId);

        if (this.onBeforeLoad) {
            this.onBeforeLoad();
        }

        this.currentRequest = $.ajax({
            url: url,
            type: "GET",
            beforeSend: function () {
                container.addClass("loading");
            },
            success: (response) => {
                const $response = $(response);
                let newContent;

                const foundContainer = $response.find("#" + this.containerId);
                if (foundContainer.length > 0) {
                    newContent = foundContainer.html();
                } else {
                    newContent = response;
                }

                container.html(newContent);

                if (this.enableHistory) {
                    window.history.pushState({ url: url }, "", url);
                }

                this.scrollToContainer();

                this.attachPaginationHandlers();

                if (this.onCountsUpdate) {
                    const countsData = this.extractCounts(response);
                    if (countsData) {
                        this.onCountsUpdate(countsData);
                    }
                }

                if (this.onAfterLoad) {
                    this.onAfterLoad(response);
                }
            },
            error: (xhr) => {
                if (xhr.statusText === "abort") {
                    return;
                }

                console.error("[AjaxPagination] Failed to load page:", xhr);

                if (this.onError) {
                    this.onError(xhr);
                } else {
                    this.showError("Failed to load page");
                }
            },
            complete: () => {
                container.removeClass("loading");
                this.isLoading = false;
                this.currentRequest = null;
            },
        });
    }

    attachPaginationHandlers() {
        const self = this;

        $(document).off(
            "click",
            `#${this.containerId} ${this.paginationSelector} a.page-link`
        );
        $(document).on(
            "click",
            `#${this.containerId} ${this.paginationSelector} a.page-link`,
            function (e) {
                e.preventDefault();

                const $link = $(this);
                const url = $link.attr("href");
                const $parent = $link.parent();

                if (
                    !url ||
                    $parent.hasClass("disabled") ||
                    $parent.hasClass("active")
                ) {
                    return;
                }

                if (self.isLoading) {
                    return;
                }

                self.loadPage(url);
            }
        );
    }

    attachPopStateHandler() {
        if (!this.enableHistory) return;

        const self = this;
        window.addEventListener("popstate", function (event) {
            if (event.state && event.state.url) {
                self.loadPage(event.state.url);
            }
        });
    }

    scrollToContainer() {
        const container = $("#" + this.containerId);
        if (container.length) {
            $("html, body").animate(
                {
                    scrollTop: container.offset().top - this.scrollOffset,
                },
                this.scrollDuration
            );
        }
    }

    extractCounts(response) {
        try {
            const $response = $(response);
            const countsElement = $response.find("[data-counts]");

            if (countsElement.length) {
                return JSON.parse(countsElement.attr("data-counts"));
            }
        } catch (e) {
            console.warn("[AjaxPagination] Failed to extract counts:", e);
        }
        return null;
    }

    showError(message) {
        if (typeof toastr !== "undefined") {
            toastr.error(message);
        } else {
            alert(message);
        }
    }

    destroy() {
        if (this.currentRequest && this.currentRequest.readyState !== 4) {
            this.currentRequest.abort();
        }

        $(document).off(
            "click",
            `#${this.containerId} ${this.paginationSelector} a.page-link`
        );

        this.isLoading = false;
        this.currentRequest = null;
    }
}

if (typeof module !== "undefined" && module.exports) {
    module.exports = AjaxPagination;
}
