import template from './sptec-order-comments-item.html.twig';
import './sptec-order-comments-item.scss';

const { Component } = Shopware;

Component.register('sptec-order-comments-item', {
    template,

    props: {
        item: {
            type: Object,
            required: true,
        },
    },

    computed: {
        date() {
            return Shopware.Filter.getByName('date');
        },
    }
});
