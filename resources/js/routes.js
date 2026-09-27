/**
 * Centralized URL builders for every named backend route (see
 * routes/web.php). This project intentionally skips Ziggy (which would
 * normally provide a route() helper generated from the Laravel side) to
 * keep the dependency list small -- see the "Why no Ziggy" note in the
 * README. Keeping every path in this one file means a route only has
 * to be typed correctly once, instead of independently in every
 * component that links or posts to it.
 */
export const routes = {
    home: () => '/',

    login: () => '/login',
    logout: () => '/logout',
    otpSend: () => '/otp/send',
    otpResend: () => '/otp/resend',
    otpVerify: () => '/otp/verify',
    otpCancel: () => '/otp/cancel',

    profileEdit: () => '/profile',
    profileUpdate: () => '/profile',

    productsIndex: (params = {}) => withQuery('/products', params),
    productShow: (slug) => `/products/${slug}`,

    cartIndex: () => '/cart',
    cartStore: () => '/cart',
    cartUpdate: (cartItemId) => `/cart/${cartItemId}`,
    cartDestroy: (cartItemId) => `/cart/${cartItemId}`,

    wishlistIndex: () => '/wishlist',
    wishlistStore: () => '/wishlist',
    wishlistDestroy: (productId) => `/wishlist/${productId}`,

    checkoutIndex: () => '/checkout',
    checkoutStore: () => '/checkout',

    paymentShow: (orderId) => `/payment/${orderId}`,
    paymentSuccess: (orderId) => `/payment/${orderId}/success`,
    paymentFail: (orderId) => `/payment/${orderId}/fail`,

    ordersIndex: () => '/orders',
    orderShow: (orderId) => `/orders/${orderId}`,
};

function withQuery(base, params) {
    const query = new URLSearchParams();

    Object.entries(params).forEach(([key, value]) => {
        if (value !== null && value !== undefined && value !== '') {
            query.set(key, value);
        }
    });

    const queryString = query.toString();

    return queryString ? `${base}?${queryString}` : base;
}
