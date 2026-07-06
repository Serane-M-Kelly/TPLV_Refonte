<?php get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

<!-- EN-TÊTE DE PAGE -->
<div class="page-header">
    <div class="container">
        <span class="eyebrow">
            <?php
            $pt = get_post_type();
            if ( 'actualite' === $pt )  echo 'Actualité';
            elseif ( 'evenement' === $pt ) echo 'Événement';
            else echo get_post_type_object( $pt )->labels->singular_name;
            ?>
        </span>
        <h1><?php the_title(); ?></h1>
        <p class="article-meta"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></p>
    </div>
</div>

<!-- CONTENU -->
<div class="section">
    <div class="container" style="max-width:780px">

        <?php if ( has_post_thumbnail() ) : ?>
            <img src="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>"
                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                 loading="lazy" width="1200" height="630"
                 style="width:100%;height:auto;border-radius:var(--radius-lg);margin-bottom:2.5rem;">
        <?php endif; ?>

        <div class="entry-content" style="line-height:1.8;font-size:1.05rem">
            <?php the_content(); ?>
        </div>

        <!-- RETOUR -->
        <div style="margin-top:3rem">
            <?php
            $pt = get_post_type();
            if ( 'actualite' === $pt ) :
                $back_url   = esc_url( home_url( '/actualites/' ) );
                $back_label = '← Retour aux actualités';
            elseif ( 'evenement' === $pt ) :
                $back_url   = esc_url( home_url( '/evenements/' ) );
                $back_label = '← Retour aux événements';
            else :
                $back_url   = esc_url( home_url( '/' ) );
                $back_label = "← Retour à l'accueil";
            endif;
            ?>
            <a href="<?php echo esc_url( $back_url ); ?>" class="btn btn-outline"><?php echo esc_html( $back_label ); ?></a>
        </div>

    </div>
</div>

<?php endwhile; ?>

<?php get_footer(); ?>
