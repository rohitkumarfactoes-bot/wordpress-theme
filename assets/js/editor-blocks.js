/**
 * Gizmodotech Pro — Editor Blocks
 * Registers custom blocks with Inspector Controls (Sidebar Settings)
 */
(function(blocks, element, components, blockEditor, serverSideRender) {
    var el = element.createElement;
    var registerBlockType = blocks.registerBlockType;
    var TextControl = components.TextControl;
    var ToggleControl = components.ToggleControl;
    var TextareaControl = components.TextareaControl;
    var SelectControl = components.SelectControl;
    var PanelBody = components.PanelBody;
    var InspectorControls = blockEditor.InspectorControls;
    var ServerSideRender = serverSideRender.default || serverSideRender;

    // 1. POST GRID BLOCK
    registerBlockType('gizmodotech/post-grid-block', {
        title: 'Gizmo Post Grid',
        icon: 'grid-view',
        category: 'gizmodotech',
        description: 'Display a grid of latest posts with sidebar settings.',
        attributes: {
            count: { type: 'string', default: '3' },
            type:  { type: 'string', default: 'post' },
            cat:   { type: 'string', default: '' },
            pagination: { type: 'boolean', default: false }
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
                                { label: 'Reviews', value: 'reviews' },
                                { label: 'Review (Singular)', value: 'review' }
                            ],
                            onChange: function(val) { setAttributes({ type: val }); }
                        }),
                        el(TextControl, {
                            label: 'Category ID (Optional)',
                            value: attributes.cat,
                            help: 'Enter a category ID to filter posts.',
                            onChange: function(val) { setAttributes({ cat: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Show Pagination',
                            checked: attributes.pagination,
                            onChange: function(val) { setAttributes({ pagination: val }); }
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

    // 3. FLEX CONTAINER BLOCK
    registerBlockType('gizmodotech/flex-container', {
        title: 'Gizmo Flex Container',
        icon: 'layout',
        category: 'gizmodotech',
        description: 'An Elementor-like container for creating flexible layouts with controls for spacing, direction, and alignment.',
        
        // Use the 'supports' property to get built-in controls for color and spacing.
        supports: {
            color: {
                background: true,
                text: true,
            },
            spacing: {
                padding: true, // This adds the padding control to the sidebar
            }
        },

        attributes: {
            // Flexbox attributes
            flexDirection: { type: 'string', default: 'row' },
            justifyContent: { type: 'string' },
            alignItems: { type: 'string' },
            flexWrap: { type: 'string', default: 'nowrap' },
            gap: { type: 'string', default: '1rem' },
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Pulling in the necessary components from the wp global
            var InspectorControls = blockEditor.InspectorControls;
            var useBlockProps = blockEditor.useBlockProps;
            var useInnerBlocksProps = blockEditor.useInnerBlocksProps;
            var PanelBody = components.PanelBody;
            var SelectControl = components.SelectControl;
            var TextControl = components.TextControl;
            var ToggleControl = components.ToggleControl;

            // useBlockProps applies the classes and styles from 'supports' (color, spacing, etc.)
            var blockProps = useBlockProps({
                style: {
                    display: 'flex',
                    flexDirection: attributes.flexDirection,
                    justifyContent: attributes.justifyContent,
                    alignItems: attributes.alignItems,
                    flexWrap: attributes.flexWrap,
                    gap: attributes.gap,
                }
            });

            // useInnerBlocksProps is used for the container that will hold the child blocks
            var innerBlocksProps = useInnerBlocksProps(blockProps, {
                // A default layout for when the block is first inserted
                template: [
                    ['core/paragraph', { placeholder: 'Add content to the first item...' }],
                    ['core/paragraph', { placeholder: 'Add content to the second item...' }]
                ],
            });

            return el(
                element.Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Flexbox Layout', initialOpen: true },
                        el(SelectControl, {
                            label: 'Direction',
                            value: attributes.flexDirection,
                            options: [
                                { label: 'Row (horizontal)', value: 'row' },
                                { label: 'Column (vertical)', value: 'column' }
                            ],
                            onChange: function(val) { setAttributes({ flexDirection: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Justify Content (main axis)',
                            value: attributes.justifyContent,
                            options: [
                                { label: 'Default (Start)', value: undefined },
                                { label: 'Start', value: 'flex-start' },
                                { label: 'Center', value: 'center' },
                                { label: 'End', value: 'flex-end' },
                                { label: 'Space Between', value: 'space-between' },
                            ],
                            onChange: function(val) { setAttributes({ justifyContent: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Align Items (cross axis)',
                            value: attributes.alignItems,
                            options: [
                                { label: 'Default (Stretch)', value: undefined },
                                { label: 'Start', value: 'flex-start' },
                                { label: 'Center', value: 'center' },
                                { label: 'End', value: 'flex-end' },
                                { label: 'Stretch', value: 'stretch' },
                            ],
                            onChange: function(val) { setAttributes({ alignItems: val }); }
                        }),
                        el(ToggleControl, {
                            label: 'Wrap items',
                            checked: attributes.flexWrap === 'wrap',
                            onChange: function(isChecked) { setAttributes({ flexWrap: isChecked ? 'wrap' : 'nowrap' }); }
                        }),
                        el(TextControl, {
                            label: 'Gap',
                            value: attributes.gap,
                            help: 'CSS value for gap, e.g., 1rem, 16px',
                            onChange: function(val) { setAttributes({ gap: val }); }
                        })
                    )
                ),
                el('div', innerBlocksProps)
            );
        },

        save: function(props) {
            var attributes = props.attributes;
            var blockProps = blockEditor.useBlockProps.save({ style: { display: 'flex', flexDirection: attributes.flexDirection, justifyContent: attributes.justifyContent, alignItems: attributes.alignItems, flexWrap: attributes.flexWrap, gap: attributes.gap } });
            var innerBlocksProps = blockEditor.useInnerBlocksProps.save(blockProps);
            return el('div', innerBlocksProps);
        }
    });

    // 4. GRID CONTAINER BLOCK
    registerBlockType('gizmodotech/grid-container', {
        title: 'Gizmo Grid Container',
        icon: 'grid-view',
        category: 'gizmodotech',
        description: 'A container for creating column-based grid layouts.',
        
        supports: {
            color: {
                background: true,
                text: true,
            },
            spacing: {
                padding: true,
            }
        },

        attributes: {
            columns: { type: 'string', default: '2' },
            gap: { type: 'string', default: '1rem' },
            alignItems: { type: 'string' },
        },

        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            // Components
            var InspectorControls = blockEditor.InspectorControls;
            var useBlockProps = blockEditor.useBlockProps;
            var useInnerBlocksProps = blockEditor.useInnerBlocksProps;
            var PanelBody = components.PanelBody;
            var SelectControl = components.SelectControl;
            var TextControl = components.TextControl;

            var blockProps = useBlockProps({
                style: {
                    display: 'grid',
                    gridTemplateColumns: `repeat(${attributes.columns}, 1fr)`,
                    gap: attributes.gap,
                    alignItems: attributes.alignItems,
                }
            });

            var innerBlocksProps = useInnerBlocksProps(blockProps, {
                template: [
                    ['core/paragraph', { placeholder: 'Grid Item 1...' }],
                    ['core/paragraph', { placeholder: 'Grid Item 2...' }]
                ],
            });

            return el(
                element.Fragment,
                {},
                el(
                    InspectorControls,
                    {},
                    el(
                        PanelBody,
                        { title: 'Grid Layout', initialOpen: true },
                        el(TextControl, {
                            label: 'Columns',
                            value: attributes.columns,
                            type: 'number',
                            min: 1,
                            onChange: function(val) { setAttributes({ columns: val }); }
                        }),
                        el(TextControl, {
                            label: 'Gap',
                            value: attributes.gap,
                            help: 'CSS value for gap, e.g., 1rem, 16px',
                            onChange: function(val) { setAttributes({ gap: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Align Items (vertical)',
                            value: attributes.alignItems,
                            options: [
                                { label: 'Default (Stretch)', value: undefined },
                                { label: 'Start', value: 'start' },
                                { label: 'Center', value: 'center' },
                                { label: 'End', value: 'end' },
                                { label: 'Stretch', value: 'stretch' },
                            ],
                            onChange: function(val) { setAttributes({ alignItems: val }); }
                        })
                    )
                ),
                el('div', innerBlocksProps)
            );
        },

        save: function(props) {
            var attributes = props.attributes;
            var blockProps = blockEditor.useBlockProps.save({ style: { display: 'grid', gridTemplateColumns: `repeat(${attributes.columns}, 1fr)`, gap: attributes.gap, alignItems: attributes.alignItems } });
            var innerBlocksProps = blockEditor.useInnerBlocksProps.save(blockProps);
            return el('div', innerBlocksProps);
        }
    });

})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender);
