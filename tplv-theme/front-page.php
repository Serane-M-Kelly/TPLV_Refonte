<?php get_header(); ?>

<section id="accueil">

  <!-- HERO -->
  <div class="hero">
    <div class="container">
      <div class="hero-content fade-in">
        <div class="hero-sub">Ensemble contre le cancer</div>
        <h1>Tous Pour La <em>Vie</em> Janzé</h1>
        <p class="hero-tagline"><?php echo esc_html( tplv_opt( 'accroche', "Premier week-end d'octobre depuis 1991 — Janzé, Ille-et-Vilaine" ) ); ?></p>
        <div class="hero-ctas">
          <a href="#missions" class="btn btn-ghost-white">Découvrir nos actions</a>
          <a href="<?php echo esc_url( home_url( '/evenements/' ) ); ?>" class="btn btn-primary">S'inscrire →</a>
        </div>
      </div>
    </div>
  </div>

  <!-- MISSIONS -->
  <div id="missions" class="section section-surface">
    <div class="container">
      <div class="section-header section-header--center fade-in">
        <span class="eyebrow eyebrow--magenta">Notre raison d'être</span>
        <h2 class="section-title">Ce que nous portons, ensemble</h2>
        <p class="section-sub section-sub--center">Structurée en association depuis 2004 et héritière d'une mobilisation locale engagée depuis les années 1990, TPLV rassemble Janzé et ses environs autour d'une conviction simple : le sport, la solidarité et la communauté sont nos meilleures réponses au cancer.</p>
      </div>
      <div class="missions-grid">
        <div class="mission-card fade-in">
          <div class="mission-icon"><i data-lucide="heart"></i></div>
          <h3>Solidarité</h3>
          <p>Chaque euro collecté est reversé à la recherche médicale — Institut Curie, Centre Eugène-Marquis de Rennes et CHU de Rennes — pour accélérer les traitements contre le cancer.</p>
        </div>
        <div class="mission-card fade-in">
          <div class="mission-icon"><i data-lucide="activity"></i></div>
          <h3>Sport &amp; Bien-être</h3>
          <p>Randonnées, relais marathon, activités physiques adaptées : nous croyons que bouger ensemble, c'est déjà soigner. Le sport est notre terrain de solidarité.</p>
        </div>
        <div class="mission-card fade-in">
          <div class="mission-icon"><i data-lucide="handshake"></i></div>
          <h3>Communauté</h3>
          <p><?php echo esc_html( tplv_opt( 'nb_communes', 33 ) ); ?> communes mobilisées, <?php echo esc_html( tplv_opt( 'nb_benevoles', 500 ) ); ?> bénévoles et une communauté qui grandit chaque année. TPLV, c'est Janzé qui dit non au cancer d'une seule voix.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- CHIFFRES CLÉS -->
  <div class="section section-navy">
    <div class="container">
      <div class="section-header section-header--center fade-in">
        <span class="eyebrow">En chiffres</span>
        <h2 class="section-title section-title-white">20 ans d'engagement</h2>
        <p class="section-sub section-sub-white section-sub--center">Ces chiffres sont le reflet d'une mobilisation collective hors du commun, année après année.</p>
      </div>
      <div class="stats-grid">
        <div class="stat-item fade-in">
          <div class="stat-number" data-target="<?php echo esc_attr( tplv_opt( 'nb_benevoles', 500 ) ); ?>">0</div>
          <div class="stat-label">Bénévoles mobilisés</div>
        </div>
        <div class="stat-item fade-in">
          <div class="stat-number" data-target="<?php echo esc_attr( tplv_opt( 'nb_communes', 33 ) ); ?>">0</div>
          <div class="stat-label">Communes partenaires</div>
        </div>
        <div class="stat-item fade-in">
          <div class="stat-number" data-target="<?php echo esc_attr( tplv_opt( 'nb_participants', 120000 ) ); ?>" data-suffix="+">0</div>
          <div class="stat-label">Participants au total</div>
        </div>
        <div class="stat-item fade-in">
          <div class="stat-number" data-target="<?php echo esc_attr( tplv_opt( 'total_redistribue', 800000 ) ); ?>" data-prefix="+" data-suffix="€">0</div>
          <div class="stat-label">Reversés à la recherche</div>
        </div>
      </div>
    </div>
  </div>

  <!-- DERNIÈRES ACTUALITÉS — WP_Query CPT Actualités -->
  <div class="section">
    <div class="container">
      <div class="section-header section-header--split fade-in">
        <div>
          <span class="eyebrow eyebrow--magenta">Vie de l'association</span>
          <h2 class="section-title">Dernières actualités</h2>
        </div>
        <a href="<?php echo esc_url( home_url( '/actualites/' ) ); ?>" class="btn btn-outline">Toutes les actualités →</a>
      </div>
      <div class="articles-grid">
        <?php
        $actu_limit = 3;

        // 1. Actualités mises « à la une » (les plus récentes d'abord).
        $actu_ids = get_posts( [
            'post_type'      => 'actualite',
            'posts_per_page' => $actu_limit,
            'meta_key'       => '_actu_featured',
            'meta_value'     => '1',
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ] );

        // 2. Compléter avec les plus récentes (hors doublons) jusqu'à 3 max.
        if ( count( $actu_ids ) < $actu_limit ) {
            $fill = get_posts( [
                'post_type'      => 'actualite',
                'posts_per_page' => $actu_limit - count( $actu_ids ),
                'post__not_in'   => ! empty( $actu_ids ) ? $actu_ids : [ 0 ],
                'orderby'        => 'date',
                'order'          => 'DESC',
                'fields'         => 'ids',
            ] );
            $actu_ids = array_merge( $actu_ids, $fill );
        }

        if ( ! empty( $actu_ids ) ) :
            $actu_query = new WP_Query( [
                'post_type'      => 'actualite',
                'post__in'       => $actu_ids,
                'orderby'        => 'post__in',
                'posts_per_page' => $actu_limit,
            ] );
            while ( $actu_query->have_posts() ) : $actu_query->the_post();
                get_template_part( 'template-parts/card-actualite' );
            endwhile;
            wp_reset_postdata();
        else : ?>
            <p class="section-sub" style="grid-column:1/-1">Les actualités arrivent bientôt !</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- HISTOIRE / À PROPOS -->
  <div class="section section-surface">
    <div class="container">
      <div class="cta-join fade-in">
        <span class="eyebrow eyebrow--magenta">Fondée en 2004</span>
        <h2>Découvrez notre histoire</h2>
        <p>Vingt ans de solidarité et d'engagement humain — <?php echo esc_html( number_format( (int) tplv_opt( 'total_redistribue', 800000 ), 0, '', ' ' ) ); ?> € reversés, <?php echo esc_html( tplv_opt( 'nb_benevoles', 500 ) ); ?> bénévoles, <?php echo esc_html( tplv_opt( 'nb_communes', 33 ) ); ?> communes mobilisées. Découvrez l'aventure humaine derrière TPLV.</p>
        <div class="cta-join-btns">
          <a href="<?php echo esc_url( home_url( '/histoire/' ) ); ?>" class="btn btn-primary">Notre histoire →</a>
          <a href="<?php echo esc_url( home_url( '/resultats/' ) ); ?>" class="btn btn-secondary">Résultats des éditions</a>
        </div>
      </div>
    </div>
  </div>

  <!-- CTA REJOIGNEZ-NOUS -->
  <div class="section section-sky">
    <div class="container">
      <div class="cta-join fade-in">
        <span class="eyebrow eyebrow--magenta">Ensemble, on va plus loin</span>
        <h2>Rejoignez l'aventure !</h2>
        <p>Que vous souhaitiez donner de votre temps, soutenir financièrement ou simplement participer — TPLV a une place pour vous.</p>
        <div class="cta-join-btns">
          <a href="<?php echo esc_url( home_url( '/benevoles/' ) ); ?>" class="btn btn-secondary">Devenir bénévole</a>
          <a href="<?php echo esc_url( home_url( '/dons/' ) ); ?>" class="btn btn-primary"><i data-lucide="heart"></i> Faire un don</a>
        </div>
      </div>
    </div>
  </div>

</section>

<?php get_footer(); ?>
