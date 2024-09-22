(function(blocks, editor, element, components, api) {

    var el            = element.createElement,
        SelectControl = components.SelectControl,
        Button        = components.Button,
        __            = wp.i18n.__,
        bdata         = TrustreviewsBlockData;

    blocks.registerBlockType('fb-reviews-widget/reviews', {
        title: __('Trust Reviews Block', 'fb-reviews-widget'),
        icon: 'star-filled',
        category: 'widgets',
        keywords: ['trust', 'reviews', 'google', 'facebook', 'yelp', 'block'],
        attributes: {id: {type: 'string'}},

        edit: function(props) {

            var attributes = props.attributes;
            var blockProps = wp.blockEditor.useBlockProps();

            let feeds = bdata.feeds,
                options = [{label: __('Select reviews widget'), value: 0}];

            for (let i = 0; i < feeds.length; i++) {
                options.push({label: feeds[i].name, value: feeds[i].id});
            }

            return el(
                'div',
                blockProps,
                el(
                    SelectControl,
                    {
                        id: 'id',
                        name: 'id',
                        value: props.attributes.id,
                        options: options,
                        onChange: function(newval) {
                            props.setAttributes({id: newval});
                        }
                    }
                ),
                el(
                    Button,
                    {
                        text: __('Edit reviews widget'),
                        href: bdata.builderUrl + '&' + bdata.slg + '_feed_id=' + props.attributes.id,
                        target: '_blank'
                    }
                ),
                el(
                    Button,
                    {
                        text: __('Creat new reviews widget'),
                        href: bdata.builderUrl,
                        target: '_blank'
                    }
                )
            );
        },

        save: function(props) {
            return null;
        }
    });
}(
    window.wp.blocks,
    window.wp.editor,
    window.wp.element,
    window.wp.components,
    window.wp.api
));