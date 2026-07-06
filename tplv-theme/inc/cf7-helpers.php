<?php
/**
 * Helpers Contact Form 7
 *
 * Récupère un formulaire CF7 par son titre, évitant d'avoir à
 * coder en dur les IDs qui changent entre installations.
 *
 * Usage dans un template :
 *   echo tplv_cf7( 'Contact TPLV' );
 *   echo tplv_cf7( 'Bénévoles TPLV' );
 *   echo tplv_cf7( 'APA TPLV' );
 */

function tplv_cf7( string $form_title, string $html_id = '' ): string {

    // CF7 non installé ou non activé
    if ( ! function_exists( 'wpcf7_contact_form' ) ) {
        return '<p class="notice-rgpd">⚠️ Le plugin <strong>Contact Form 7</strong> doit être installé et activé pour afficher ce formulaire.</p>';
    }

    $forms = get_posts( [
        'post_type'      => 'wpcf7_contact_form',
        'title'          => $form_title,
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ] );

    if ( empty( $forms ) ) {
        return sprintf(
            "<p class=\"notice-rgpd\">⚠️ Formulaire CF7 introuvable : <strong>%s</strong>. Créez-le dans <em>Contact → Ajouter</em> dans l'admin WP.</p>",
            esc_html( $form_title )
        );
    }

    $id_attr = $html_id ? ' html_id="' . esc_attr( $html_id ) . '"' : '';
    return do_shortcode( '[contact-form-7 id="' . $forms[0] . '"' . $id_attr . ']' );
}
