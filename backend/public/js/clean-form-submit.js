/**
 * Clean Form Submission Utility
 *
 * Automatically removes empty parameters from form submissions
 * to keep URLs clean and readable
 *
 * Usage:
 * 1. Add class "clean-form" to your form
 * 2. That's it! The script will handle the rest automatically
 *
 * Example:
 * <form action="/admin/orders" method="GET" class="clean-form">
 *     <input type="text" name="search">
 *     <input type="text" name="status">
 *     <button type="submit">Search</button>
 * </form>
 *
 * Result:
 * - If search is empty: /admin/orders?status=pending
 * - If both are empty: /admin/orders
 * - If both have values: /admin/orders?search=test&status=pending
 */

(function ($) {
    "use strict";

    /**
     * Initialize clean form submission
     */
    function initCleanFormSubmit() {
        // Handle all forms with class "clean-form"
        $(document).on(
            "submit",
            ".clean-form, form[data-clean-submit]",
            function (e) {
                e.preventDefault();

                const form = $(this);
                const baseUrl = form.attr("action");
                const params = new URLSearchParams();

                // Get all form data
                const formData = new FormData(this);

                // Add only non-empty values
                for (let [key, value] of formData.entries()) {
                    // Convert to string and trim
                    value = String(value).trim();

                    // Skip empty values
                    if (value === "" || value === null || value === undefined) {
                        continue;
                    }

                    // Skip default values if specified
                    const input = form.find(`[name="${key}"]`);
                    const defaultValue = input.data("default");
                    if (
                        defaultValue !== undefined &&
                        value === String(defaultValue)
                    ) {
                        continue;
                    }

                    params.append(key, value);
                }

                // Build clean URL
                const queryString = params.toString();
                const cleanUrl = queryString
                    ? `${baseUrl}?${queryString}`
                    : baseUrl;

                // Navigate to clean URL
                window.location.href = cleanUrl;

                return false;
            }
        );
    }

    /**
     * Remove empty parameters from current URL
     */
    function cleanCurrentUrl() {
        const url = new URL(window.location.href);
        const params = new URLSearchParams(url.search);
        const cleanParams = new URLSearchParams();

        for (let [key, value] of params.entries()) {
            if (value.trim() !== "") {
                cleanParams.append(key, value);
            }
        }

        const newUrl = cleanParams.toString()
            ? `${url.origin}${url.pathname}?${cleanParams.toString()}`
            : `${url.origin}${url.pathname}`;

        if (newUrl !== window.location.href) {
            window.history.replaceState({}, "", newUrl);
        }
    }

    /**
     * Build query string from object, skipping empty values
     */
    function buildCleanQueryString(params) {
        const cleanParams = new URLSearchParams();

        for (let [key, value] of Object.entries(params)) {
            if (value !== "" && value !== null && value !== undefined) {
                cleanParams.append(key, String(value).trim());
            }
        }

        return cleanParams.toString();
    }

    /**
     * Navigate with clean parameters
     */
    function navigateWithCleanParams(baseUrl, params) {
        const queryString = buildCleanQueryString(params);
        const url = queryString ? `${baseUrl}?${queryString}` : baseUrl;
        window.location.href = url;
    }

    // Auto-initialize when document is ready
    $(document).ready(function () {
        initCleanFormSubmit();

        // Clean current URL on page load (optional)
        // Uncomment the line below if you want to clean URL on page load
        cleanCurrentUrl();
    });

    // Expose utility functions globally
    window.CleanFormUtils = {
        buildCleanQueryString: buildCleanQueryString,
        navigateWithCleanParams: navigateWithCleanParams,
        cleanCurrentUrl: cleanCurrentUrl,
    };
})(jQuery);
