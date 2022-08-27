import template from './sw-order-detail-comments.html.twig';
import './sw-order-detail-comments.scss';

const { Component, Context, Mixin } = Shopware;
const Criteria = Shopware.Data.Criteria;

Component.register('sw-order-detail-comments', {
    template,

    inject: [
        'repositoryFactory',
    ],

    mixins: [
        Mixin.getByName('listing'),
    ],

    data() {
        return {
            currentOrderCommentId: null,
            showOrderCommentModal: false,
            deleteOrderCommentId: null,
            isLoading: true,
            orderComments: [],
            limit: 10,
            sortBy: 'createdAt',
            sortDirection: 'DESC',
        };
    },

    metaInfo() {
        return {
            title: 'Comments',
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
                .addSorting(Criteria.sort(this.sortBy, this.sortDirection))
                .addFilter(Criteria.equals('orderId', orderId));


            if (this.term !== null) {
                criteria.setTerm(this.term);
            }

            return criteria;
        },
    },

    methods: {
        getList() {
            this.isLoading = true;

            return this.orderCommentRepository.search(this.orderCommentCriteria).then((searchResult) => {
                this.total = searchResult.total;
                this.orderComments = searchResult;
                this.isLoading = false;
                return this.orderComments;
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

        getColumns() {
            return [{
                property: 'content',
                dataIndex: 'content',
                label: 'sw-order.commentCard.columnContent',
                width: '250px',
                primary: true,
            }, {
                property: 'createdBy',
                label: 'sw-order.commentCard.columnCreatedBy',
            }, {
                property: 'createdAt',
                label: 'sw-order.commentCard.columnCreatedAt',
            }, {
                property: 'updatedAt',
                label: 'sw-order.commentCard.columnUpdatedAt',
            }, {
                property: 'internal',
                label: 'sw-order.commentCard.columnInternal',
            }];
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
