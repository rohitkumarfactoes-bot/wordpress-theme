<?php
/**
 * Gizmodotech Theme Customizer
 *
 * @package Gizmodotech
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * Customizer additions.
 *
 * @param WP_Customize_Manager $wp_customize Theme Customizer object.
 */
function gizmodotech_customize_register($wp_customize) {
    // Dark Mode Toggle
    $wp_customize->add_section('gizmodotech_dark_mode', array(
        'title'    => esc_html__('Dark Mode', 'gizmodotech'),
        'priority' => 30,
    ));

    $wp_customize->add_setting('gizmodotech_enable_dark_mode', array(
        'default'           => true,
        'sanitize_callback' => 'wp_validate_boolean',
    ));

    $wp_customize->add_control('gizmodotech_enable_dark_mode', array(
        'label'   => esc_html__('Enable Dark Mode Toggle', 'gizmodotech'),
        'section' => 'gizmodotech_dark_mode',
        'type'    => 'checkbox',
    ));

    // Social Media Links
    $wp_customize->add_section('gizmodotech_social_links', array(
        'title'    => esc_html__('Social Media Links', 'gizmodotech'),
        'priority' => 40,
    ));

    $social_networks = array(
        'facebook'  => 'Facebook',
        'twitter'   => 'Twitter',
        'instagram' => 'Instagram',
        'youtube'   => 'YouTube',
        'linkedin'  => 'LinkedIn',
    );

    foreach ($social_networks as $network => $label) {
        $wp_customize->add_setting('gizmodotech_' . $network . '_url', array(
            'default'           => '',
            'sanitize_callback' => 'esc_url_raw',
        ));

        $wp_customize->add_control('gizmodotech_' . $network . '_url', array(
            'label'   => $label . ' ' . esc_html__('URL', 'gizmodotech'),
            'section' => 'gizmodotech_social_links',
            'type'    => 'url',
        ));
    }

    // Global Colors
    $wp_customize->add_section('gizmodotech_colors', array(
        'title'    => esc_html__('Global Colors', 'gizmodotech'),
        'priority' => 20,
    ));

    $colors = array(
        'primary_color' => array('label' => 'Primary Color', 'default' => '#0ea5e9', 'var' => '--color-primary'),
        'bg_color'      => array('label' => 'Background Color', 'default' => '#ffffff', 'var' => '--color-bg'),
        'text_color'    => array('label' => 'Text Color', 'default' => '#1f2937', 'var' => '--color-text'),
    );

    foreach ($colors as $id => $args) {
        $wp_customize->add_setting('gizmodotech_' . $id, array(
            'default'           => $args['default'],
            'sanitize_callback' => 'sanitize_hex_color',
        ));
        $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'gizmodotech_' . $id, array(
            'label'   => esc_html__($args['label'], 'gizmodotech'),
            'section' => 'gizmodotech_colors',
        )));
    }

    // --- Enhanced Typography Section ---
    $wp_customize->add_panel('gizmodotech_typography_panel', array(
        'title'    => esc_html__('Typography', 'gizmodotech'),
        'priority' => 25,
    ));

    $google_fonts = gizmodotech_get_google_fonts();
    $font_weights = array(
        '300' => 'Light (300)',
        '400' => 'Normal (400)',
        '500' => 'Medium (500)',
        '600' => 'Semi-Bold (600)',
        '700' => 'Bold (700)',
        '800' => 'Extra-Bold (800)',
    );
    $text_transforms = array(
        'none'       => 'None',
        'uppercase'  => 'Uppercase',
        'lowercase'  => 'Lowercase',
        'capitalize' => 'Capitalize',
    );

    $elements = array(
        'body' => array('label' => 'Body', 'defaults' => ['family' => 'Inter', 'size' => 16, 'weight' => '400', 'transform' => 'none']),
        'h1'   => array('label' => 'Heading 1', 'defaults' => ['family' => 'DM Sans', 'size' => 40, 'weight' => '700', 'transform' => 'none']),
        'h2'   => array('label' => 'Heading 2', 'defaults' => ['family' => 'DM Sans', 'size' => 32, 'weight' => '700', 'transform' => 'none']),
        'h3'   => array('label' => 'Heading 3', 'defaults' => ['family' => 'DM Sans', 'size' => 28, 'weight' => '700', 'transform' => 'none']),
        'h4'   => array('label' => 'Heading 4', 'defaults' => ['family' => 'DM Sans', 'size' => 24, 'weight' => '700', 'transform' => 'none']),
        'h5'   => array('label' => 'Heading 5', 'defaults' => ['family' => 'DM Sans', 'size' => 20, 'weight' => '700', 'transform' => 'none']),
        'h6'   => array('label' => 'Heading 6', 'defaults' => ['family' => 'DM Sans', 'size' => 18, 'weight' => '700', 'transform' => 'none']),
    );

    foreach ($elements as $id => $props) {
        $section_id = "gizmodotech_{$id}_typography";
        $wp_customize->add_section($section_id, array(
            'title' => $props['label'],
            'panel' => 'gizmodotech_typography_panel',
        ));

        // Font Family
        $wp_customize->add_setting("gizmodotech_{$id}_font_family", ['default' => $props['defaults']['family'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_font_family", [
            'label'   => __('Font Family', 'gizmodotech'),
            'section' => $section_id,
            'type'    => 'select',
            'choices' => $google_fonts,
        ]);

        // Font Size
        $wp_customize->add_setting("gizmodotech_{$id}_font_size", ['default' => $props['defaults']['size'], 'sanitize_callback' => 'absint']);
        $wp_customize->add_control("gizmodotech_{$id}_font_size", [
            'label'       => __('Font Size (px)', 'gizmodotech'),
            'section'     => $section_id,
            'type'        => 'number',
            'input_attrs' => ['min' => 8, 'max' => 100, 'step' => 1],
        ]);

        // Font Weight
        $wp_customize->add_setting("gizmodotech_{$id}_font_weight", ['default' => $props['defaults']['weight'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_font_weight", [
            'label'   => __('Font Weight', 'gizmodotech'),
            'section' => $section_id,
            'type'    => 'select',
            'choices' => $font_weights,
        ]);

        // Text Transform
        $wp_customize->add_setting("gizmodotech_{$id}_text_transform", ['default' => $props['defaults']['transform'], 'sanitize_callback' => 'sanitize_text_field']);
        $wp_customize->add_control("gizmodotech_{$id}_text_transform", [
            'label'   => __('Text Transform', 'gizmodotech'),
            'section' => $section_id,
            'type'    => 'select',
            'choices' => $text_transforms,
        ]);
    }
}
add_action('customize_register', 'gizmodotech_customize_register');

/**
 * Output Customizer CSS Variables
 */
function gizmodotech_customizer_css() {
    ?>
    <style type="text/css">
        :root {
            --color-primary: <?php echo esc_attr(get_theme_mod('gizmodotech_primary_color', '#0ea5e9')); ?>;
            --color-bg: <?php echo esc_attr(get_theme_mod('gizmodotech_bg_color', '#ffffff')); ?>;
            --color-text: <?php echo esc_attr(get_theme_mod('gizmodotech_text_color', '#1f2937')); ?>;
        }

        <?php
        $elements = array(
            'body' => array('selector' => 'body', 'defaults' => ['family' => 'Inter', 'size' => 16, 'weight' => '400', 'transform' => 'none']),
            'h1'   => array('selector' => 'h1, .h1, .single-post-title, .page-title', 'defaults' => ['family' => 'DM Sans', 'size' => 40, 'weight' => '700', 'transform' => 'none']),
            'h2'   => array('selector' => 'h2, .h2, .widget-title', 'defaults' => ['family' => 'DM Sans', 'size' => 32, 'weight' => '700', 'transform' => 'none']),
            'h3'   => array('selector' => 'h3, .h3, .article-title', 'defaults' => ['family' => 'DM Sans', 'size' => 28, 'weight' => '700', 'transform' => 'none']),
            'h4'   => array('selector' => 'h4, .h4', 'defaults' => ['family' => 'DM Sans', 'size' => 24, 'weight' => '700', 'transform' => 'none']),
            'h5'   => array('selector' => 'h5, .h5', 'defaults' => ['family' => 'DM Sans', 'size' => 20, 'weight' => '700', 'transform' => 'none']),
            'h6'   => array('selector' => 'h6, .h6', 'defaults' => ['family' => 'DM Sans', 'size' => 18, 'weight' => '700', 'transform' => 'none']),
        );

        foreach ($elements as $id => $props) {
            $family = get_theme_mod("gizmodotech_{$id}_font_family", $props['defaults']['family']);
            $size = get_theme_mod("gizmodotech_{$id}_font_size", $props['defaults']['size']);
            $weight = get_theme_mod("gizmodotech_{$id}_font_weight", $props['defaults']['weight']);
            $transform = get_theme_mod("gizmodotech_{$id}_text_transform", $props['defaults']['transform']);

            echo "{$props['selector']} {\n";
            if ($family) echo "    font-family: '{$family}', sans-serif;\n";
            if ($size) echo "    font-size: {$size}px;\n";
            if ($weight) echo "    font-weight: {$weight};\n";
            if ($transform) echo "    text-transform: {$transform};\n";
            echo "}\n\n";
        }

        // Base font size for rem units
        $base_size = get_theme_mod("gizmodotech_body_font_size", 16);
        echo "html { font-size: {$base_size}px; }\n";
        ?>
    </style>
    <?php
}
add_action('wp_head', 'gizmodotech_customizer_css');

/**
 * Helper function to get a list of Google Fonts.
 */
function gizmodotech_get_google_fonts() {
    return array(
        'DM Sans'          => 'DM Sans',
        'Inter'            => 'Inter',
        'Roboto'           => 'Roboto',
        'Open Sans'        => 'Open Sans',
        'Lato'             => 'Lato',
        'Montserrat'       => 'Montserrat',
        'Poppins'          => 'Poppins',
        'Source Sans Pro'  => 'Source Sans Pro',
        'Oswald'           => 'Oswald',
        'Raleway'          => 'Raleway',
        'Nunito'           => 'Nunito',
        'Merriweather'     => 'Merriweather',
        'Playfair Display' => 'Playfair Display',
        'PT Serif'         => 'PT Serif',
    );
}

