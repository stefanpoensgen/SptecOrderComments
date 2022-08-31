import './module/sw-order/page/sw-order-detail';
import './module/sw-order/view/sw-order-detail-comments';
import './module/sw-order/component/sw-order-comment-modal';

const { Module } = Shopware;

Module.register('sptec-order-comments', {
    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.order.detail') {
            currentRoute.children.push({
                name: 'sw.order.detail.comments',
                path: '/sw/order/detail/:id/comments',
                component: 'sw-order-detail-comments',
                meta: {
                    parentPath: 'sw.order.index',
                    privilege: 'order.viewer',
                },
            });
        }
        next(currentRoute);
    },
});
