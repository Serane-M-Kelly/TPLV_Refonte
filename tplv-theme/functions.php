<?php

// Masquer les notices PHP en front-end (elles restent dans debug.log)
if ( ! is_admin() ) {
    ini_set( 'display_errors', '0' );
}

add_theme_support( 'title-tag' );
add_theme_support( 'post-thumbnails' );

register_nav_menus( [
    'primary' => 'Navigation principale',
    'footer'  => 'Navigation pied de page',
] );

require_once get_template_directory() . '/inc/enqueue.php';
require_once get_template_directory() . '/inc/nav-walkers.php';
require_once get_template_directory() . '/inc/cpt-actualites.php';
require_once get_template_directory() . '/inc/cpt-evenements.php';
require_once get_template_directory() . '/inc/cpt-documents.php';
require_once get_template_directory() . '/inc/cpt-partenaires.php';
require_once get_template_directory() . '/inc/cf7-helpers.php';
require_once get_template_directory() . '/inc/apa.php';
require_once get_template_directory() . '/inc/helloasso.php';
require_once get_template_directory() . '/inc/seo-rgpd.php';
require_once get_template_directory() . '/inc/reglages-tplv.php';
require_once get_template_directory() . '/inc/roles-tplv.php';
require_once get_template_directory() . '/inc/admin-tplv.php';
require_once get_template_directory() . '/inc/simplifier-admin.php';
require_once get_template_directory() . '/inc/formulaires-rapides.php';
