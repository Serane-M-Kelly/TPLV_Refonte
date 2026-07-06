<?php get_header(); ?>

<?php
$post_type = get_query_var( 'post_type' );
$is_actualites = ( 'actualite' === $post_type );
$is_evenements  = ( 'evenement' === $post_type );
?>

<!-- EN-TÊTE DE PAGE -->
<div class="page-header<?php echo $is_evenements ? ' page-header--wave-surface' : ''; ?>">
    <div class="container">
        <?php if ( $is_actualites ) : ?>
            <span class="eyebrow">Vie de l'association</span>
            <h1>Actualités</h1>
            <p>Retrouvez toutes les nouvelles de l'association TPLV — éditions, APA, partenariats et vie de la communauté.</p>
        <?php elseif ( $is_evenements ) : ?>
            <span class="eyebrow">Agenda TPLV</span>
            <h1>Événements &amp; Inscriptions</h1>
            <p>Toutes les activités du week-end festif et sportif TPLV — inscriptions via HelloAsso.</p>
        <?php else : ?>
            <h1><?php the_archive_title(); ?></h1>
        <?php endif; ?>
    </div>
</div>

<!-- CONTENU -->
<div class="section<?php echo $is_evenements ? ' section-surface' : ''; ?>">
    <div class="container">

        <?php if ( have_posts() ) : ?>

            <div class="<?php echo $is_evenements ? 'events-grid' : 'articles-grid'; ?>">
                <?php while ( have_posts() ) : the_post(); ?>
                    <?php if ( $is_evenements ) : ?>
                        <?php get_template_part( 'template-parts/card-evenement' ); ?>
                    <?php else : ?>
                        <?php get_template_part( 'template-parts/card-actualite' ); ?>
                    <?php endif; ?>
                <?php endwhile; ?>
            </div>

            <!-- PAGINATION -->
            <?php if ( $is_actualites ) : ?>
                <div class="pagination">
                    <?php
                    echo paginate_links( [
                        'prev_text' => '← Précédent',
                        'next_text' => 'Suivant →',
                    ] );
                    ?>
                </div>
            <?php endif; ?>

        <?php else : ?>
            <p class="section-sub">Aucun contenu disponible pour le moment. Revenez bientôt !</p>
        <?php endif; ?>

    </div>
</div>

<?php get_footer(); ?>
