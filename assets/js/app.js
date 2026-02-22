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
    const body = $( '.post-content' ) || document.body;
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
    const content = $( '.post-content' );
    if ( ! toc || ! content ) { return; }

    const headings = $$( 'h2, h3', content );
    if ( headings.length < 3 ) {
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
      li.style.paddingLeft = h.tagName === 'H3' ? '1rem' : '0';
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
  }

  // DOM-ready
  if ( document.readyState === 'loading' ) {
    document.addEventListener( 'DOMContentLoaded', init );
  } else {
    init();
  }

} )();
