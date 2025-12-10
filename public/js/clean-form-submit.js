(function ($) {
    "use strict";

    function initCleanFormSubmit() {
        $(document).on(
            "submit",
            ".clean-form, form[data-clean-submit]",
            function (e) {
                e.preventDefault();

                const form = $(this);
                const baseUrl = form.attr("action");
                const params = new URLSearchParams();

                const formData = new FormData(this);

                for (let [key, value] of formData.entries()) {
                    value = String(value).trim();

                    if (value === "" || value === null || value === undefined) {
                        continue;
                    }

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

                const queryString = params.toString();
                const cleanUrl = queryString
                    ? `${baseUrl}?${queryString}`
                    : baseUrl;

                window.location.href = cleanUrl;

                return false;
            }
        );
    }

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

    function buildCleanQueryString(params) {
        const cleanParams = new URLSearchParams();

        for (let [key, value] of Object.entries(params)) {
            if (value !== "" && value !== null && value !== undefined) {
                cleanParams.append(key, String(value).trim());
            }
        }

        return cleanParams.toString();
    }

    function navigateWithCleanParams(baseUrl, params) {
        const queryString = buildCleanQueryString(params);
        const url = queryString ? `${baseUrl}?${queryString}` : baseUrl;
        window.location.href = url;
    }

    $(document).ready(function () {
        initCleanFormSubmit();

        cleanCurrentUrl();
    });

    window.CleanFormUtils = {
        buildCleanQueryString: buildCleanQueryString,
        navigateWithCleanParams: navigateWithCleanParams,
        cleanCurrentUrl: cleanCurrentUrl,
    };
})(jQuery);
