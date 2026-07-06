<?php get_header(); ?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header">
    <div class="container">
      <span class="eyebrow">Ensemble, on va plus loin</span>
      <h1>Nos partenaires</h1>
      <p>Entreprises, collectivités et associations — ils font confiance à TPLV et soutiennent la lutte contre le cancer.</p>
    </div>
  </div>

  <!-- PARTENAIRES -->
  <div class="section">
    <div class="container">

      <?php
      $parrains = new WP_Query( [
          'post_type'      => 'partenaire',
          'posts_per_page' => -1,
          'orderby'        => 'menu_order',
          'order'          => 'ASC',
          'meta_key'       => '_partenaire_type',
          'meta_value'     => 'parrain',
      ] );
      ?>
      <?php $a_des_parrains = $parrains->have_posts(); ?>
      <?php if ( $a_des_parrains ) : ?>
          <div class="section-header fade-in">
              <span class="eyebrow eyebrow--magenta">Édition <?php echo esc_html( tplv_opt( 'derniere_annee', date( 'Y' ) ) ); ?></span>
              <h2 class="section-title">Parrains &amp; marraines de l'édition</h2>
          </div>
          <div class="partners-main">
              <?php while ( $parrains->have_posts() ) : $parrains->the_post();
                  $logo_id  = (int) get_post_meta( get_the_ID(), '_partenaire_logo_id', true );
                  $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                  $lien     = get_post_meta( get_the_ID(), '_partenaire_lien', true );
                  ?>
                  <?php if ( $lien ) : ?><a href="<?php echo esc_url( $lien ); ?>" target="_blank" rel="noopener" class="partner-logo fade-in"><?php else : ?><div class="partner-logo fade-in"><?php endif; ?>
                      <?php if ( $logo_url ) : ?>
                          <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                      <?php else : ?>
                          <?php the_title(); ?>
                      <?php endif; ?>
                  <?php echo $lien ? '</a>' : '</div>'; ?>
              <?php endwhile; wp_reset_postdata(); ?>
          </div>

          <div class="section-divider"></div>
      <?php endif; ?>

      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">Nos partenaires</span>
        <h2 class="section-title<?php echo $a_des_parrains ? ' section-title--sm' : ''; ?>">Ils s'engagent à nos côtés</h2>
      </div>
      <div class="partners-assoc">
        <?php
        $partenaires = new WP_Query( [
            'post_type'      => 'partenaire',
            'posts_per_page' => -1,
            'orderby'        => 'menu_order',
            'order'          => 'ASC',
            'meta_key'       => '_partenaire_type',
            'meta_value'     => 'partenaire',
        ] );

        if ( $partenaires->have_posts() ) :
            while ( $partenaires->have_posts() ) : $partenaires->the_post();
                $logo_id  = (int) get_post_meta( get_the_ID(), '_partenaire_logo_id', true );
                $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
                $lien     = get_post_meta( get_the_ID(), '_partenaire_lien', true );
                ?>
                <?php if ( $lien ) : ?><a href="<?php echo esc_url( $lien ); ?>" target="_blank" rel="noopener" class="partner-logo partner-logo--sm fade-in"><?php else : ?><div class="partner-logo partner-logo--sm fade-in"><?php endif; ?>
                    <?php if ( $logo_url ) : ?>
                        <img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_the_title() ); ?>" loading="lazy">
                    <?php else : ?>
                        <?php the_title(); ?>
                    <?php endif; ?>
                <?php echo $lien ? '</a>' : '</div>'; ?>
            <?php endwhile;
            wp_reset_postdata();
        else : ?>
            <p class="section-sub" style="font-style:italic">Les partenaires seront bientôt affichés ici.</p>
        <?php endif; ?>
      </div>

      <div class="partner-cta fade-in">
        <h3 class="partner-cta__title">Vous souhaitez devenir partenaire&nbsp;?</h3>
        <p class="partner-cta__desc">Gagnez en visibilité locale tout en soutenant une cause qui touche toutes les familles. Consultez notre dossier de partenariat.</p>
        <a href="<?php echo esc_url( home_url( '/documents/' ) ); ?>" class="btn btn-primary">Télécharger le dossier partenariat →</a>
      </div>

    </div>
  </div>

<?php get_footer(); ?>
