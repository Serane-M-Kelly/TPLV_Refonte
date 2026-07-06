<?php get_header(); ?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header">
    <div class="container">
      <span class="eyebrow">Ressources</span>
      <h1>Documents &amp; Téléchargements</h1>
      <p>Retrouvez tous les documents officiels de l'association TPLV — programmes, bilans et dossiers.</p>
    </div>
  </div>

  <!-- LISTE DE DOCUMENTS — WP_Query CPT Documents -->
  <div class="section">
    <div class="container">
      <div class="docs-list-wrap">
        <div class="docs-list">
          <?php
          $documents = new WP_Query( [
              'post_type'      => 'document',
              'posts_per_page' => -1,
              'orderby'        => 'menu_order',
              'order'          => 'ASC',
          ] );

          if ( $documents->have_posts() ) :
              while ( $documents->have_posts() ) : $documents->the_post();
                  $fichier_id = (int) get_post_meta( get_the_ID(), '_doc_fichier_id', true );
                  $fichier_url = $fichier_id ? wp_get_attachment_url( $fichier_id ) : '';
                  $icone       = get_post_meta( get_the_ID(), '_doc_icone', true ) ?: 'file-text';
                  $taille      = '';
                  if ( $fichier_id ) {
                      $chemin = get_attached_file( $fichier_id );
                      if ( $chemin && file_exists( $chemin ) ) {
                          $taille = size_format( filesize( $chemin ) );
                      }
                  }
                  ?>
                  <div class="doc-item fade-in">
                      <div class="doc-icon"><i data-lucide="<?php echo esc_attr( $icone ); ?>"></i></div>
                      <div class="doc-info">
                          <h3><?php the_title(); ?></h3>
                          <?php if ( $fichier_id ) : ?>
                              <span>PDF<?php echo $taille ? ' · ' . esc_html( $taille ) : ''; ?> · Mis à jour le <?php echo esc_html( get_the_modified_date( 'j F Y' ) ); ?></span>
                          <?php endif; ?>
                      </div>
                      <div class="doc-btn">
                          <?php if ( $fichier_url ) : ?>
                              <a href="<?php echo esc_url( $fichier_url ); ?>" class="btn btn-outline" target="_blank" rel="noopener">Télécharger</a>
                          <?php else : ?>
                              <span class="btn btn-outline" style="opacity:.5" title="Fichier non disponible pour le moment">Bientôt disponible</span>
                          <?php endif; ?>
                      </div>
                  </div>
              <?php endwhile;
              wp_reset_postdata();
          else : ?>
              <p class="section-sub" style="font-style:italic">Les documents seront bientôt disponibles.</p>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
