<?php
/**
 * Template Part — Card Actualité
 *
 * Usage :
 *   get_template_part( 'template-parts/card-actualite' );
 *
 * Doit être appelé à l'intérieur d'une boucle WP_Query sur le CPT 'actualite'.
 */

$badge = get_post_meta( get_the_ID(), '_badge_actualite', true );
$video = get_post_meta( get_the_ID(), '_actu_video_url', true );
$img   = get_the_post_thumbnail_url( get_the_ID(), 'large' );

// Embed natif WordPress (YouTube, Facebook, Instagram…) — false si non supporté.
$embed = $video ? wp_oembed_get( $video ) : false;
?>

<div class="article-card fade-in">

    <?php if ( $embed ) : ?>
        <div class="article-card-img article-card-embed"><?php echo $embed; // oEmbed généré par WordPress ?></div>
    <?php elseif ( $img ) : ?>
        <img class="article-card-img" src="<?php echo esc_url( $img ); ?>"
             alt="<?php echo esc_attr( get_the_title() ); ?>"
             loading="lazy" width="1200" height="630">
    <?php else : ?>
        <div class="article-card-img" aria-hidden="true">Photo — 1200×630 px</div>
    <?php endif; ?>

    <div class="article-card-body">
        <?php if ( $badge ) : ?>
            <span class="article-badge"><?php echo esc_html( $badge ); ?></span>
        <?php endif; ?>

        <p class="article-meta"><?php echo esc_html( get_the_date( 'j F Y' ) ); ?></p>
        <h3><?php the_title(); ?></h3>
        <p><?php echo esc_html( get_the_excerpt() ); ?></p>

        <?php if ( $video && ! $embed ) : ?>
            <a href="<?php echo esc_url( $video ); ?>" class="article-link" target="_blank" rel="noopener">Voir la publication <span>→</span></a>
        <?php endif; ?>

        <a href="<?php the_permalink(); ?>" class="article-link">Lire la suite <span>→</span></a>
    </div>

</div>
