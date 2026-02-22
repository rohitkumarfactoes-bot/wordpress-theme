/**
 * Gizmodotech Pro — Editor Blocks
 * Registers custom blocks with Inspector Controls (Sidebar Settings)
 */
(function(blocks, element, components, blockEditor, serverSideRender) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var TextareaControl = components.TextareaControl;
    var SelectControl = components.SelectControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var ServerSideRender = serverSideRender;

    // 1. POST GRID BLOCK
    registerBlockType('gizmodotech/post-grid-block', {
        title: 'Gizmo Post Grid',
        icon: 'grid-view',
        category: 'gizmodotech',
        description: 'Display a grid of latest posts with sidebar settings.',
        attributes: {
            count: { type: 'string', default: '3' },
            type:  { type: 'string', default: 'post' },
            cat:   { type: 'string', default: '' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                element.Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Grid Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Number of Posts',
                            value: attributes.count,
                            onChange: function(val) { setAttributes({ count: val }); },
                            type: 'number'
                        }),
                        el(SelectControl, {
                            label: 'Post Type',
                            value: attributes.type,
                            options: [
                                { label: 'Standard Posts', value: 'post' },
                                { label: 'Tech News', value: 'technews' },
                                { label: 'Reviews', value: 'reviews' }
                            ],
                            onChange: function(val) { setAttributes({ type: val }); }
                        }),
                        el(TextControl, {
                            label: 'Category ID (Optional)',
                            value: attributes.cat,
                            help: 'Enter a category ID to filter posts.',
                            onChange: function(val) { setAttributes({ cat: val }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'gizmodotech/post-grid-block',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Rendered via PHP
        }
    });

    // 2. PROS & CONS BLOCK
    registerBlockType('gizmodotech/pros-cons-block', {
        title: 'Gizmo Pros & Cons',
        icon: 'thumbs-up',
        category: 'gizmodotech',
        description: 'A review box for Pros and Cons.',
        attributes: {
            pros: { type: 'string', default: 'Great Battery|Amazing Screen' },
            cons: { type: 'string', default: 'Expensive|No Headphone Jack' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el(
                element.Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Review Data', initialOpen: true },
                        el(TextareaControl, {
                            label: 'Pros (separate with |)',
                            value: attributes.pros,
                            rows: 4,
                            onChange: function(val) { setAttributes({ pros: val }); }
                        }),
                        el(TextareaControl, {
                            label: 'Cons (separate with |)',
                            value: attributes.cons,
                            rows: 4,
                            onChange: function(val) { setAttributes({ cons: val }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'gizmodotech/pros-cons-block',
                    attributes: attributes
                })
            );
        },
        save: function() {
            return null; // Rendered via PHP
        }
    });

})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender);
