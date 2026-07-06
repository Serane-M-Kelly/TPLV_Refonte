<?php
/**
 * Rôle "Gestionnaire TPLV" (Phase Admin 3).
 *
 * Rôle destiné au bureau de l'association : gère les CPT (Actualités,
 * Événements) et les Réglages TPLV, sans jamais avoir `manage_options`
 * (donc sans accès aux plugins, thèmes ou utilisateurs).
 *
 * `manage_tplv_settings` est une capacité dédiée à la page "Réglages TPLV" :
 * elle remplace `manage_options` pour cette page précise, et est accordée
 * ici à la fois à ce nouveau rôle et à `administrator` (ajout, aucune
 * capacité existante n'est retirée aux administrateurs).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'init', 'tplv_register_role_gestionnaire' );
function tplv_register_role_gestionnaire(): void {
    $administrator = get_role( 'administrator' );
    if ( $administrator && ! $administrator->has_cap( 'manage_tplv_settings' ) ) {
        $administrator->add_cap( 'manage_tplv_settings' );
    }

    if ( get_role( 'gestionnaire_tplv' ) ) {
        return; // Déjà créé — on ne recrée jamais un rôle existant.
    }

    $editor = get_role( 'editor' );
    $caps   = $editor ? $editor->capabilities : [];

    $caps['manage_tplv_settings'] = true;

    add_role( 'gestionnaire_tplv', 'Gestionnaire TPLV', $caps );
}
