<?php
/**
 * Page de confirmation post-formulaire.
 *
 * Slug WP à créer : "confirmation"
 * CF7 → Paramètres → Après l'envoi → Redirection de page → /confirmation/
 *
 * La query string ?type=benevoles|contact|apa permet d'adapter le message.
 */
get_header();

$type = isset( $_GET['type'] ) ? sanitize_key( $_GET['type'] ) : 'contact';

$messages = [
    'benevoles' => [
        'icon'  => 'heart-handshake',
        'titre' => 'Candidature envoyée !',
        'texte' => 'Merci pour votre intérêt pour le bénévolat TPLV. Nous avons bien reçu votre candidature et vous recontacterons très prochainement.',
        'cta'   => [ 'url' => home_url( '/benevoles/' ), 'label' => '← Retour à la page bénévoles' ],
    ],
    'apa' => [
        'icon'  => 'activity',
        'titre' => 'Demande APA envoyée !',
        'texte' => "Votre demande d'inscription à l'Activité Physique Adaptée a bien été transmise. Un membre de l'équipe vous contactera dans les meilleurs délais. Vos données médicales ne sont pas conservées.",
        'cta'   => [ 'url' => home_url( '/apa/' ), 'label' => '← Retour à la page APA' ],
    ],
    'contact' => [
        'icon'  => 'mail-check',
        'titre' => 'Message envoyé !',
        'texte' => "Votre message a bien été transmis à l'équipe TPLV. Nous vous répondrons dans les meilleurs délais (généralement sous 48h).",
        'cta'   => [ 'url' => home_url( '/' ), 'label' => "← Retour à l'accueil" ],
    ],
];

$msg = $messages[ $type ] ?? $messages['contact'];
?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header">
    <div class="container">
      <span class="eyebrow">Confirmation</span>
      <h1><?php echo esc_html( $msg['titre'] ); ?></h1>
      <p><?php echo esc_html( $msg['texte'] ); ?></p>
    </div>
  </div>

  <div class="section">
    <div class="container" style="text-align:center;padding:3rem 0">
      <div style="font-size:4rem;margin-bottom:1.5rem;color:var(--magenta)" aria-hidden="true">
        <i data-lucide="<?php echo esc_attr( $msg['icon'] ); ?>"></i>
      </div>
      <div style="display:flex;gap:1rem;justify-content:center;flex-wrap:wrap">
        <a href="<?php echo esc_url( $msg['cta']['url'] ); ?>" class="btn btn-outline">
          <?php echo esc_html( $msg['cta']['label'] ); ?>
        </a>
        <?php if ( $msg['cta']['url'] !== home_url( '/' ) ) : ?>
        <a href="<?php echo esc_url( home_url( '/' ) ); ?>" class="btn btn-primary">
          Retour à l'accueil
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
