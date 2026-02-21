# Gizmodotech WordPress Theme

A modern, SEO-optimized WordPress theme designed specifically for tech news, gadget reviews, and technology blogs.

## Features

- ✅ Modern, clean design inspired by leading tech sites
- ✅ Built-in dark mode with user preference persistence
- ✅ Fully responsive and mobile-optimized
- ✅ SEO-optimized with semantic HTML5
- ✅ Performance-focused
- ✅ Customizable via WordPress Customizer
- ✅ Widget-ready (Sidebar + 4 Footer widgets)
- ✅ Menu support (Primary + Footer)
- ✅ Social media integration
- ✅ Translation ready

## Requirements

- WordPress 6.0+
- PHP 7.4+
- MySQL 5.7+ or MariaDB 10.2+

## Installation

### Method 1: WordPress Admin (Recommended)

1. Download the theme ZIP file
2. Go to **WordPress Admin → Appearance → Themes**
3. Click **Add New** → **Upload Theme**
4. Choose the ZIP file and click **Install Now**
5. Click **Activate**

### Method 2: FTP Upload

1. Extract the ZIP file
2. Upload the `gizmodotech-wp-theme` folder to `/wp-content/themes/`
3. Go to **WordPress Admin → Appearance → Themes**
4. Find "Gizmodotech" and click **Activate**

## Initial Setup
Follow the standard WordPress theme setup for menus, widgets, and the Customizer.

## Development & Customization

This theme uses Tailwind CSS for styling. To make changes to the CSS, you need to set up a build process **on your local computer**.

### Requirements
- [Node.js](https://nodejs.org/) (v16 or higher) installed on your **local computer**.
- npm (comes with Node.js).

### Build Setup (Local Computer)

1.  **Install Dependencies**: Open a terminal in the theme's root directory **on your computer** and run:
    ```bash
    npm install
    ```

2.  **Start Development**: To watch for changes and automatically recompile the CSS while you work locally:
    ```bash
    npm run watch
    ```

3.  **Build for Production**: Before uploading to your server, generate the final CSS file:
    ```bash
    npm run build
    ```

### Deployment

**CRITICAL:** When uploading the theme to your WordPress hosting, you must **EXCLUDE** the development files.

**✅ DO UPLOAD:**
- `dist/main.css` (The generated style file)
- All PHP files (`*.php`)
- `style.css` (Theme information)
- `assets/` folder
- `inc/` folder

**❌ DO NOT UPLOAD:**
- `node_modules/` (This is huge and for local use only)
- `package.json`, `package-lock.json`
- `tailwind.config.js`, `postcss.config.js`

### Custom CSS

For small tweaks, you can add custom CSS via **Appearance → Customize → Additional CSS**.

### Child Theme

For extensive customizations, it is highly recommended to use a child theme. This ensures your changes are not lost when the parent theme is updated.

1. Create folder: `/wp-content/themes/gizmodotech-child/`
2. Create `style.css`:

```css
/*
 Theme Name: Gizmodotech Child
 Template: gizmodotech-wp-theme
 Version: 1.0.0
*/
```

3. Create `functions.php`:

```php
<?php
function gizmodotech_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
    // Enqueue the child theme's style.css, making sure it loads after the parent's Tailwind CSS
    wp_enqueue_style('child-style', get_stylesheet_uri(), array('gizmodotech-tailwind'), wp_get_theme()->get('Version'));
}
add_action('wp_enqueue_scripts', 'gizmodotech_child_enqueue_styles');
```

## File Structure

```
gizmodotech-wp-theme/
├── assets/
│   ├── css/
│   │   └── dark-mode.css
│   ├── js/
│   │   ├── navigation.js
│   │   └── dark-mode.js
│   └── img/
├── inc/
├── template-parts/
│   ├── content.php
│   ├── content-single.php
│   └── content-none.php
├── languages/
├── style.css
├── functions.php
├── header.php
├── footer.php
├── index.php
├── single.php
├── archive.php
├── searchform.php
├── comments.php
└── screenshot.png
```

## Recommended Plugins

- **Yoast SEO** or **Rank Math** - SEO optimization
- **Contact Form 7** - Contact forms
- **WP Super Cache** - Caching
- **Smush** - Image optimization
- **Akismet** - Spam protection

## Support

- **Email**: rohit@gizmodotech.com
- **Website**: https://gizmodotech.com

## Changelog

### Version 1.0.0 (February 2026)
- Initial release
- Modern responsive design
- Dark mode support
- SEO optimized
- Performance focused

## License

GPL v2 or later

## Credits

- Font: Inter & DM Sans (Google Fonts)
- Icons: Feather Icons (MIT License)

---

**Built with ❤️ for Gizmodotech**
