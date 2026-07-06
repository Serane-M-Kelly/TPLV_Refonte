</main>

<!-- ═══════════════════════════════
     FOOTER
════════════════════════════════ -->
<footer id="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand">
        <div class="footer-brand-inner">
          <img src="<?php echo esc_url( get_template_directory_uri() . '/assets/images/logo-sombre.png' ); ?>"
               alt="<?php bloginfo( 'name' ); ?>" width="557" height="448">
        </div>
        <p class="footer-desc">Association loi 1901 structurée en 2004, TPLV s'inscrit dans une mobilisation locale engagée depuis les années 1990, autour d'un week-end festif et sportif (premier week-end d'octobre) pour soutenir la recherche contre le cancer.</p>
        <div class="footer-social">
          <a href="<?php echo esc_url( tplv_opt( 'facebook', '#' ) ); ?>" aria-label="Facebook">f</a>
          <a href="<?php echo esc_url( tplv_opt( 'instagram', '#' ) ); ?>" aria-label="Instagram">in</a>
          <a href="#" aria-label="Twitter" title="Lien à confirmer">𝕏</a>
        </div>
      </div>
      <div class="footer-col">
        <h4>Navigation</h4>
        <?php wp_nav_menu( [
            'theme_location' => 'footer',
            'container'      => false,
            'fallback_cb'    => false,
        ] ); ?>
      </div>
      <div class="footer-col">
        <h4>Contact</h4>
        <div class="footer-contact">
          <p>28 rue Jean-Marie Lacire<br>35150 Janzé</p>
          <p><a href="mailto:contact@tplv-janze.fr">contact@tplv-janze.fr</a></p>
          <p><a href="tel:+33299470000">02 99 47 00 00</a></p>
        </div>
      </div>
      <div class="footer-col">
        <h4>Soutenir TPLV</h4>
        <p>Chaque don, même modeste, contribue directement à la recherche contre le cancer.</p>
        <div class="footer-don">
          <a href="<?php echo esc_url( home_url( '/dons/' ) ); ?>" class="btn btn-primary">
            <i data-lucide="heart"></i> Faire un don
          </a>
        </div>
        <p class="footer-don-note">Don déductible des impôts à hauteur de 66%</p>
      </div>
    </div>
    <div class="footer-bottom">
      <span>© <?php echo date( 'Y' ); ?> Association Tous Pour La Vie — Janzé (35)</span>
      <div class="footer-legal">
        <a href="#" title="Lien à confirmer">Mentions légales</a>
        <a href="#" title="Lien à confirmer">Politique de confidentialité</a>
        <a href="#" title="Lien à confirmer">CGU</a>
      </div>
    </div>
  </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
