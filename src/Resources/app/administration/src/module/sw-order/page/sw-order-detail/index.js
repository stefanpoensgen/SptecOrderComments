import template from './sw-order-detail.html.twig';

const { Component } = Shopware;

Component.override('sw-order-detail', {
    template,
});
