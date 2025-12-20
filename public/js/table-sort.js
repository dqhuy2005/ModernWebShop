class TableSort {
    constructor(options) {
        this.containerId = options.containerId || "table-container";
        this.currentSortBy = options.sortBy || null;
        this.currentSortOrder = options.sortOrder || "desc";
        this.paginationInstance = options.paginationInstance || null;
        this.onBeforeSort = options.onBeforeSort || null;
        this.onAfterSort = options.onAfterSort || null;
        this.onError = options.onError || null;
    }

    sort(column) {
        if (this.onBeforeSort && typeof this.onBeforeSort === "function") {
            this.onBeforeSort(column);
        }

        if (this.currentSortBy === column) {
            this.currentSortOrder =
                this.currentSortOrder === "asc" ? "desc" : "asc";
        } else {
            this.currentSortBy = column;
            this.currentSortOrder = "desc";
        }

        let url = new URL(window.location.href);
        url.searchParams.set("sort_by", this.currentSortBy);
        url.searchParams.set("sort_order", this.currentSortOrder);
        url.searchParams.set("page", 1);

        if (
            this.paginationInstance &&
            typeof this.paginationInstance.loadPage === "function"
        ) {
            this.paginationInstance.loadPage(url.toString(), () => {
                if (
                    this.onAfterSort &&
                    typeof this.onAfterSort === "function"
                ) {
                    this.onAfterSort(column, this.currentSortOrder);
                }
            });
        } else {
            this.loadSortedData(url.toString());
        }
    }

    loadSortedData(url) {
        const container = document.getElementById(this.containerId);

        if (!container) {
            console.error("Container not found:", this.containerId);
            return;
        }

        container.classList.add("loading");

        $.ajax({
            url: url,
            method: "GET",
            dataType: "html",
            success: (response) => {
                container.innerHTML = response;
                container.classList.remove("loading");

                window.history.pushState({}, "", url);

                if (
                    this.onAfterSort &&
                    typeof this.onAfterSort === "function"
                ) {
                    this.onAfterSort(this.currentSortBy, this.currentSortOrder);
                }
            },
            error: (xhr, status, error) => {
                container.classList.remove("loading");
                console.error("Sort failed:", error);

                if (this.onError && typeof this.onError === "function") {
                    this.onError(xhr, status, error);
                } else if (typeof toastr !== "undefined") {
                    toastr.error("Failed to sort data");
                } else {
                    alert("Failed to sort data");
                }
            },
        });
    }

    getSortBy() {
        return this.currentSortBy;
    }

    getSortOrder() {
        return this.currentSortOrder;
    }

    updateFromUrl() {
        const urlParams = new URLSearchParams(window.location.search);
        this.currentSortBy = urlParams.get("sort_by");
        this.currentSortOrder = urlParams.get("sort_order") || "desc";
    }
}

window.sortTable = function (column) {
    if (window.tableSort && window.tableSort instanceof TableSort) {
        window.tableSort.sort(column);
    } else {
        console.error(
            "TableSort instance not found. Initialize TableSort first."
        );
    }
};
