import template from './sptec-order-comments.html.twig';
import './sptec-order-comments.scss';

const { Component, Context, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.register('sptec-order-comments', {
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            identifier: '',
            currentOrderCommentId: null,
            showOrderCommentModal: false,
            deleteOrderCommentId: null,
            isLoading: true,
            orderComments: [],
            total: 0,
            limit: 10,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle(this.identifier, this.$tc('sptec-order-comments.tabOrderComments')),
        };
    },

    computed: {
        orderCommentRepository() {
            return this.repositoryFactory.create('sptec_order_comment');
        },

        orderCommentCriteria() {
            const orderId = this.$route.params.id;
            const criteria = new Criteria(this.page, this.limit);

            criteria
                .addAssociation('createdBy')
                .addAssociation('order')
                .addAssociation('media')
                .addSorting(Criteria.sort(this.sortBy, this.sortDirection))
                .addFilter(Criteria.equals('orderId', orderId));

            if (this.term !== null) {
                criteria.setTerm(this.term);
            }

            return criteria;
        },
    },

    watch: {
        isLoading(value) {
            this.$emit('loading-change', value);
        },
    },

    methods: {
        getList() {
            this.orderCommentRepository.search(this.orderCommentCriteria).then((searchResult) => {
                this.total = searchResult.total;
                this.orderComments = searchResult;
                this.identifier = searchResult.first().order.orderNumber;
                this.isLoading = false;
            }).catch(() => {
                this.isLoading = false;
            });
        },

        onChange(term) {
            this.term = term;
            this.orderComments.criteria.setPage(1);
            this.orderComments.criteria.setTerm(term);

            this.getList();
        },

        openModal() {
            this.showOrderCommentModal = true;
        },

        closeModal() {
            this.showOrderCommentModal = false;
            this.currentOrderCommentId = null;
        },

        editComment(orderCommentId) {
            this.currentOrderCommentId = orderCommentId;
            this.openModal();
        },

        deleteComment(orderCommentId) {
            this.deleteOrderCommentId = orderCommentId;
        },

        onConfirmCommentDelete() {
            this.orderCommentRepository.delete(this.deleteOrderCommentId, Context.api).then(() => {
                this.onCancelCommentDelete();
                this.getList();
            });
        },

        onCancelCommentDelete() {
            this.deleteOrderCommentId = null;
        },
    },
});
