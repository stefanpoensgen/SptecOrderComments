import template from './sptec-order-comments-item.html.twig';
import './sptec-order-comments-item.scss';

const { Component } = Shopware;

/**
 * @private
 */
Component.register('sptec-order-comments-item', {
    template,

    props: {
        item: {
            type: Object,
            required: true,
        },
    },
});
