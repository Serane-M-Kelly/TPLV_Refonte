<?php
/**
 * Page "Formulaires" (Phase Admin 8).
 *
 * Tableau de suivi des 3 formulaires CF7 du site (Contact, Bénévoles, APA) :
 * statut d'installation du plugin, statut de création de chaque formulaire,
 * lien direct vers sa gestion dans CF7. Aucune donnée sensible affichée —
 * uniquement des informations de configuration.
 *
 * Enregistrée depuis inc/admin-tplv.php (même pattern que Réglages TPLV).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function tplv_render_formulaires_page(): void {
    $formulaires = [
        [ 'titre' => 'Contact TPLV',   'page' => '/contact/',   'desc' => 'Formulaire de contact général' ],
        [ 'titre' => 'Bénévoles TPLV', 'page' => '/benevoles/', 'desc' => 'Inscription bénévole' ],
        [ 'titre' => 'APA TPLV',       'page' => '/apa/',       'desc' => 'Inscription activité physique adaptée — RGPD santé, consentement uniquement' ],
    ];

    $cf7_actif = function_exists( 'wpcf7_contact_form' );
    ?>
    <div class="wrap tplv-admin">
        <h1>Formulaires du site</h1>
        <p class="tplv-welcome">Suivi des 3 formulaires du site, gérés par le plugin Contact Form 7.</p>

        <?php if ( ! $cf7_actif ) : ?>
            <div class="notice notice-warning inline"><p>⚠️ Le plugin <strong>Contact Form 7</strong> n'est pas encore installé. Les formulaires ci-dessous ne fonctionneront qu'une fois le plugin activé.</p></div>
        <?php endif; ?>

        <table class="widefat striped" style="max-width:900px; margin-top:16px;">
            <thead>
                <tr>
                    <th>Formulaire</th>
                    <th>Page publique</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ( $formulaires as $f ) :
                    $form_id = $cf7_actif ? tplv_cf7_form_id( $f['titre'] ) : 0;
                    ?>
                    <tr>
                        <td>
                            <strong><?php echo esc_html( $f['titre'] ); ?></strong><br>
                            <span class="description"><?php echo esc_html( $f['desc'] ); ?></span>
                        </td>
                        <td><a href="<?php echo esc_url( home_url( $f['page'] ) ); ?>" target="_blank" rel="noopener">Voir la page →</a></td>
                        <td>
                            <?php if ( ! $cf7_actif ) : ?>
                                <span style="color:#b32d2e;">Plugin non installé</span>
                            <?php elseif ( $form_id ) : ?>
                                <span style="color:#00a32a;">✓ Créé</span>
                            <?php else : ?>
                                <span style="color:#b32d2e;">Non créé</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ( $form_id ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcf7&post=' . $form_id . '&action=edit' ) ); ?>" class="button button-small">Gérer ce formulaire</a>
                            <?php elseif ( $cf7_actif ) : ?>
                                <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpcf7-new' ) ); ?>" class="button button-small">Créer "<?php echo esc_attr( $f['titre'] ); ?>"</a>
                            <?php else : ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
