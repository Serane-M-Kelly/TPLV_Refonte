<?php
/**
 * Simplification de l'admin WordPress natif (Phase Admin 4, étendue Phase Admin 9).
 *
 * Le rôle "Gestionnaire TPLV" (bureau + bénévoles) n'utilise jamais les
 * Articles, Pages ou Commentaires natifs : les CPT Actualités/Événements
 * ne déclarent pas `comments`, et les Pages du thème sont des templates
 * sur-mesure qui n'affichent jamais `the_content()`. Masquer ces menus
 * évite la confusion pour un profil non technique.
 *
 * Les menus natifs de CF7, Yoast SEO et Complianz sont masqués pour la même
 * raison : TPLV → Formulaires (Phase Admin 8) et TPLV → Réglages offrent déjà
 * un accès guidé aux fonctions dont ce rôle a besoin, sans le reste de
 * l'interface de configuration de ces plugins.
 *
 * Les administrateurs (`manage_options`) gardent l'interface WordPress
 * complète et inchangée.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'tplv_simplifier_menus_admin', 999 );
function tplv_simplifier_menus_admin(): void {
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }
    remove_menu_page( 'edit.php' );                // Articles (natif, non utilisé sur ce site)
    remove_menu_page( 'edit.php?post_type=page' );  // Pages (non fonctionnel avec les templates sur-mesure)
    remove_menu_page( 'edit-comments.php' );        // Commentaires (natif, non utilisé)
    remove_menu_page( 'wpcf7' );                    // Contact Form 7 — passer par TPLV → Formulaires
    remove_menu_page( 'wpseo_dashboard' );           // Yoast SEO — configuration réservée aux administrateurs
    remove_menu_page( 'complianz' );                 // Complianz — configuration réservée aux administrateurs
}

add_action( 'wp_dashboard_setup', 'tplv_simplifier_widgets_dashboard', 999 );
function tplv_simplifier_widgets_dashboard(): void {
    if ( current_user_can( 'manage_options' ) ) {
        return;
    }
    remove_meta_box( 'dashboard_right_now', 'dashboard', 'normal' );  // Coup d'œil
    remove_meta_box( 'dashboard_activity', 'dashboard', 'normal' );   // Activité
    remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );  // Brouillon rapide
    remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );      // Actus WordPress
}
