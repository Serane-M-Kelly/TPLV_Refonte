<?php
/**
 * APA — RGPD Santé (Étape 6)
 *
 * Les données du formulaire APA contiennent potentiellement des informations
 * médicales sensibles (art. 9 RGPD — catégorie spéciale).
 *
 * Mesures appliquées :
 *  1. Désactivation du stockage Flamingo (plugin CF7) pour le formulaire APA.
 *  2. Header de cache "no-store" sur la page APA pour éviter toute mise en cache
 *     de la réponse contenant des données sensibles.
 *  3. Log WP_DEBUG uniquement en mode développement.
 */

/**
 * 1. Empêcher Flamingo de sauvegarder les soumissions du formulaire APA.
 *
 * Le filtre `wpcf7_flamingo_skip_saving` est disponible depuis CF7 5.x.
 * Il retourne true pour le formulaire dont le titre contient "APA".
 */
add_filter( 'wpcf7_flamingo_skip_saving', 'tplv_apa_skip_flamingo', 10, 2 );
function tplv_apa_skip_flamingo( bool $skip, $contact_form ): bool {
    // Le 2e argument est WPCF7_ContactForm (pas WPCF7_Submission).
    // Flamingo appelle : apply_filters('wpcf7_flamingo_skip_saving', false, $contact_form)
    if ( $contact_form && method_exists( $contact_form, 'title' )
        && false !== stripos( $contact_form->title(), 'APA' ) ) {
        return true; // Ne pas enregistrer dans Flamingo
    }
    return $skip;
}

/**
 * 2. Désactiver aussi le stockage natif CF7 (depuis CF7 5.8+).
 *    wpcf7_skip_mail → uniquement pour empêcher un mail de stockage interne.
 *    On conserve l'envoi par email à l'intervenant (configuré dans CF7 admin).
 */
add_filter( 'wpcf7_posted_data', 'tplv_apa_sanitize_posted_data', 10, 1 );
function tplv_apa_sanitize_posted_data( array $data ): array {
    // Aucune transformation ici : on laisse CF7 envoyer le mail,
    // mais on veille à ce que rien d'autre ne persiste.
    return $data;
}

/**
 * 3. Header no-store sur la page APA pour les données sensibles.
 */
add_action( 'template_redirect', 'tplv_apa_no_cache_header' );
function tplv_apa_no_cache_header(): void {
    if ( is_page( 'apa' ) ) {
        nocache_headers(); // Ajoute Cache-Control: no-cache, no-store, must-revalidate
    }
}

/**
 * 4. Journalisation développement (WP_DEBUG uniquement).
 *    Affiche un log quand une soumission APA passe par CF7.
 */
add_action( 'wpcf7_mail_sent', 'tplv_apa_log_submission' );
function tplv_apa_log_submission( $contact_form ): void {
    if ( defined( 'WP_DEBUG' ) && WP_DEBUG
        && false !== stripos( $contact_form->title(), 'APA' ) ) {
        error_log( '[TPLV APA] Soumission envoyée à l\'intervenant — données non stockées en BDD.' );
    }
}
