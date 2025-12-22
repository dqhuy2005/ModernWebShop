const SuccessNotification = {
    addToCart() {
        return NotificationBase.show(
            "success",
            "Sản phẩm đã được thêm vào Giỏ hàng",
            2000
        );
    },

    show(message, duration = 2000) {
        return NotificationBase.show("success", message, duration);
    },

    cartUpdated() {
        return NotificationBase.show("success", "Đã cập nhật giỏ hàng", 1500);
    },

    itemRemoved() {
        return NotificationBase.show(
            "success",
            "Đã xóa sản phẩm khỏi giỏ hàng",
            1500
        );
    },

    profileUpdated() {
        return NotificationBase.show(
            "success",
            "Đã cập nhật thông tin cá nhân",
            2000
        );
    },

    orderPlaced() {
        return NotificationBase.show("success", "Đặt hàng thành công!", 2000);
    },
};

if (typeof module !== "undefined" && module.exports) {
    module.exports = SuccessNotification;
}
