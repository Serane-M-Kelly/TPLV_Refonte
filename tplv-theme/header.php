<!doctype html>
<html <?php language_attributes(); ?>>
<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>"/>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <meta name="theme-color" content="#011D42"/>
  <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>

<!-- ═══════════════════════════════
     LOADER
════════════════════════════════ -->
<div id="page-loader" aria-hidden="true">
  <div class="loader-inner">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200" width="88" height="88" aria-hidden="true">
      <defs>
        <linearGradient id="lg-loader" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" style="stop-color:#E91E85"/>
          <stop offset="100%" style="stop-color:#C4145A"/>
        </linearGradient>
        <filter id="glow-loader">
          <feGaussianBlur stdDeviation="2.5" result="blur"/>
          <feMerge><feMergeNode in="blur"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>
      <g transform="translate(100,92) scale(0.72)">
        <path d="M 0,40 C -5,35 -12,18 -20,5 C -28,-8 -42,-28 -55,-32 C -68,-36 -82,-30 -85,-18 C -88,-6 -84,10 -75,25 C -66,40 -50,58 -35,72 C -20,86 -8,95 0,100 C 8,95 20,86 35,72 C 50,58 66,40 75,25 C 84,10 88,-6 85,-18 C 82,-30 68,-36 55,-32 C 42,-28 28,-8 20,5 C 12,18 5,35 0,40 Z"
          fill="none" stroke="url(#lg-loader)" stroke-width="8" stroke-linecap="round"
          filter="url(#glow-loader)" stroke-dasharray="800" stroke-dashoffset="800">
          <animate attributeName="stroke-dashoffset" values="800;0;0;800"
            keyTimes="0;0.4;0.7;1" dur="3s" repeatCount="indefinite"
            calcMode="spline" keySplines="0.4 0 0.2 1;0.4 0 0.2 1;0.4 0 0.2 1"/>
          <animate attributeName="stroke-width" values="7;9;8;10;7" dur="3s" repeatCount="indefinite"/>
        </path>
        <path d="M 0,40 C -5,35 -12,18 -20,5 C -28,-8 -42,-28 -55,-32 C -68,-36 -82,-30 -85,-18 C -88,-6 -84,10 -75,25 C -66,40 -50,58 -35,72 C -20,86 -8,95 0,100 C 8,95 20,86 35,72 C 50,58 66,40 75,25 C 84,10 88,-6 85,-18 C 82,-30 68,-36 55,-32 C 42,-28 28,-8 20,5 C 12,18 5,35 0,40 Z"
          fill="#E91E85" opacity="0">
          <animate attributeName="opacity" values="0;0;0.18;0.05;0.18;0.05;0;0"
            keyTimes="0;0.4;0.48;0.52;0.58;0.62;0.7;1" dur="3s" repeatCount="indefinite"/>
        </path>
      </g>
      <circle cx="55" cy="42" r="2" fill="#E91E85" opacity="0">
        <animate attributeName="opacity" values="0;0;0.8;0;0" keyTimes="0;0.45;0.55;0.65;1" dur="3s" repeatCount="indefinite"/>
        <animate attributeName="r" values="1;3;1" dur="3s" repeatCount="indefinite"/>
      </circle>
      <circle cx="145" cy="42" r="2" fill="#FF69B4" opacity="0">
        <animate attributeName="opacity" values="0;0;0.7;0;0" keyTimes="0;0.5;0.58;0.68;1" dur="3s" repeatCount="indefinite"/>
        <animate attributeName="r" values="1;2.5;1" dur="3s" repeatCount="indefinite"/>
      </circle>
      <circle cx="100" cy="30" r="1.5" fill="#E91E85" opacity="0">
        <animate attributeName="opacity" values="0;0;0.9;0;0" keyTimes="0;0.42;0.52;0.62;1" dur="3s" repeatCount="indefinite"/>
        <animate attributeName="r" values="1;3;1" dur="3s" repeatCount="indefinite"/>
      </circle>
    </svg>
    <span class="loader-text">Chargement…</span>
  </div>
</div>

<!-- ═══════════════════════════════
     HEADER
════════════════════════════════ -->
<header id="site-header">
  <div class="container">
    <div class="header-inner">
      <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="header-logo">
        <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-transparent.png' ); ?>"
             alt="<?php bloginfo( 'name' ); ?>" width="500" height="500">
      </a>
      <nav class="header-nav" aria-label="Navigation principale">
        <?php wp_nav_menu( [
            'theme_location' => 'primary',
            'container'      => false,
            'items_wrap'     => '%3$s',
            'walker'         => new TPLV_Nav_Walker(),
            'fallback_cb'    => false,
        ] ); ?>
      </nav>
      <div class="header-cta">
        <a href="<?php echo esc_url( home_url( '/dons/' ) ); ?>" class="btn btn-primary">
          <i data-lucide="heart"></i> Faire un don
        </a>
        <button class="hamburger" id="hamburger" aria-label="Menu" aria-expanded="false">
          <span></span><span></span><span></span>
        </button>
      </div>
    </div>
  </div>
</header>

<!-- Nav mobile -->
<nav class="mobile-overlay" id="mobile-overlay" aria-label="Navigation mobile">
  <?php wp_nav_menu( [
      'theme_location' => 'primary',
      'container'      => false,
      'items_wrap'     => '%3$s',
      'walker'         => new TPLV_Mobile_Nav_Walker(),
      'fallback_cb'    => false,
  ] ); ?>
  <a href="<?php echo esc_url( home_url( '/dons/' ) ); ?>" class="btn btn-primary mobile-link">
    <i data-lucide="heart"></i> Faire un don
  </a>
</nav>

<main>
