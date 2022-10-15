Shopware.Component.override('sw-order-list', {

    computed: {
        orderCriteria() {
            const criteria = this.$super('orderCriteria');
            criteria.addAssociation('sptecOrderComments');
            return criteria;
        },

        listFilterOptions() {
            const options = this.$super('listFilterOptions');

            options['comment-filter'] = {
                property: 'sptecOrderComments',
                label: this.$tc('sptec-order-comments.filter.commentFilterLabel'),
                placeholder: this.$tc('sptec-order-comments.filter.commentFilterPlaceholder'),
                optionHasCriteria: this.$tc('sptec-order-comments.filter.orderHasComment'),
                optionNoCriteria: this.$tc('sptec-order-comments.filter.orderNoComment'),
            };

            options['task-filter'] = {
                property: 'sptecOrderComments.task',
                label: this.$tc('sptec-order-comments.filter.taskFilterLabel'),
                placeholder: this.$tc('sptec-order-comments.filter.taskFilterPlaceholder'),
                optionHasCriteria: this.$tc('sptec-order-comments.filter.commentHasTask'),
                optionNoCriteria: this.$tc('sptec-order-comments.filter.commentTaskDone'),
            };

            return options;
        },
    },
    methods: {
        createdComponent() {
            this.defaultFilters.push('comment-filter', 'task-filter');
            this.$super('createdComponent');
        },
    },
});
