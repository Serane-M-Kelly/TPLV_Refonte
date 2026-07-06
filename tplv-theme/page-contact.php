<?php get_header(); ?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header">
    <div class="container">
      <span class="eyebrow">Nous joindre</span>
      <h1>Contact</h1>
      <p>Une question ? Un projet de partenariat ? Une envie de s'investir ? Écrivez-nous.</p>
    </div>
  </div>

  <!-- CONTACT -->
  <div class="section">
    <div class="container">
      <div class="contact-layout">

        <!-- Coordonnées + carte -->
        <div class="contact-info fade-in">
          <div class="contact-info-item">
            <div class="contact-info-icon"><i data-lucide="map-pin"></i></div>
            <div>
              <h3>Adresse</h3>
              <p>Association TPLV<br><?php echo nl2br( esc_html( tplv_opt( 'adresse', "28 rue Jean-Marie Lacire\n35150 Janzé" ) ) ); ?></p>
            </div>
          </div>
          <?php $tplv_email = tplv_opt( 'email', 'contact@tplv-janze.fr' ); ?>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i data-lucide="mail"></i></div>
            <div>
              <h3>Email</h3>
              <a href="mailto:<?php echo esc_attr( $tplv_email ); ?>"><?php echo esc_html( $tplv_email ); ?></a>
            </div>
          </div>
          <?php $tplv_tel = tplv_opt( 'telephone', '02 99 47 00 00' ); ?>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i data-lucide="phone"></i></div>
            <div>
              <h3>Téléphone</h3>
              <a href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $tplv_tel ) ); ?>"><?php echo esc_html( $tplv_tel ); ?></a><br>
              <span class="contact-hours">Du lundi au vendredi, 9h–12h</span>
            </div>
          </div>
          <div class="contact-info-item">
            <div class="contact-info-icon"><i data-lucide="smartphone"></i></div>
            <div>
              <h3>Réseaux sociaux</h3>
              <div class="social-links">
                <a href="<?php echo esc_url( tplv_opt( 'facebook', '#' ) ); ?>" class="social-link" aria-label="Facebook">f</a>
                <a href="<?php echo esc_url( tplv_opt( 'instagram', '#' ) ); ?>" class="social-link" aria-label="Instagram">in</a>
                <a href="#" class="social-link" aria-label="Twitter" title="Lien à confirmer">𝕏</a>
              </div>
            </div>
          </div>
          <div class="map-placeholder">
            <i data-lucide="map-pin"></i> Carte Google Maps — Janzé (35150)<br>
            <span class="map-note">Iframe intégré via shortcode WordPress</span>
          </div>
        </div>

        <!-- Formulaire Contact — CF7 -->
        <div class="fade-in">
          <div class="notice-rgpd">
            🔒 Vos données sont utilisées uniquement pour répondre à votre message. Elles ne sont jamais transmises à des tiers.
          </div>
          <?php echo tplv_cf7( 'Contact TPLV', 'form-contact' ); ?>
        </div>

      </div>
    </div>
  </div>

<?php get_footer(); ?>
