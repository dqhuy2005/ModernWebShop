const NotificationBase = {
    /**
     * Show a notification with custom content
     * @param {string} type - Notification type (success, error, warning, info)
     * @param {string} message - Message to display
     * @param {number} duration - Duration in milliseconds (default: 2000)
     */
    show(type, message, duration = 2000) {
        const notificationId = `notification-${type}-${Date.now()}`;
        const notification = this.createNotification(
            notificationId,
            type,
            message
        );

        document.body.appendChild(notification);

        requestAnimationFrame(() => {
            notification.classList.add("show");
        });

        setTimeout(() => {
            this.dismiss(notificationId);
        }, duration);

        return notificationId;
    },

    /**
     * Create notification element
     */
    createNotification(id, type, message) {
        const notification = document.createElement("div");
        notification.id = id;
        notification.className = `custom-notification custom-notification-${type}`;

        const config = this.getConfig(type);

        notification.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">
                    <i class="${config.icon}"></i>
                </div>
                <p class="notification-message">${message}</p>
            </div>
        `;

        return notification;
    },

    /**
     * Get notification configuration by type
     */
    getConfig(type) {
        const configs = {
            success: {
                icon: "fas fa-check",
                color: "#26aa99",
            },
            error: {
                icon: "fas fa-times",
                color: "#ff4d4f",
            },
            warning: {
                icon: "fas fa-exclamation-triangle",
                color: "#faad14",
            },
            info: {
                icon: "fas fa-info-circle",
                color: "#1890ff",
            },
        };

        return configs[type] || configs.info;
    },

    /**
     * Dismiss notification
     */
    dismiss(notificationId) {
        const notification = document.getElementById(notificationId);
        if (notification) {
            notification.classList.remove("show");
            setTimeout(() => {
                notification.remove();
            }, 300);
        }
    },
};

if (typeof module !== "undefined" && module.exports) {
    module.exports = NotificationBase;
}
