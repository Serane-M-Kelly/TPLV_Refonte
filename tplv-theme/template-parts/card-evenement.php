<?php
/**
 * Template Part — Card Événement
 *
 * Usage :
 *   get_template_part( 'template-parts/card-evenement' );
 *
 * Doit être appelé à l'intérieur d'une boucle WP_Query sur le CPT 'evenement'.
 */

$date_raw = get_post_meta( get_the_ID(), '_event_date',           true );
$lieu     = get_post_meta( get_the_ID(), '_event_lieu',           true );
$horaires = get_post_meta( get_the_ID(), '_event_horaires',       true );
$tarif    = get_post_meta( get_the_ID(), '_event_tarif',          true );
$ha_url   = get_post_meta( get_the_ID(), '_event_lien_helloasso', true );
$couleur  = get_post_meta( get_the_ID(), '_event_couleur',        true ) ?: 'default';

// Formatage de la date (YYYY-MM-DD → "14 Juin 2025")
$date_obj  = $date_raw ? date_create( $date_raw ) : null;
$day       = $date_obj ? date_format( $date_obj, 'j' )    : '—';
$month_yr  = $date_obj ? date_format( $date_obj, 'F Y' )  : '';

// Classe CSS de la variante de couleur
$img_class = 'event-card-img';
if ( $couleur && 'default' !== $couleur ) {
    $img_class .= ' event-card-img--' . esc_attr( $couleur );
}

$img = get_the_post_thumbnail_url( get_the_ID(), 'large' );
?>

<div class="event-card fade-in">

    <div class="<?php echo esc_attr( $img_class ); ?>">
        <?php if ( $img ) : ?>
            <img src="<?php echo esc_url( $img ); ?>"
                 alt="<?php echo esc_attr( get_the_title() ); ?>"
                 loading="lazy" width="800" height="400"
                 style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;opacity:.25;">
        <?php endif; ?>
        <div class="event-date-day"><?php echo esc_html( $day ); ?></div>
        <div class="event-date-month"><?php echo esc_html( $month_yr ); ?></div>
    </div>

    <div class="event-card-body">
        <h3><?php the_title(); ?></h3>

        <?php if ( $lieu || $horaires ) : ?>
            <div class="event-meta">
                <?php if ( $lieu ) : ?>
                    <span><i data-lucide="map-pin"></i> <?php echo esc_html( $lieu ); ?></span>
                <?php endif; ?>
                <?php if ( $horaires ) : ?>
                    <span>⏰ <?php echo esc_html( $horaires ); ?></span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <p><?php the_content(); ?></p>

        <?php if ( $tarif ) : ?>
            <div class="event-price"><?php echo esc_html( 'Tarif : ' . $tarif ); ?></div>
        <?php endif; ?>

        <?php if ( $ha_url ) : ?>
            <a href="<?php echo esc_url( $ha_url ); ?>" class="btn btn-primary"
               target="_blank" rel="noopener noreferrer">
                S'inscrire → HelloAsso
            </a>
            <p class="rgpd-notice">↗ Vous serez redirigé vers HelloAsso (site externe) pour finaliser votre inscription.</p>
        <?php else : ?>
            <a href="<?php the_permalink(); ?>" class="btn btn-primary">En savoir plus →</a>
        <?php endif; ?>
    </div>

</div>
