import template from './sptec-media-item.html.twig';
import './sptec-media-item.scss';

Shopware.Component.register('sptec-media-item', {
    template,

    props: {
        item: {
            required: true,
            type: Object,
        },

        showRemove: {
            type: Boolean,
            required: false,
            default: false,
        },

        hideTooltip: {
            type: Boolean,
            required: false,
            default: false,
        },
    },

    data() {
        return {
            showModal: false,
        };
    },

    computed: {
        title() {
            return `${this.item.media.fileName}.${this.item.media.fileExtension}`;
        },
    },

    methods: {
        download() {
            const el = document.createElement('a');
            el.setAttribute('download', `${this.item.media.fileName}.${this.item.media.fileExtension}`);
            el.setAttribute('rel', 'noopener');
            el.setAttribute('target', '_blank');
            el.setAttribute('href', this.item.media.url);
            el.style.display = 'hidden';

            document.body.appendChild(el);

            el.click();
            this.$nextTick(() => {
                el.parentNode.removeChild(el);
            });
        },
    },
});
