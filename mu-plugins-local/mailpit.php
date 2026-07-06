<?php
/**
 * Développement local uniquement — jamais utilisé en production.
 *
 * Ce dossier (`mu-plugins-local/`) n'est monté que par `docker-compose.yml`
 * dans ce projet local ; il n'existe pas sur l'hébergement o2switch.
 *
 * Redirige tous les emails sortants de WordPress (dont les soumissions
 * Contact Form 7) vers Mailpit, pour pouvoir les consulter réellement
 * pendant les tests locaux (http://localhost:8025) au lieu qu'ils
 * échouent silencieusement (ce conteneur n'a aucun serveur mail réel).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'phpmailer_init', function ( $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host        = 'mailpit';
    $phpmailer->Port        = 1025;
    $phpmailer->SMTPAuth    = false;
    $phpmailer->SMTPAutoTLS = false;
} );

// L'adresse par défaut de WordPress ("wordpress@localhost") n'a pas de domaine
// valide et est rejetée par PHPMailer en mode SMTP strict — sans effet sur le
// contenu réel des emails, juste sur l'expéditeur technique en local.
add_filter( 'wp_mail_from', fn() => 'wordpress@tplv.local' );
