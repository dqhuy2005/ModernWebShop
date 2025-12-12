const ConfirmModal = {
    currentCallback: null,
    modalElement: null,

    init() {
        if (!this.modalElement) {
            this.createModal();
        }
    },

    createModal() {
        const modal = document.createElement("div");
        modal.id = "confirmModal";
        modal.className = "confirm-modal";
        modal.innerHTML = `
            <div class="modal-content-wrapper">
                <div class="modal-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <p class="modal-message" id="confirmModalMessage"></p>
                <div class="modal-actions">
                    <button class="btn-cancel" onclick="ConfirmModal.cancel()">Hủy</button>
                    <button class="btn-confirm" onclick="ConfirmModal.confirm()">Xác nhận</button>
                </div>
            </div>
        `;
        document.body.appendChild(modal);
        this.modalElement = modal;
    },

    /**
     * Show confirmation modal
     * @param {string} message - Message to display
     * @param {Function} callback - Callback function when confirmed
     * @param {Object} options - Additional options (icon, confirmText, cancelText)
     */
    show(message, callback, options = {}) {
        this.init();

        this.currentCallback = callback;

        document.getElementById("confirmModalMessage").textContent = message;

        if (options.icon) {
            const iconElement =
                this.modalElement.querySelector(".modal-icon i");
            iconElement.className = options.icon;
        }

        if (options.confirmText) {
            this.modalElement.querySelector(".btn-confirm").textContent =
                options.confirmText;
        }
        if (options.cancelText) {
            this.modalElement.querySelector(".btn-cancel").textContent =
                options.cancelText;
        }

        this.modalElement.classList.add("show");
    },

    /**
     * Confirm action
     */
    confirm() {
        if (this.currentCallback) {
            this.currentCallback();
        }
        this.close();
    },

    /**
     * Cancel action
     */
    cancel() {
        this.close();
    },

    /**
     * Close modal
     */
    close() {
        if (this.modalElement) {
            this.modalElement.classList.remove("show");
            this.currentCallback = null;
        }
    },

    /**
     * Delete confirmation (preset)
     */
    delete(message, callback) {
        this.show(message, callback, {
            icon: "fas fa-trash-alt",
        });
    },

    /**
     * Warning confirmation (preset)
     */
    warning(message, callback) {
        this.show(message, callback, {
            icon: "fas fa-exclamation-triangle",
        });
    },
};

if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", () => ConfirmModal.init());
} else {
    ConfirmModal.init();
}

if (typeof module !== "undefined" && module.exports) {
    module.exports = ConfirmModal;
}
