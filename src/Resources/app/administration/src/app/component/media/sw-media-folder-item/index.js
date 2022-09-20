const { Component } = Shopware;

Component.override('sw-media-folder-item', {
    computed: {
        iconName() {
            let iconName = this.$super('iconName');

            if (
                this.iconConfig.name === 'regular-shopping-bag' &&
                this.iconConfig.color === '#763b8f'
            ) {
                iconName = 'multicolor-folder-thumbnail--purple';
            }

            return iconName;
        },
    },
});
