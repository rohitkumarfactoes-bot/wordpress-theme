/**
 * Gizmodotech Pro — app.js
 * Lightweight vanilla JS: Dark Mode, Reading Progress, Search Overlay,
 * Share Buttons, Comment Toggle, Scroll-to-Top, Mobile Nav, Copy-to-Clipboard
 *
 * NO dependencies. Pure Vanilla JS. Deferred load.
 */

( function () {
  'use strict';

  /* ── Utility: $ selector ── */
  const $  = ( sel, ctx = document ) => ctx.querySelector( sel );
  const $$ = ( sel, ctx = document ) => [...ctx.querySelectorAll( sel )];

  /* ============================================================
     1. DARK MODE
     Persists in localStorage AND sends cookie for PHP body_class
     ============================================================ */

  const THEME_KEY = 'gizmo_theme';
  const html      = document.documentElement;

  function setTheme( theme ) {
    html.setAttribute( 'data-theme', theme );
    document.body.classList.toggle( 'dark-mode', theme === 'dark' );
    localStorage.setItem( THEME_KEY, theme );
    document.cookie = `gizmo_theme=${theme};path=/;max-age=${60 * 60 * 24 * 365}`;

    $$( '.dark-mode-toggle' ).forEach( btn => {
      btn.setAttribute( 'aria-label', theme === 'dark' ? 'Switch to Light Mode' : 'Switch to Dark Mode' );
      btn.setAttribute( 'aria-pressed', String( theme === 'dark' ) );
    } );
  }

  function initTheme() {
    const stored  = localStorage.getItem( THEME_KEY );
    const prefers = window.matchMedia( '(prefers-color-scheme: dark)' ).matches ? 'dark' : 'light';
    setTheme( stored || prefers );
  }

  function toggleTheme() {
    const current = html.getAttribute( 'data-theme' ) || 'light';
    setTheme( current === 'dark' ? 'light' : 'dark' );
  }

  initTheme();

  // Watch system preference changes
  window.matchMedia( '(prefers-color-scheme: dark)' ).addEventListener( 'change', e => {
    if ( ! localStorage.getItem( THEME_KEY ) ) {
      setTheme( e.matches ? 'dark' : 'light' );
    }
  } );

  /* ============================================================
     2. READING PROGRESS BAR
     ============================================================ */

  function initProgressBar() {
    const bar  = $( '.progress-bar' );
    const body = $( '.single-body' ) || document.body; // Corrected selector
    if ( ! bar ) { return; }

    let ticking = false;

    function updateBar() {
      const rect   = body.getBoundingClientRect();
      const total  = rect.height - window.innerHeight;
      const scrolled = Math.max( 0, -rect.top );
      const pct    = total > 0 ? Math.min( 100, ( scrolled / total ) * 100 ) : 0;
      bar.style.width = pct + '%';
      ticking = false;
    }

    window.addEventListener( 'scroll', () => {
      if ( ! ticking ) {
        requestAnimationFrame( updateBar );
        ticking = true;
      }
    }, { passive: true } );
  }

  /* ============================================================
     3. SCROLL-TO-TOP BUTTON
     ============================================================ */

  function initScrollTop() {
    const btn = $( '.scroll-top' );
    if ( ! btn ) { return; }

    window.addEventListener( 'scroll', () => {
      btn.classList.toggle( 'is-visible', window.scrollY > 400 );
    }, { passive: true } );

    btn.addEventListener( 'click', () => {
      window.scrollTo( { top: 0, behavior: 'smooth' } );
    } );
  }

  /* ============================================================
     4. SEARCH OVERLAY
     ============================================================ */

  function initSearch() {
    const overlay = $( '.search-overlay' );
    if ( ! overlay ) { return; }

    const input   = $( '.search-overlay__input', overlay );
    const closers = [
      $( '.search-overlay__close', overlay ),
      $( '.search-toggle' ),
    ];

    function openSearch() {
      overlay.classList.add( 'is-open' );
      overlay.setAttribute( 'aria-hidden', 'false' );
      document.body.style.overflow = 'hidden';
      requestAnimationFrame( () => input && input.focus() );
    }

    function closeSearch() {
      overlay.classList.remove( 'is-open' );
      overlay.setAttribute( 'aria-hidden', 'true' );
      document.body.style.overflow = '';
    }

    closers.forEach( el => el && el.addEventListener( 'click', e => {
      overlay.classList.contains( 'is-open' ) ? closeSearch() : openSearch();
    } ) );

    overlay.addEventListener( 'click', e => {
      if ( e.target === overlay ) { closeSearch(); }
    } );

    document.addEventListener( 'keydown', e => {
      if ( e.key === 'Escape' ) { closeSearch(); }
      if ( ( e.ctrlKey || e.metaKey ) && e.key === 'k' ) {
        e.preventDefault();
        overlay.classList.contains( 'is-open' ) ? closeSearch() : openSearch();
      }
    } );
  }

  /* ============================================================
     5. MOBILE NAV
     ============================================================ */

  function initMobileNav() {
    const nav     = $( '.mobile-nav' );
    const trigger = $( '.hamburger' );
    if ( ! nav || ! trigger ) { return; }

    function toggleNav() {
      const isOpen = nav.classList.toggle( 'is-open' );
      trigger.setAttribute( 'aria-expanded', String( isOpen ) );
      document.body.style.overflow = isOpen ? 'hidden' : '';

      // Animate hamburger → X
      const spans = $$( 'span', trigger );
      if ( spans.length >= 3 ) {
        spans[0].style.transform = isOpen ? 'rotate(45deg) translate(5px, 5px)' : '';
        spans[1].style.opacity   = isOpen ? '0' : '';
        spans[2].style.transform = isOpen ? 'rotate(-45deg) translate(5px, -5px)' : '';
      }
    }

    trigger.addEventListener( 'click', toggleNav );

    document.addEventListener( 'keydown', e => {
      if ( e.key === 'Escape' && nav.classList.contains( 'is-open' ) ) { toggleNav(); }
    } );

    // Close on backdrop click
    document.addEventListener( 'click', e => {
      if ( nav.classList.contains( 'is-open' ) &&
           ! nav.contains( e.target ) &&
           ! trigger.contains( e.target ) ) {
        toggleNav();
      }
    } );
  }

  /* ============================================================
     6. SHARE BUTTONS
     ============================================================ */

  function initShareButtons() {
    const url   = encodeURIComponent( window.location.href );
    const title = encodeURIComponent( document.title );

    const shareMap = {
      'share-btn--twitter':   `https://twitter.com/intent/tweet?url=${url}&text=${title}`,
      'share-btn--facebook':  `https://www.facebook.com/sharer/sharer.php?u=${url}`,
      'share-btn--whatsapp':  `https://wa.me/?text=${title}%20${url}`,
      'share-btn--linkedin':  `https://www.linkedin.com/sharing/share-offsite/?url=${url}`,
    };

    Object.entries( shareMap ).forEach( ( [cls, shareUrl] ) => {
      $$( '.' + cls ).forEach( btn => {
        btn.addEventListener( 'click', e => {
          e.preventDefault();
          window.open( shareUrl, '_blank', 'width=600,height=400,noopener,noreferrer' );
        } );
      } );
    } );

    // Copy link
    $$( '.share-btn--copy' ).forEach( btn => {
      btn.addEventListener( 'click', () => {
        navigator.clipboard.writeText( window.location.href )
          .then( () => {
            const original = btn.innerHTML;
            btn.innerHTML  = '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg> <span>Copied!</span>';
            btn.classList.add( 'copied' );
            setTimeout( () => {
              btn.innerHTML = original;
              btn.classList.remove( 'copied' );
            }, 2000 );
          } )
          .catch( () => {
            // Fallback for older browsers
            const area  = document.createElement( 'textarea' );
            area.value  = window.location.href;
            area.style.cssText = 'position:fixed;opacity:0;';
            document.body.appendChild( area );
            area.select();
            document.execCommand( 'copy' );
            document.body.removeChild( area );
          } );
      } );
    } );
  }

  /* ============================================================
     7. COMMENTS TOGGLE
     ============================================================ */

  function initCommentToggle() {
    $$( '.comments-toggle' ).forEach( toggle => {
      const target = $( '#' + toggle.getAttribute( 'aria-controls' ) );
      if ( ! target ) { return; }

      toggle.addEventListener( 'click', () => {
        const expanded = toggle.getAttribute( 'aria-expanded' ) === 'true';
        toggle.setAttribute( 'aria-expanded', String( ! expanded ) );
        target.classList.toggle( 'is-open', ! expanded );

        // Smooth scroll to form when opening
        if ( ! expanded ) {
          setTimeout( () => {
            target.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
          }, 100 );
        }
      } );

      // Auto-open if URL has #comment or #respond
      if ( window.location.hash === '#comments' || window.location.hash === '#respond' ) {
        toggle.setAttribute( 'aria-expanded', 'true' );
        target.classList.add( 'is-open' );
      }
    } );
  }

  /* ============================================================
     8. STICKY NAV SHADOW ON SCROLL
     ============================================================ */

  function initStickyNav() {
    const header = $( '.site-header' );
    if ( ! header ) { return; }

    window.addEventListener( 'scroll', () => {
      header.classList.toggle( 'is-scrolled', window.scrollY > 10 );
    }, { passive: true } );
  }

  /* ============================================================
     9. LAZY IMAGE LOADING (intersection observer fallback)
     ============================================================ */

  function initLazyImages() {
    if ( 'loading' in HTMLImageElement.prototype ) { return; } // native support

    const observer = new IntersectionObserver( ( entries, obs ) => {
      entries.forEach( entry => {
        if ( ! entry.isIntersecting ) { return; }
        const img = entry.target;
        if ( img.dataset.src ) {
          img.src = img.dataset.src;
          img.removeAttribute( 'data-src' );
        }
        obs.unobserve( img );
      } );
    }, { rootMargin: '200px 0px' } );

    $$( 'img[data-src]' ).forEach( img => observer.observe( img ) );
  }

  /* ============================================================
     10. SMOOTH ANCHOR LINKS (offset for sticky nav)
     ============================================================ */

  function initSmoothAnchors() {
    const navH = 72;
    $$( 'a[href^="#"]:not([href="#"])' ).forEach( link => {
      link.addEventListener( 'click', e => {
        const target = $( link.getAttribute( 'href' ) );
        if ( ! target ) { return; }
        e.preventDefault();
        const top = target.getBoundingClientRect().top + window.scrollY - navH;
        window.scrollTo( { top, behavior: 'smooth' } );
        target.setAttribute( 'tabindex', '-1' );
        target.focus( { preventScroll: true } );
      } );
    } );
  }

  /* ============================================================
     11. DARK MODE TOGGLE EVENT BINDING
     ============================================================ */

  function initDarkModeToggles() {
    $$( '.dark-mode-toggle' ).forEach( btn => {
      btn.addEventListener( 'click', toggleTheme );
      btn.addEventListener( 'keydown', e => {
        if ( e.key === 'Enter' || e.key === ' ' ) { e.preventDefault(); toggleTheme(); }
      } );
    } );
  }

  /* ============================================================
     12. TABLE OF CONTENTS — Auto-generate (optional)
     ============================================================ */

  function initTOC() {
    const toc     = $( '.toc__list' );
    const content = $( '.single-body' );
    if ( ! toc || ! content ) { return; }

    const headings = $$( 'h2', content );
    if ( headings.length < 1 ) {
      const tocEl = toc.closest( '.toc' );
      if ( tocEl ) { tocEl.style.display = 'none'; }
      return;
    }

    headings.forEach( ( h, i ) => {
      if ( ! h.id ) { h.id = 'toc-' + i + '-' + h.textContent.trim().toLowerCase().replace( /\s+/g, '-' ).replace( /[^\w-]/g, '' ); }
      const li   = document.createElement( 'li' );
      const link = document.createElement( 'a' );
      link.href        = '#' + h.id;
      link.textContent = h.textContent;
      li.appendChild( link );
      toc.appendChild( li );
    } );
  }

  /* ============================================================
     13. LOAD MORE (AJAX)
     ============================================================ */

  function initLoadMore() {
    const btn = $( '.load-more-btn' );
    if ( ! btn || typeof GizmoData === 'undefined' ) { return; }

    let page = 2;

    btn.addEventListener( 'click', async () => {
      btn.disabled    = true;
      btn.textContent = 'Loading…';

      try {
        const fd = new FormData();
        fd.append( 'action', 'gizmo_load_more' );
        fd.append( 'nonce',  GizmoData.nonce );
        fd.append( 'page',   page );
        fd.append( 'cat',    btn.dataset.cat || '0' );

        const res  = await fetch( GizmoData.ajaxUrl, { method: 'POST', body: fd } );
        const data = await res.json();

        if ( data.success ) {
          const grid = $( '.bento-grid' ) || $( '.news-grid' ) || $( '.stories-grid' );
          if ( grid ) {
            grid.insertAdjacentHTML( 'beforeend', data.data.html );
          }
          page++;
          if ( ! data.data.has_more ) { btn.remove(); }
        }
      } catch ( err ) {
        console.error( 'Load more failed:', err );
      } finally {
        btn.disabled    = false;
        btn.textContent = 'Load More';
      }
    } );
  }

  /* ============================================================
     14. HOMEPAGE POST SLIDER
     ============================================================ */
  function initHomepageSlider() {
    const sliderContainer = $( '.post-slider-container' );
    if ( !sliderContainer ) { return; }

    const sliderTrack = $( '.post-slider-track', sliderContainer );
    const cards       = $$( '.post-item-card', sliderTrack );
    const prevButton  = $( '.slider-button-prev', sliderContainer );
    const nextButton  = $( '.slider-button-next', sliderContainer );

    if ( !sliderTrack || !prevButton || !nextButton ) return;

    let currentIndex = 0;
    let cardWidth = 0;
    let cardMarginRight = 0;
    let maxScrollPosition = 0;

    function updateMeasurements() {
      if ( cards.length === 0 ) {
        prevButton.disabled = true;
        nextButton.disabled = true;
        return;
      }
      const firstCardStyle = window.getComputedStyle( cards[0] );
      cardWidth = cards[0].offsetWidth;
      cardMarginRight = parseFloat( firstCardStyle.marginRight );

      const totalWidth = cards.reduce( ( acc, card ) => acc + card.offsetWidth + parseFloat( window.getComputedStyle( card ).marginRight ), 0 );
      maxScrollPosition = Math.max( 0, totalWidth - sliderContainer.offsetWidth );
    }

    function updateSliderButtons() {
      prevButton.disabled = ( currentIndex === 0 );
      const currentTranslateX = Math.abs( parseFloat( sliderTrack.style.transform.replace( 'translateX(', '' ).replace( 'px)', '' ) ) || 0 );
      nextButton.disabled = ( currentTranslateX >= maxScrollPosition - 1 ); // -1 for rounding errors
    }

    function slideTo( index ) {
      if ( cards.length === 0 ) return;
      currentIndex = Math.max( 0, Math.min( index, cards.length - 1 ) );

      let targetTranslateX = -currentIndex * ( cardWidth + cardMarginRight );
      targetTranslateX = Math.max( -maxScrollPosition, targetTranslateX );
      targetTranslateX = Math.min( 0, targetTranslateX );

      sliderTrack.style.transform = `translateX(${targetTranslateX}px)`;
      updateSliderButtons();
    }

    function slideLeft() {
      const cardsToScroll = Math.floor(sliderContainer.offsetWidth / (cardWidth + cardMarginRight));
      slideTo( currentIndex - cardsToScroll );
    }

    function slideRight() {
      const cardsToScroll = Math.floor(sliderContainer.offsetWidth / (cardWidth + cardMarginRight));
      slideTo( currentIndex + cardsToScroll );
    }

    prevButton.addEventListener( 'click', slideLeft );
    nextButton.addEventListener( 'click', slideRight );

    function initializeSlider() {
      updateMeasurements();
      slideTo( currentIndex );
    }

    initializeSlider();
    window.addEventListener( 'resize', initializeSlider );
  }

  /* ============================================================
     15. MOBILE GALLERY INTERACTION
     ============================================================ */
  function initMobileGallery() {
    const gallery = $( '.mobile-wrap' );
    if ( ! gallery ) return;

    const thumbnails = $$( '.thumbnail img', gallery );
    const display    = $( '#image-display', gallery );

    if ( ! display ) return;

    thumbnails.forEach( thumb => {
      thumb.addEventListener( 'click', () => {
        const fullSrc = thumb.getAttribute( 'data-full-image' );
        display.innerHTML = `<img src="${fullSrc}" alt="Full Image">`;
      } );
    } );
  }

  /* ============================================================
     16. MOBILE FILTERING
     ============================================================ */
  function initMobileFilter() {
    const filterNav = $( '.mobile-filter-nav' );
    if ( !filterNav || typeof GizmoData === 'undefined' ) return;

    const container = $( '#mobiles-grid-container' );
    const buttons = $$( '.mobile-filter-btn', filterNav );

    filterNav.addEventListener('click', async (e) => {
      if (!e.target.matches('.mobile-filter-btn')) return;

      const btn = e.target;
      if (btn.classList.contains('is-active')) return;

      const categoryId = btn.dataset.category;

      // Update active button state
      buttons.forEach(b => b.classList.remove('is-active'));
      btn.classList.add('is-active');

      // Show loading state
      container.style.opacity = '0.5';
      container.style.minHeight = '200px'; // prevent layout jump

      try {
        const fd = new FormData();
        fd.append( 'action', 'gizmo_filter_mobiles' );
        fd.append( 'nonce',  GizmoData.nonce );
        fd.append( 'category', categoryId );

        const res  = await fetch( GizmoData.ajaxUrl, { method: 'POST', body: fd } );
        const data = await res.json();

        if ( data.success ) {
          container.innerHTML = data.data.html;
        } else {
          container.innerHTML = '<p>Error loading posts.</p>';
        }
      } catch ( err ) {
        console.error( 'Mobile filter failed:', err );
        container.innerHTML = '<p>Error loading posts.</p>';
      } finally {
        container.style.opacity = '1';
      }
    });
  }

  /* ============================================================
     17. CAROUSEL SLIDER (Generic)
     ============================================================ */
  function initCarouselSlider() {
    const containers = $$('.gizmo-slider-block');
    if (!containers.length) return;

    containers.forEach(container => {
      const track = $('.gizmo-slider-track', container);
      const prevBtn = $('.gizmo-slider-btn.prev', container);
      const nextBtn = $('.gizmo-slider-btn.next', container);
      
      if (!track || !prevBtn || !nextBtn) return;

      const getScrollStep = () => track.clientWidth;

      prevBtn.addEventListener('click', () => {
        track.scrollBy({ left: -getScrollStep(), behavior: 'smooth' });
      });

      nextBtn.addEventListener('click', () => {
        track.scrollBy({ left: getScrollStep(), behavior: 'smooth' });
      });
    });
  }

  /* ============================================================
     19. CUSTOM FAQ ACCORDION
     ============================================================ */
  function initCustomFAQ() {
    const triggers = $$('.gizmo-faq-trigger');
    if (!triggers.length) return;

    triggers.forEach(btn => {
      btn.addEventListener('click', () => {
        const item = btn.closest('.gizmo-faq-item');
        const content = item.querySelector('.gizmo-faq-content');
        const isExpanded = btn.getAttribute('aria-expanded') === 'true';

        btn.setAttribute('aria-expanded', !isExpanded);
        content.hidden = isExpanded;
        item.classList.toggle('is-active', !isExpanded);
      });
    });
  }

/* ============================================================
   18. AMAZON SIDEBAR LOADER (Async)
   ============================================================ */
function initAmazonLoader() {
  const container = document.getElementById('gizmo-amazon-sidebar');
  if (!container || typeof GizmoData === 'undefined') return;

  const keyword = container.dataset.keyword;
  if (!keyword) {
    console.log('Amazon: No keyword provided');
    container.style.display = 'none';
    return;
  }

  // Show loading state
  container.innerHTML = '<div class="sidebar-amazon-loading">Loading products...</div>';

  const fd = new FormData();
  fd.append('action', 'gizmo_load_amazon_products');
  fd.append('nonce', GizmoData.nonce);
  fd.append('keyword', keyword);

  fetch(GizmoData.ajaxUrl, { method: 'POST', body: fd })
    .then(res => res.json())
    .then(data => {

      // ── Print PHP debug log in console ──
      if (data.data && data.data.debug) {
        console.group('🛒 Amazon PA API Debug');
        data.data.debug.forEach(line => console.log(line));
        console.groupEnd();
      }

      if (data.success && data.data.html) {
        container.innerHTML = data.data.html;
        
        // Log if using fallback
        if (data.data.fallback) {
          console.log('Amazon: Using fallback display (API not available or not configured)');
        }
      } else {
        console.error('Amazon Error:', data.data?.message ?? 'Unknown error');
        // Don't hide container on error, show fallback
        container.innerHTML = '<div class="sidebar-amazon-error">Unable to load products</div>';
      }
    })
    .catch(err => {
      console.error('Amazon fetch error:', err);
      container.innerHTML = '<div class="sidebar-amazon-error">Unable to load products</div>';
    });
}

/* ============================================================
   20. ASYNC AD LOADER
   Injects ad code from <template> only if device matches
   ============================================================ */
function initAsyncAds() {
  const slots = $$('.gizmo-async-ad');
  if (!slots.length) return;

  const width = window.innerWidth;
  const isMobile = width < 768;
  const isTablet = width >= 768 && width <= 1024;
  const isDesktop = width > 1024;

  slots.forEach(slot => {
    const target = slot.dataset.device;
    let shouldLoad = (target === 'all');
    if (target === 'mobile' && isMobile) shouldLoad = true;
    if (target === 'tablet' && isTablet) shouldLoad = true;
    if (target === 'desktop' && isDesktop) shouldLoad = true;
    if (target === 'no_mobile' && !isMobile) shouldLoad = true;

    if (shouldLoad) {
      const template = $('template', slot);
      if (template) {
        // Use template.content if available for better script handling
        if ('content' in template) {
          slot.innerHTML = '';
          slot.appendChild(template.content.cloneNode(true));
        } else {
          slot.innerHTML = template.innerHTML;
        }

        // Re-execute scripts to ensure they run
        $$('script', slot).forEach(oldScript => {
          const newScript = document.createElement('script');
          Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
          if (oldScript.innerHTML) {
            newScript.appendChild(document.createTextNode(oldScript.innerHTML));
          }
          oldScript.parentNode.replaceChild(newScript, oldScript);
        });
      }
    }
  });
}

/* ============================================================
   21. COMPARE PAGE LOGIC
   ============================================================ */
function initCompare() { // Merged from plugin
    const container = $('.compare-container');
    if (!container) return;

    const { ajaxUrl, nonce } = GizmoData;
    const maxDevices = 3;
    const placeholderImage = 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs='; // Transparent pixel

    let allDeviceSpecs = {};
    let searchDebounceTimer = null;

    const categories = [
        "Brand", "Model", "Price in India", "Launch Date", "Form Factor", "Dimensions", "Weight", "IP Rating", "Build", "Protection", "Colors", "Type", "Screen Size", "Resolution", "Brightness", "Processor", "CPU", "RAM", "Internal Storage", "GPU", "No of Rear Cameras", "Rear Camera", "Front Camera", "Features", "Video", "Front Video", "Operating System", "Skin", "Wi-Fi", "GPS", "Bluetooth", "USB Type-C", "SIM", "NFC", "Fingerprint", "Compass", "Proximity", "Accelerometer", "Gyroscope", "Battery Type", "Charging", "Wireless Charging"
    ];
    const higherIsBetter = ["ram", "internal storage", "screen size", "brightness", "no of rear cameras", "battery type"];
    const lowerIsBetter = ["price in india", "weight"];
    const numericSpecs = ["price in india", "ram", "internal storage", "screen size", "resolution", "weight", "dimensions", "brightness", "no of rear cameras"];
    const booleanSpecs = ["nfc", "wireless charging", "fingerprint"];

    function initializeComparisonTable() {
        const tableContainer = $('.comparison-table-container');
        let tableHtml = `<div class="comparison-table"><table class="tb-min"><thead><tr><th class="spec-col">Specification</th>`;
        for (let i = 1; i <= maxDevices; i++) {
            tableHtml += `<th class="device-slot-${i}">Device ${i}</th>`;
        }
        tableHtml += `</tr></thead><tbody>`;
        categories.forEach(category => {
            tableHtml += `<tr data-spec="${category}"><td>${category}</td>`;
            for (let i = 1; i <= maxDevices; i++) {
                tableHtml += `<td class="spec-val-${i}">-</td>`;
            }
            tableHtml += `</tr>`;
        });
        tableHtml += `</tbody></table></div>`;
        tableContainer.innerHTML = tableHtml;
    }

    function addControlButtons() {
        if (!$('#dc-share-btn')) {
            $('.highlight-differences').insertAdjacentHTML('beforeend', `<button id="dc-share-btn" class="dc-share-btn" title="Copy comparison link">🔗 Share</button>`);
        }
    }

    function extractSpecificationsFromContent(content) {
        const specs = {};
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = content;
        tempDiv.querySelectorAll('table tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length === 2) {
                const key = cells[0].textContent.trim();
                const value = cells[1].textContent.trim();
                if (key) specs[key] = value;
            }
        });
        return specs;
    }

    function parseNumeric(str) {
        return parseFloat((str || '').toString().replace(/[^0-9.]/g, ''));
    }

    function isSignificantDifference(specName, value1, value2) {
        const clean1 = (value1 || '').toString().trim().toLowerCase();
        const clean2 = (value2 || '').toString().trim().toLowerCase();
        if (clean1 === clean2) return { isDiff: false, isSignificant: false };
        if (numericSpecs.includes(specName.toLowerCase())) {
            const num1 = parseNumeric(clean1);
            const num2 = parseNumeric(clean2);
            if (!isNaN(num1) && !isNaN(num2) && num1 !== 0 && num2 !== 0) {
                const pctDiff = (Math.abs(num1 - num2) / ((num1 + num2) / 2)) * 100;
                if (pctDiff > 15) return { isDiff: true, isSignificant: true };
                if (pctDiff > 5) return { isDiff: true, isSignificant: false };
                return { isDiff: false, isSignificant: false };
            }
        }
        if (booleanSpecs.includes(specName.toLowerCase())) {
            const exists1 = clean1 === 'yes' || clean1 === 'true' || clean1 !== '-';
            const exists2 = clean2 === 'yes' || clean2 === 'true' || clean2 !== '-';
            if (exists1 !== exists2) return { isDiff: true, isSignificant: true };
        }
        return { isDiff: true, isSignificant: false };
    }

    function highlightDifferences() {
        const enabled = $('#my-compare-input-1').checked;
        $$('.tb-min tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            const specName = cells[0]?.textContent?.trim();
            const values = Array.from(cells).slice(1).map(cell => cell?.textContent?.trim() || '-');
            cells.forEach(cell => cell.classList.remove('significant-difference', 'minor-difference'));
            if (!enabled) return;
            const filledValues = values.filter(v => v !== '-' && v !== '');
            if (filledValues.length < 2) return;
            const referenceValue = filledValues[0];
            if (filledValues.every(v => v === referenceValue)) return;
            for (let i = 1; i < cells.length; i++) {
                const cellValue = values[i - 1];
                if (cellValue === '-' || cellValue === '') continue;
                const { isDiff, isSignificant } = isSignificantDifference(specName, cellValue, referenceValue);
                if (isDiff) {
                    cells[i].classList.add(isSignificant ? 'significant-difference' : 'minor-difference');
                }
            }
        });
    }

    function updateWinnerBadges() {
        $$('.tb-min tbody tr').forEach(row => {
            const cells = row.querySelectorAll('td');
            const specName = cells[0]?.textContent?.trim().toLowerCase();
            Array.from(cells).slice(1).forEach(cell => {
                const badge = cell.querySelector('.dc-winner-badge');
                if (badge) badge.remove();
            });
            const isHigher = higherIsBetter.includes(specName);
            const isLower = lowerIsBetter.includes(specName);
            if (!isHigher && !isLower) return;
            const values = Array.from(cells).slice(1).map(cell => parseNumeric(cell?.textContent?.trim()));
            const validValues = values.filter(v => !isNaN(v) && v > 0);
            if (validValues.length < 2) return;
            const bestVal = isHigher ? Math.max(...validValues) : Math.min(...validValues);
            for (let i = 1; i < cells.length; i++) {
                if (values[i - 1] === bestVal) {
                    cells[i].insertAdjacentHTML('beforeend', '<span class="dc-winner-badge">★ Best</span>');
                }
            }
        });
    }

    function updateTableUI(slug, slot) {
        const deviceSpecs = allDeviceSpecs[slug] || {};
        $$('.tb-min tbody tr').forEach(row => {
            const specName = row.querySelector('td:first-child').textContent.trim();
            const specValue = deviceSpecs[specName] || '-';
            row.querySelector(`td.spec-val-${slot}`).textContent = specValue;
        });
        const deviceTitle = $(`#compare-list-${slot} h3`)?.textContent;
        $(`.comparison-table thead th.device-slot-${slot}`).innerHTML = deviceTitle || `Device ${slot}`;
        updateWinnerBadges();
        highlightDifferences();
    }

    function clearSlot(slot) {
        const compareList = $(`#compare-list-${slot}`);
        const slug = compareList.querySelector('.compare-item')?.dataset.slug;
        if (slug) delete allDeviceSpecs[slug];
        compareList.innerHTML = '';
        $(`#search-input-${slot}`).value = '';
        $(`#search-results-${slot}`).innerHTML = '';
        $$('.tb-min tbody tr').forEach(row => {
            row.querySelector(`td.spec-val-${slot}`).textContent = '-';
        });
        $(`.comparison-table thead th.device-slot-${slot}`).textContent = `Device ${slot}`;
        updateWinnerBadges();
        highlightDifferences();
        updateURLWithDevices();
    }

    function renderDeviceCard(device, slot) {
        const price = (device.content.match(/₹[\d,]+/) || ['Price not available'])[0];
        return `<div class="compare-item" data-slug="${device.slug}">
                    <button class="dc-remove-btn" data-slot="${slot}" title="Remove device">✕</button>
                    <a href="${device.url}" class="compare-item-link">
                        <div class="image-container"><img src="${device.image || placeholderImage}" alt="${device.title}" onerror="this.src='${placeholderImage}'"></div>
                        <h3>${device.title}</h3>
                        <p class="price">${price}</p>
                    </a>
                </div>`;
    }

    async function addDeviceToComparison(slug, slot) {
        if (!slug || slug === 'undefined') return;
        const compareList = $(`#compare-list-${slot}`);
        if (compareList.querySelector(`[data-slug="${slug}"]`)) return;
        compareList.innerHTML = '<div class="dc-loading">Loading...</div>';
        try {
            const res = await fetch(`${ajaxUrl}?action=gizmo_dc_handle_comparison&security=${nonce}&slugs[]=${slug}`);
            const response = await res.json();
            if (response.success && response.data.devices?.[0]) {
                const device = response.data.devices[0];
                compareList.innerHTML = renderDeviceCard(device, slot);
                allDeviceSpecs[device.slug] = extractSpecificationsFromContent(device.content);
                updateTableUI(device.slug, slot);
                updateURLWithDevices();
            } else {
                compareList.innerHTML = '';
            }
        } catch (err) {
            compareList.innerHTML = '';
        }
    }

    function updateURLWithDevices() {
        const params = new URLSearchParams();
        let hasDevices = false;
        for (let slot = 1; slot <= maxDevices; slot++) {
            const slug = $(`#compare-list-${slot} .compare-item`)?.dataset.slug;
            if (slug && slug !== 'undefined') {
                params.set(`device${slot}`, slug);
                hasDevices = true;
            }
        }
        const newUrl = hasDevices ? `${window.location.pathname}?${params.toString()}` : window.location.pathname;
        history.replaceState(null, '', newUrl);
    }

    function loadDevicesFromURL() {
        const urlParams = new URLSearchParams(window.location.search);
        for (let slot = 1; slot <= maxDevices; slot++) {
            const slug = urlParams.get(`device${slot}`);
            if (slug) addDeviceToComparison(slug, slot);
        }
    }

    function showNotice(message, type = 'success') {
        $('#dc-notice')?.remove();
        const notice = document.createElement('div');
        notice.id = 'dc-notice';
        notice.className = `dc-notice dc-notice-${type}`;
        notice.textContent = message;
        document.body.appendChild(notice);
        setTimeout(() => notice.classList.add('dc-notice-visible'), 10);
        setTimeout(() => {
            notice.classList.remove('dc-notice-visible');
            setTimeout(() => notice.remove(), 400);
        }, 3000);
    }

    async function searchDevices(query, slot) {
        const resultsContainer = $(`#search-results-${slot}`);
        if (query.length < 2) {
            resultsContainer.innerHTML = '';
            return;
        }
        resultsContainer.innerHTML = '<div class="dc-search-loading">Searching...</div>';
        try {
            const res = await fetch(`${ajaxUrl}?action=gizmo_dc_search_devices&security=${nonce}&query=${query}`);
            const response = await res.json();
            resultsContainer.innerHTML = '';
            if (response.success && response.data.length > 0) {
                response.data.forEach(device => {
                    resultsContainer.insertAdjacentHTML('beforeend', `
                        <div class="search-result-item" data-slug="${device.slug}">
                            <img src="${device.image || placeholderImage}" alt="${device.title}" onerror="this.src='${placeholderImage}'">
                            <div><strong>${device.title}</strong><span class="search-result-price">${device.price || ''}</span></div>
                        </div>`);
                });
            } else {
                resultsContainer.innerHTML = '<div class="dc-no-results">No devices found.</div>';
            }
        } catch (err) {
            resultsContainer.innerHTML = '<div class="dc-no-results">Search failed.</div>';
        }
    }

    // Event Listeners
    $$('.search-group input').forEach(input => {
        input.addEventListener('input', function () {
            const slot = this.closest('.search-group').dataset.slot;
            const query = this.value.trim();
            clearTimeout(searchDebounceTimer);
            searchDebounceTimer = setTimeout(() => searchDevices(query, slot), 300);
        });
    });

    document.addEventListener('click', function (e) {
        if (e.target.closest('.search-result-item')) {
            const item = e.target.closest('.search-result-item');
            const slug = item.dataset.slug;
            const slot = item.closest('.search-group').dataset.slot;
            item.closest('.search-results').innerHTML = '';
            $(`#search-input-${slot}`).value = '';
            addDeviceToComparison(slug, slot);
        } else if (e.target.closest('.dc-remove-btn')) {
            e.preventDefault();
            e.stopPropagation();
            const slot = parseInt(e.target.closest('.dc-remove-btn').dataset.slot);
            clearSlot(slot);
        } else if (e.target.closest('#dc-share-btn')) {
            updateURLWithDevices();
            navigator.clipboard.writeText(window.location.href).then(() => showNotice('Comparison link copied!'));
        } else if (!e.target.closest('.search-group')) {
            $$('.search-results').forEach(el => el.innerHTML = '');
        }
    });

    $('#my-compare-input-1').addEventListener('change', highlightDifferences);

    // Init
    initializeComparisonTable();
    addControlButtons();
    loadDevicesFromURL();
    highlightDifferences();
}

  /* ============================================================
     INIT ALL
     ============================================================ */

  function init() {
    initDarkModeToggles();
    initProgressBar();
    initScrollTop();
    initSearch();
    initMobileNav();
    initShareButtons();
    initCommentToggle();
    initStickyNav();
    initLazyImages();
    initSmoothAnchors();
    initTOC();
    initLoadMore();
    initHomepageSlider();
    initMobileGallery();
    initMobileFilter();
    initCarouselSlider();
    initAmazonLoader();
    initCustomFAQ();
    initAsyncAds();
    initCompare();
  }

  // DOM-ready
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
  } else {
    init();
  }

} )();
