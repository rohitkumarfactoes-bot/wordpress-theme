# Gizmodotech WordPress Theme

A modern, SEO-optimized WordPress theme designed specifically for tech news, gadget reviews, and technology blogs. Features a clean Beebom-inspired design, dark mode support, and lightning-fast performance.

## 🎯 Theme Features

### ✨ Core Features
- ✅ **Modern Design**: Clean, professional UI inspired by leading tech sites
- ✅ **Dark Mode**: Built-in dark mode toggle with persistent user preference
- ✅ **Fully Responsive**: Mobile-first design that works on all devices
- ✅ **SEO Optimized**: Clean code, semantic HTML5, and fast loading
- ✅ **Performance Focused**: Optimized CSS/JS, lazy loading images
- ✅ **Customizable**: WordPress Customizer integration with live preview
- ✅ **Widget Ready**: Multiple widget areas (sidebar + 4 footer widgets)
- ✅ **Menu Support**: Primary and footer navigation menus
- ✅ **Social Media Integration**: Display social media links
- ✅ **Newsletter Ready**: Built-in newsletter subscription form

### 🎨 Design Elements
- Trending tags bar (sticky)
- Featured post section
- Article cards with hover effects
- Category badges
- Reading time display
- Post views counter
- Smooth animations
- Custom search overlay
- Mobile-friendly navigation

### 📱 Technical Features
- WordPress 6.0+ compatible
- PHP 7.4+ required
- Block Editor (Gutenberg) ready
- Translation ready (i18n)
- Jetpack compatible
- WooCommerce ready (optional)
- Custom logo support
- Multiple image sizes
- Threaded comments

## 📦 Installation

### Method 1: Upload via WordPress Admin (Recommended)

1. Download the theme files as a ZIP file
2. Go to **WordPress Admin → Appearance → Themes**
3. Click **Add New** → **Upload Theme**
4. Choose the ZIP file and click **Install Now**
5. After installation, click **Activate**

### Method 2: FTP Upload

1. Extract the ZIP file
2. Upload the `gizmodotech-wp-theme` folder to `/wp-content/themes/`
3. Go to **WordPress Admin → Appearance → Themes**
4. Find "Gizmodotech" and click **Activate**

### Method 3: Local Development

```bash
# Navigate to your themes directory
cd /path/to/wordpress/wp-content/themes/

# Copy or clone the theme
cp -r /path/to/gizmodotech-wp-theme ./gizmodotech

# Set correct permissions
chmod -R 755 gizmodotech
```

## ⚙️ Initial Setup

### 1. Configure Theme Settings

Go to **Appearance → Customize** to access the following options:

#### Site Identity
- Upload your logo
- Set site title and tagline
- Upload site icon (favicon)
- Enable/disable dark mode toggle

#### Menus
- Create a Primary Menu
- Create a Footer Menu
- Assign menus to their locations

#### Widgets
- Configure Sidebar widgets
- Add content to Footer widget areas (1-4)

#### Social Media Links
- Add your social media URLs:
  - Facebook
  - Twitter
  - Instagram
  - YouTube
  - LinkedIn

### 2. Recommended Plugins

**Essential:**
- Contact Form 7 (for contact forms)
- Yoast SEO or Rank Math (for advanced SEO)
- WP Super Cache or W3 Total Cache (for caching)

**Optional:**
- Jetpack (for extra features)
- Akismet (spam protection)
- UpdraftPlus (backups)

### 3. Set Up Your Homepage

**Option A: Latest Posts (Default)**
- Go to **Settings → Reading**
- Select "Your latest posts"

**Option B: Static Front Page**
- Create a page for your homepage
- Create a page for your blog
- Go to **Settings → Reading**
- Select "A static page"
- Choose your pages

### 4. Create Your First Posts

1. Go to **Posts → Add New**
2. Add featured image (recommended: 1200×630px)
3. Select a category
4. Add tags
5. Write your content
6. Publish!

## 🎨 Customization Guide

### Changing Colors

The theme uses CSS variables for easy color customization. Edit `style.css`:

```css
:root {
  --color-primary: #0ea5e9;     /* Your brand color */
  --color-accent: #ef4444;       /* Accent color */
  /* Edit other colors as needed */
}
```

### Adding Custom CSS

Go to **Appearance → Customize → Additional CSS** and add your custom styles.

### Creating Child Theme

For extensive customizations, create a child theme:

1. Create a new folder: `/wp-content/themes/gizmodotech-child/`
2. Create `style.css`:

```css
/*
Theme Name: Gizmodotech Child
Template: gizmodotech-wp-theme
Version: 1.0.0
*/

/* Your custom styles here */
```

3. Create `functions.php`:

```php
<?php
function gizmodotech_child_enqueue_styles() {
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}
add_action('wp_enqueue_scripts', 'gizmodotech_child_enqueue_styles');
```

### Modifying Templates

1. Copy the template file from parent theme to child theme
2. Edit the copied file
3. WordPress will use the child theme version

## 📝 Theme Customization Options

### WordPress Customizer Options

Access via **Appearance → Customize**:

- **Site Identity**: Logo, title, tagline, favicon
- **Dark Mode**: Enable/disable dark mode toggle
- **Colors**: (Coming in future update)
- **Menus**: Assign menus to locations
- **Widgets**: Configure widget areas
- **Homepage Settings**: Static page or latest posts
- **Additional CSS**: Add custom styles
- **Social Media**: Add social network URLs

### Widget Areas

The theme includes 5 widget areas:

1. **Sidebar** - Main sidebar (right side on desktop)
2. **Footer 1** - First footer column
3. **Footer 2** - Second footer column
4. **Footer 3** - Third footer column  
5. **Footer 4** - Fourth footer column

### Menu Locations

2 menu locations available:

1. **Primary Menu** - Main navigation in header
2. **Footer Menu** - Links in footer bottom

## 🚀 Performance Optimization

### Image Optimization

The theme automatically creates these image sizes:
- **Featured**: 1200×675px (hero images)
- **Large**: 800×450px (large cards)
- **Medium**: 600×400px (article cards)
- **Small**: 400×300px (thumbnails)

Recommended plugins:
- Smush or ShortPixel (image compression)
- Lazy Load by WP Rocket (lazy loading)

### Caching

Install a caching plugin:
- WP Super Cache (easy)
- W3 Total Cache (advanced)
- WP Rocket (premium, best)

### CDN Setup

For faster global loading:
1. Sign up for Cloudflare (free)
2. Add your site
3. Update nameservers
4. Enable caching

## 🔍 SEO Setup

### Basic SEO Settings

1. Install Yoast SEO or Rank Math
2. Go through the configuration wizard
3. Set up:
   - Site name and tagline
   - Social profiles
   - Knowledge graph
   - Webmaster tools verification

### Permalink Structure

Go to **Settings → Permalinks** and choose:
- Post name: `/%postname%/` (Recommended)

### XML Sitemap

Most SEO plugins auto-generate sitemaps. Submit to:
- Google Search Console
- Bing Webmaster Tools

## 🐛 Troubleshooting

### Theme not displaying correctly

1. Clear browser cache (Ctrl+Shift+R)
2. Clear WordPress cache (if using caching plugin)
3. Deactivate all plugins, reactivate one by one
4. Check browser console for JavaScript errors

### Featured images not showing

1. Regenerate thumbnails: Install "Regenerate Thumbnails" plugin
2. Run the plugin to regenerate all image sizes

### Dark mode not working

1. Clear browser cookies and cache
2. Check if JavaScript is enabled
3. Check browser console for errors

### Mobile menu not opening

1. Check if jQuery is loading properly
2. Ensure no JavaScript conflicts with plugins
3. Deactivate plugins one by one to find conflict

## 📞 Support & Documentation

- **Email**: rohit@gizmodotech.com
- **Website**: https://gizmodotech.com
- **Documentation**: Check this README file

## 📋 Changelog

### Version 1.0.0 (February 2026)
- Initial release
- Modern responsive design
- Dark mode support
- SEO optimized
- Performance focused
- Widget ready
- Translation ready

## 📄 License

This theme is licensed under the GNU General Public License v2 or later.
See LICENSE file for details.

## 🙏 Credits

**Design Inspiration:**
- Beebom - Modern UI patterns
- GSMArena - Tech content layout
- Gadgets360 - Category organization

**Technologies:**
- WordPress
- HTML5, CSS3
- jQuery
- Google Fonts (Inter, DM Sans)

---

**Built with ❤️ for Gizmodotech**  
**Version 1.0.0 | February 2026**

For questions or support, please contact: rohit@gizmodotech.com
