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
    var RichText = blockEditor.RichText;
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

    // 5. PRODUCT REVIEW BLOCK (Template)
    registerBlockType('gizmodotech/product-review', {
        title: 'Gizmo Product Review',
        icon: 'cart',
        category: 'gizmodotech',
        description: 'Full product review layout with Pros/Cons.',
        edit: function(props) {
            var blockProps = blockEditor.useBlockProps();
            var innerBlocksProps = blockEditor.useInnerBlocksProps(blockProps, {
                template: [
                    ['core/group', { className: 'pros-cons-block', layout: { type: 'flex', flexWrap: 'nowrap' } }, [
                        ['core/group', { className: 'pcb-left', layout: { type: 'flex', orientation: 'vertical' } }, [
                            ['core/group', { className: 'pcb-image-wrap' }, [
                                ['core/image', { sizeSlug: 'full', url: 'https://placehold.co/600x800', alt: 'Product' }]
                            ]],
                            ['core/paragraph', { className: 'pcb-price', placeholder: '₹99,999', content: '₹99,999' }],
                            ['core/paragraph', { className: 'pcb-rating', placeholder: 'Rating: 9/10', content: 'Rating: 9/10' }]
                        ]],
                        ['core/group', { className: 'pcb-right' }, [
                            ['core/group', { className: 'pcb-pros-cons-row', layout: { type: 'flex', flexWrap: 'nowrap' } }, [
                                ['core/group', { className: 'pcb-pros' }, [
                                    ['core/paragraph', { className: 'pcb-pros-title', content: 'Pros' }],
                                    ['core/list', { className: 'pcb-list pros-list' }, [
                                        ['core/list-item', { content: 'Excellent camera' }],
                                        ['core/list-item', { content: 'Great battery' }]
                                    ]]
                                ]],
                                ['core/group', { className: 'pcb-cons' }, [
                                    ['core/paragraph', { className: 'pcb-cons-title', content: 'Cons' }],
                                    ['core/list', { className: 'pcb-list cons-list' }, [
                                        ['core/list-item', { content: 'Expensive' }]
                                    ]]
                                ]]
                            ]],
                            ['core/group', { className: 'pcb-buy-row', layout: { type: 'flex', justifyContent: 'space-between' } }, [
                                ['core/paragraph', { className: 'pcb-buy-label', content: 'Buy Now' }],
                                ['core/group', { className: 'pcb-buy-buttons', layout: { type: 'flex' } }, [
                                    ['core/image', { width: 120, url: 'https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png' }],
                                    ['core/image', { width: 120, url: 'https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png' }]
                                ]]
                            ]]
                        ]]
                    ]]
                ]
            });
            return el('div', innerBlocksProps);
        },
        save: function(props) {
            return el(blockEditor.InnerBlocks.Content);
        }
    });

    // 6. SPECS CARD BLOCK
    registerBlockType('gizmodotech/specs-card-block', {
        title: 'Gizmo Specs Card',
        icon: 'smartphone',
        category: 'gizmodotech',
        description: 'Specs summary card.',
        edit: function(props) {
            var blockProps = blockEditor.useBlockProps();
            var innerBlocksProps = blockEditor.useInnerBlocksProps(blockProps, {
                template: [
                    ['core/group', { className: 'main-cont-specs', layout: { type: 'flex', flexWrap: 'nowrap' } }, [
                        // Image Col
                        ['core/group', { className: 'specs-img-wrap', layout: { type: 'flex', orientation: 'vertical' } }, [
                            ['core/image', { sizeSlug: 'large', url: 'https://placehold.co/600x800' }]
                        ]],
                        // Text Col
                        ['core/group', { className: 'specs-text-wrap', layout: { type: 'flex', orientation: 'vertical' } }, [
                            // Price Row
                            ['core/group', { layout: { type: 'flex', justifyContent: 'space-between' } }, [
                                ['core/paragraph', { content: '₹14,999' }],
                                ['core/paragraph', { className: 'text-sm', content: '4 + 128GB' }]
                            ]],
                            // Specs Row
                            ['core/group', { layout: { type: 'flex' } }, [
                                ['core/group', { layout: { type: 'flex', flexWrap: 'nowrap' } }, [
                                    ['core/image', { className: 'min-img', sizeSlug: 'full', url: 'https://gizmodotech.com/wp-content/uploads/2024/12/processor-gradient-icon-1.png' }],
                                    ['core/group', { layout: { type: 'flex', orientation: 'vertical' } }, [
                                        ['core/paragraph', { className: 'text-sm', content: 'Display' }],
                                        ['core/paragraph', { className: 'text-sm', content: '6.7" FHD+' }]
                                    ]]
                                ]]
                            ]],
                            // Buy Row
                            ['core/group', { layout: { type: 'flex', justifyContent: 'space-between' } }, [
                                ['core/paragraph', { content: 'Buy Now' }],
                                ['core/group', { layout: { type: 'flex' } }, [
                                    ['core/image', { width: 100, url: 'https://gizmodotech.com/wp-content/uploads/2024/12/buy-amazon.png' }],
                                    ['core/image', { width: 100, url: 'https://gizmodotech.com/wp-content/uploads/2024/12/buy-flipkart.png' }]
                                ]]
                            ]],
                            // Link
                            ['core/paragraph', { className: 'text-cm', fontSize: 'xs', content: '<a href="#">See full specifications</a>' }]
                        ]]
                    ]]
                ]
            });
            return el('div', innerBlocksProps);
        },
        save: function() { return el(blockEditor.InnerBlocks.Content); }
    });

    // 7. PROS & CONS LIST BLOCK
    registerBlockType('gizmodotech/pros-cons-list', {
        title: 'Gizmo Pros & Cons List',
        icon: 'list-view',
        category: 'gizmodotech',
        description: 'A static Pros and Cons list block with bullet points.',
        attributes: {
            prosTitle: { type: 'string', default: 'Pros:' },
            consTitle: { type: 'string', default: 'Cons:' },
            prosList: { type: 'string', source: 'html', selector: '.pros ul', default: '<li>Item 1</li><li>Item 2</li>' },
            consList: { type: 'string', source: 'html', selector: '.cons ul', default: '<li>Item 1</li><li>Item 2</li>' }
        },
        edit: function(props) {
            var attributes = props.attributes;
            var setAttributes = props.setAttributes;

            return el('div', { className: 'pros-cons-main' },
                el('div', { className: 'pros' },
                    el('p', {}, 
                        el(RichText, {
                            tagName: 'strong',
                            value: attributes.prosTitle,
                            onChange: function(val) { setAttributes({ prosTitle: val }); },
                            placeholder: 'Pros Label'
                        })
                    ),
                    el(RichText, {
                        tagName: 'ul',
                        multiline: 'li',
                        value: attributes.prosList,
                        onChange: function(val) { setAttributes({ prosList: val }); },
                        placeholder: 'Add pros list...'
                    })
                ),
                el('div', { className: 'cons' },
                    el('p', {}, 
                        el(RichText, {
                            tagName: 'strong',
                            value: attributes.consTitle,
                            onChange: function(val) { setAttributes({ consTitle: val }); },
                            placeholder: 'Cons Label'
                        })
                    ),
                    el(RichText, {
                        tagName: 'ul',
                        multiline: 'li',
                        value: attributes.consList,
                        onChange: function(val) { setAttributes({ consList: val }); },
                        placeholder: 'Add cons list...'
                    })
                )
            );
        },
        save: function(props) {
            var attributes = props.attributes;

            return el('div', { className: 'pros-cons-main' },
                el('div', { className: 'pros' },
                    el('p', {}, el(RichText.Content, { tagName: 'strong', value: attributes.prosTitle })),
                    el(RichText.Content, { tagName: 'ul', value: attributes.prosList })
                ),
                el('div', { className: 'cons' },
                    el('p', {}, el(RichText.Content, { tagName: 'strong', value: attributes.consTitle })),
                    el(RichText.Content, { tagName: 'ul', value: attributes.consList })
                )
            );
        }
    });

    // 8. CAROUSEL SLIDER BLOCK
    registerBlockType('gizmodotech/carousel-slider', {
        title: 'Gizmo Carousel Slider',
        icon: 'slides',
        category: 'gizmodotech',
        description: 'A post carousel slider.',
        attributes: {
            title: { type: 'string', default: 'Trending Now' },
            postType: { type: 'string', default: 'post' },
            count: { type: 'string', default: '8' },
            categories: { type: 'string', default: '' }
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
                        { title: 'Carousel Settings', initialOpen: true },
                        el(TextControl, {
                            label: 'Section Title',
                            value: attributes.title,
                            onChange: function(val) { setAttributes({ title: val }); }
                        }),
                        el(SelectControl, {
                            label: 'Post Type',
                            value: attributes.postType,
                            options: [
                                { label: 'Standard Posts', value: 'post' },
                                { label: 'Tech News', value: 'technews' },
                                { label: 'Reviews', value: 'reviews' }
                            ],
                            onChange: function(val) { setAttributes({ postType: val }); }
                        }),
                        el(TextControl, {
                            label: 'Number of Posts',
                            value: attributes.count,
                            type: 'number',
                            onChange: function(val) { setAttributes({ count: val }); }
                        }),
                        el(TextControl, {
                            label: 'Category IDs (comma separated)',
                            value: attributes.categories,
                            onChange: function(val) { setAttributes({ categories: val }); }
                        })
                    )
                ),
                el(ServerSideRender, {
                    block: 'gizmodotech/carousel-slider',
                    attributes: attributes
                })
            );
        },
        save: function() { return null; }
    });

})(window.wp.blocks, window.wp.element, window.wp.components, window.wp.blockEditor, window.wp.serverSideRender);
