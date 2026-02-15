<?php
/**
 * The template for displaying the footer
 *
 * @package Gizmodotech
 * @since 1.0.0
 */
?>

    <footer id="colophon" class="site-footer">
        
        <!-- Newsletter Section -->
        <div class="newsletter-section">
            <div class="container">
                <div class="newsletter-content">
                    <h3><?php esc_html_e('Stay Updated with Tech News', 'gizmodotech'); ?></h3>
                    <p><?php esc_html_e('Get the latest gadget reviews, tech news, and exclusive insights delivered to your inbox.', 'gizmodotech'); ?></p>
                    
                    <form class="newsletter-form" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" method="post">
                        <input type="hidden" name="action" value="newsletter_subscribe">
                        <?php wp_nonce_field('newsletter_subscribe_nonce', 'newsletter_nonce'); ?>
                        <input type="email" name="email" placeholder="<?php esc_attr_e('Enter your email', 'gizmodotech'); ?>" required>
                        <button type="submit" class="btn btn-primary">
                            <?php esc_html_e('Subscribe', 'gizmodotech'); ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Footer Widgets -->
        <?php if (is_active_sidebar('footer-1') || is_active_sidebar('footer-2') || is_active_sidebar('footer-3') || is_active_sidebar('footer-4')) : ?>
        <div class="footer-widgets">
            <div class="container">
                <div class="footer-widgets-grid">
                    <?php for ($i = 1; $i <= 4; $i++) : ?>
                        <?php if (is_active_sidebar('footer-' . $i)) : ?>
                        <div class="footer-widget-column">
                            <?php dynamic_sidebar('footer-' . $i); ?>
                        </div>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Footer Bottom -->
        <div class="footer-bottom">
            <div class="container">
                <div class="footer-bottom-content">
                    
                    <!-- Copyright -->
                    <div class="copyright">
                        <p>
                            <?php
                            printf(
                                esc_html__('© %1$s %2$s. All rights reserved.', 'gizmodotech'),
                                date('Y'),
                                get_bloginfo('name')
                            );
                            ?>
                        </p>
                    </div>

                    <!-- Footer Menu -->
                    <?php if (has_nav_menu('footer')) : ?>
                    <nav class="footer-navigation">
                        <?php
                        wp_nav_menu(array(
                            'theme_location' => 'footer',
                            'menu_class'     => 'footer-menu',
                            'container'      => false,
                            'depth'          => 1,
                        ));
                        ?>
                    </nav>
                    <?php endif; ?>

                    <!-- Social Links -->
                    <?php if (gizmodotech_has_social_links()) : ?>
                    <div class="footer-social">
                        <?php gizmodotech_social_links(); ?>
                    </div>
                    <?php endif; ?>

                </div>
            </div>
        </div>

        <!-- Scroll to Top -->
        <button id="scroll-top" class="scroll-top-btn hidden" aria-label="<?php esc_attr_e('Scroll to top', 'gizmodotech'); ?>">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M18 15l-6-6-6 6"/>
            </svg>
        </button>

    </footer><!-- #colophon -->

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>
