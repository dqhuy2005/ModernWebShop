const SuccessNotification = {
    /**
     * Show add to cart success notification
     */
    addToCart() {
        return NotificationBase.show('success', 'Sản phẩm đã được thêm vào Giỏ hàng', 2000);
    },

    /**
     * Show generic success notification
     * @param {string} message - Success message
     * @param {number} duration - Duration in milliseconds
     */
    show(message, duration = 2000) {
        return NotificationBase.show('success', message, duration);
    },

    /**
     * Show cart update success
     */
    cartUpdated() {
        return NotificationBase.show('success', 'Đã cập nhật giỏ hàng', 1500);
    },

    /**
     * Show item removed success
     */
    itemRemoved() {
        return NotificationBase.show('success', 'Đã xóa sản phẩm khỏi giỏ hàng', 1500);
    },

    /**
     * Show profile updated success
     */
    profileUpdated() {
        return NotificationBase.show('success', 'Đã cập nhật thông tin cá nhân', 2000);
    },

    /**
     * Show order placed success
     */
    orderPlaced() {
        return NotificationBase.show('success', 'Đặt hàng thành công!', 2000);
    }
};

if (typeof module !== 'undefined' && module.exports) {
    module.exports = SuccessNotification;
}
