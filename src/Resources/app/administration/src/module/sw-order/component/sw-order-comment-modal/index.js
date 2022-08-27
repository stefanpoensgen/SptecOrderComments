import template from './sw-order-comment-modal.html.twig';
import './sw-order-comment-modal.scss';

const { Component } = Shopware;

Component.register('sw-order-comment-modal', {
    template,

    inject: [
        'repositoryFactory',
    ],

    props: {
        orderId: {
            type: String,
            required: true,
        },
        orderCommentId: {
            type: String,
            required: false,
            default: null,
        },
    },

    data() {
        return {
            isLoading: true,
            orderComment: undefined,
        };
    },

    computed: {
        orderCommentRepository() {
            return this.repositoryFactory.create('sptec_order_comment');
        },

        primaryActionDisabled() {
            return !this.orderComment || this.orderComment.content === '';
        },

        currentUser() {
            return Shopware.State.get('session').currentUser;
        },

        userName() {
            if (!this.currentUser) {
                return '';
            }

            return `${this.currentUser.firstName} ${this.currentUser.lastName}`;
        },
    },

    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            if (this.orderCommentId) {
                this.getOrderComment();
                return;
            }

            this.orderComment = this.orderCommentRepository.create(Shopware.Context.api);
            this.orderComment.createdById = this.currentUser.id;
            this.orderComment.orderId = this.orderId;
            this.orderComment.internal = true;
            this.isLoading = false;
        },

        closeModal() {
            this.$emit('close-modal');
        },

        saveComment() {
            this.orderCommentRepository.save(this.orderComment, Shopware.Context.api).then(() => {
                this.closeModal();
                this.$emit('reload-order-comments');
            });
        },

        getOrderComment() {
            this.isLoading = true;
            this.orderCommentRepository
                .get(this.orderCommentId, Shopware.Context.api)
                .then(orderComment => {
                    this.orderComment = orderComment;
                    this.isLoading = false;
                });
        },
    },
});
