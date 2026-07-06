<?php

function tplv_enqueue_assets() {
    $uri = get_template_directory_uri();
    $ver = wp_get_theme()->get( 'Version' );

    wp_enqueue_style( 'tplv-fonts',
        'https://fonts.googleapis.com/css2?family=Raleway:wght@300;400;600;700;800&family=Open+Sans:wght@400;600&display=swap',
        [], null
    );
    wp_enqueue_style( 'tplv-tokens',     $uri . '/assets/css/tokens.css',     [],              $ver );
    wp_enqueue_style( 'tplv-base',       $uri . '/assets/css/base.css',       ['tplv-tokens'], $ver );
    wp_enqueue_style( 'tplv-header',     $uri . '/assets/css/header.css',     ['tplv-base'],   $ver );
    wp_enqueue_style( 'tplv-footer',     $uri . '/assets/css/footer.css',     ['tplv-base'],   $ver );
    wp_enqueue_style( 'tplv-components', $uri . '/assets/css/components.css', ['tplv-base'],   $ver );

    wp_enqueue_script( 'tplv-lucide',  'https://unpkg.com/lucide@latest',        [],              null, true );
    wp_enqueue_script( 'tplv-nombres-animes', $uri . '/assets/js/nombres-animes.js', ['tplv-lucide'], $ver,  true );
    wp_enqueue_script( 'tplv-main',    $uri . '/assets/js/main.js',    ['tplv-lucide'], $ver,  true );
}
add_action( 'wp_enqueue_scripts', 'tplv_enqueue_assets' );

add_filter( 'wp_resource_hints', function ( $hints, $relation_type ) {
    if ( 'preconnect' === $relation_type ) {
        $hints[] = [ 'href' => 'https://fonts.googleapis.com' ];
        $hints[] = [ 'href' => 'https://fonts.gstatic.com', 'crossorigin' => 'anonymous' ];
    }
    return $hints;
}, 10, 2 );
