<?php
/**
 * Formulaires rapides — Actualité / Événement (Phase Admin 7).
 *
 * Pages d'admin "cachées" (jamais dans le menu, `add_submenu_page( null, ... )`)
 * accessibles uniquement via les cartes de TPLV → Contenu. Contenu en texte
 * simple (pas d'éditeur riche) : WordPress affiche automatiquement un
 * paragraphe par ligne vide (`wpautop`).
 *
 * Astuce de réutilisation : les champs des metabox existantes
 * (`inc/cpt-actualites.php`, `inc/cpt-evenements.php`) sont repris à
 * l'identique (même nonce, mêmes noms de champs). `wp_insert_post()`
 * déclenche donc automatiquement `save_post_actualite`/`save_post_evenement`,
 * qui enregistrent les métadonnées sans code dupliqué ici.
 *
 * L'écran WordPress complet (menu natif Actualités/Événements) reste
 * disponible pour la mise en forme riche ou la planification — rien n'est
 * retiré, ce formulaire n'est qu'un raccourci.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'admin_menu', 'tplv_register_formulaires_rapides' );
function tplv_register_formulaires_rapides(): void {
    add_submenu_page( null, 'Ajouter une actualité', '', 'edit_posts', 'tplv-actu-rapide', 'tplv_render_actu_rapide' );
    add_submenu_page( null, 'Ajouter un événement', '', 'edit_posts', 'tplv-event-rapide', 'tplv_render_event_rapide' );
}

/* ─────────────────────────────────────────────
 * 1. Formulaire Actualité rapide
 * ───────────────────────────────────────────── */

function tplv_render_actu_rapide(): void {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Action non autorisée.' );
    }
    $badges = tplv_actualite_badges();
    ?>
    <div class="wrap">
        <h1>Ajouter une actualité</h1>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="tplv_actu_rapide_submit">
            <?php wp_nonce_field( 'tplv_actualite_nonce_action', 'tplv_actualite_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th><label for="tplv_titre">Titre</label></th>
                    <td><input type="text" id="tplv_titre" name="tplv_titre" class="regular-text" required style="width:100%;max-width:500px;"></td>
                </tr>
                <tr>
                    <th><label for="tplv_apercu">Aperçu (affiché sur la carte)</label></th>
                    <td>
                        <textarea id="tplv_apercu" name="tplv_apercu" rows="2" style="width:100%;max-width:600px;"></textarea>
                        <p class="description">Un résumé court, écrit pour la carte affichée sur l'accueil et la liste des actualités. Laissez vide pour un résumé automatique du contenu.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tplv_contenu">Contenu</label></th>
                    <td>
                        <textarea id="tplv_contenu" name="tplv_contenu" rows="8" style="width:100%;max-width:600px;"></textarea>
                        <p class="description">Laissez une ligne vide entre deux paragraphes.</p>
                    </td>
                </tr>
                <tr>
                    <th>Image</th>
                    <td>
                        <input type="hidden" id="tplv_image_id" name="tplv_image_id" value="">
                        <div id="tplv_image_apercu" style="margin-bottom:8px; display:none;"></div>
                        <button type="button" class="button" id="tplv_choisir_image">Choisir une image</button>
                        <button type="button" class="button" id="tplv_retirer_image" style="display:none;">Retirer</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="tplv_badge">Badge / catégorie</label></th>
                    <td>
                        <select id="tplv_badge" name="tplv_badge">
                            <option value="">— Aucun —</option>
                            <?php foreach ( $badges as $b ) : ?>
                                <option value="<?php echo esc_attr( $b ); ?>"><?php echo esc_html( $b ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th><label for="tplv_video">Lien Facebook, Instagram ou YouTube</label></th>
                    <td><input type="url" id="tplv_video" name="tplv_video" class="regular-text" placeholder="https://..."></td>
                </tr>
                <tr>
                    <th>À la une</th>
                    <td><label><input type="checkbox" name="tplv_featured" value="1"> Mettre cette actualité à la une sur l'accueil</label></td>
                </tr>
            </table>

            <?php submit_button( "Publier l'actualité" ); ?>
        </form>
    </div>
    <?php tplv_render_media_picker_script( 'tplv_image_id', 'tplv_image_apercu', 'tplv_choisir_image', 'tplv_retirer_image' ); ?>
    <?php
}

add_action( 'admin_post_tplv_actu_rapide_submit', 'tplv_handle_actu_rapide_submit' );
function tplv_handle_actu_rapide_submit(): void {
    if ( ! isset( $_POST['tplv_actualite_nonce'] ) || ! wp_verify_nonce( $_POST['tplv_actualite_nonce'], 'tplv_actualite_nonce_action' ) ) {
        wp_die( 'Session expirée, veuillez réessayer.' );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Action non autorisée.' );
    }

    $titre = isset( $_POST['tplv_titre'] ) ? sanitize_text_field( wp_unslash( $_POST['tplv_titre'] ) ) : '';
    if ( '' === $titre ) {
        wp_die( 'Le titre est obligatoire. <a href="javascript:history.back()">Retour</a>' );
    }
    $contenu = isset( $_POST['tplv_contenu'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tplv_contenu'] ) ) : '';
    $apercu  = isset( $_POST['tplv_apercu'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tplv_apercu'] ) ) : '';

    $post_id = wp_insert_post( [
        'post_type'    => 'actualite',
        'post_title'   => $titre,
        'post_content' => $contenu,
        'post_excerpt' => $apercu,
        'post_status'  => 'publish',
    ] );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        wp_die( 'Une erreur est survenue lors de la publication.' );
    }

    $image_id = isset( $_POST['tplv_image_id'] ) ? absint( $_POST['tplv_image_id'] ) : 0;
    if ( $image_id ) {
        set_post_thumbnail( $post_id, $image_id );
    }

    wp_safe_redirect( admin_url( 'admin.php?page=tplv-contenu&tplv_success=actualite' ) );
    exit;
}

/* ─────────────────────────────────────────────
 * 2. Formulaire Événement rapide
 * ───────────────────────────────────────────── */

function tplv_render_event_rapide(): void {
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Action non autorisée.' );
    }
    $couleurs = [ 'default' => 'Navy → Magenta', 'sky' => 'Sky', 'magenta' => 'Magenta', 'green' => 'Vert' ];
    ?>
    <div class="wrap">
        <h1>Ajouter un événement</h1>
        <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
            <input type="hidden" name="action" value="tplv_event_rapide_submit">
            <?php wp_nonce_field( 'tplv_evenement_nonce_action', 'tplv_evenement_nonce' ); ?>

            <table class="form-table">
                <tr>
                    <th><label for="tplv_titre">Titre</label></th>
                    <td><input type="text" id="tplv_titre" name="tplv_titre" class="regular-text" required style="width:100%;max-width:500px;"></td>
                </tr>
                <tr>
                    <th><label for="tplv_apercu">Aperçu (affiché sur la carte)</label></th>
                    <td>
                        <textarea id="tplv_apercu" name="tplv_apercu" rows="2" style="width:100%;max-width:600px;"></textarea>
                        <p class="description">Un résumé court, écrit pour la carte affichée sur l'accueil et la liste des événements. Laissez vide pour un résumé automatique du contenu.</p>
                    </td>
                </tr>
                <tr>
                    <th><label for="tplv_contenu">Contenu</label></th>
                    <td>
                        <textarea id="tplv_contenu" name="tplv_contenu" rows="6" style="width:100%;max-width:600px;"></textarea>
                        <p class="description">Laissez une ligne vide entre deux paragraphes.</p>
                    </td>
                </tr>
                <tr>
                    <th>Image</th>
                    <td>
                        <input type="hidden" id="tplv_image_id" name="tplv_image_id" value="">
                        <div id="tplv_image_apercu" style="margin-bottom:8px; display:none;"></div>
                        <button type="button" class="button" id="tplv_choisir_image">Choisir une image</button>
                        <button type="button" class="button" id="tplv_retirer_image" style="display:none;">Retirer</button>
                    </td>
                </tr>
                <tr>
                    <th><label for="event_date">Date</label></th>
                    <td><input type="date" id="event_date" name="event_date" style="width:100%;max-width:220px;"></td>
                </tr>
                <tr>
                    <th><label for="event_lieu">Lieu</label></th>
                    <td><input type="text" id="event_lieu" name="event_lieu" class="regular-text" placeholder="ex. Janzé, Place de l'Église"></td>
                </tr>
                <tr>
                    <th><label for="event_horaires">Horaires</label></th>
                    <td><input type="text" id="event_horaires" name="event_horaires" class="regular-text" placeholder="ex. 09h00 – 17h00"></td>
                </tr>
                <tr>
                    <th><label for="event_tarif">Tarif</label></th>
                    <td><input type="text" id="event_tarif" name="event_tarif" class="regular-text" placeholder="ex. 15 € / personne"></td>
                </tr>
                <tr>
                    <th><label for="event_ha_url">Lien HelloAsso</label></th>
                    <td><input type="url" id="event_ha_url" name="event_ha_url" class="regular-text" placeholder="https://www.helloasso.com/..."></td>
                </tr>
                <tr>
                    <th><label for="event_couleur">Couleur de la carte</label></th>
                    <td>
                        <select id="event_couleur" name="event_couleur">
                            <?php foreach ( $couleurs as $val => $label ) : ?>
                                <option value="<?php echo esc_attr( $val ); ?>"><?php echo esc_html( $label ); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </td>
                </tr>
            </table>

            <?php submit_button( "Publier l'événement" ); ?>
        </form>
    </div>
    <?php tplv_render_media_picker_script( 'tplv_image_id', 'tplv_image_apercu', 'tplv_choisir_image', 'tplv_retirer_image' ); ?>
    <?php
}

add_action( 'admin_post_tplv_event_rapide_submit', 'tplv_handle_event_rapide_submit' );
function tplv_handle_event_rapide_submit(): void {
    if ( ! isset( $_POST['tplv_evenement_nonce'] ) || ! wp_verify_nonce( $_POST['tplv_evenement_nonce'], 'tplv_evenement_nonce_action' ) ) {
        wp_die( 'Session expirée, veuillez réessayer.' );
    }
    if ( ! current_user_can( 'edit_posts' ) ) {
        wp_die( 'Action non autorisée.' );
    }

    $titre = isset( $_POST['tplv_titre'] ) ? sanitize_text_field( wp_unslash( $_POST['tplv_titre'] ) ) : '';
    if ( '' === $titre ) {
        wp_die( 'Le titre est obligatoire. <a href="javascript:history.back()">Retour</a>' );
    }
    $contenu = isset( $_POST['tplv_contenu'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tplv_contenu'] ) ) : '';
    $apercu  = isset( $_POST['tplv_apercu'] ) ? sanitize_textarea_field( wp_unslash( $_POST['tplv_apercu'] ) ) : '';

    $post_id = wp_insert_post( [
        'post_type'    => 'evenement',
        'post_title'   => $titre,
        'post_content' => $contenu,
        'post_excerpt' => $apercu,
        'post_status'  => 'publish',
    ] );

    if ( is_wp_error( $post_id ) || ! $post_id ) {
        wp_die( 'Une erreur est survenue lors de la publication.' );
    }

    $image_id = isset( $_POST['tplv_image_id'] ) ? absint( $_POST['tplv_image_id'] ) : 0;
    if ( $image_id ) {
        set_post_thumbnail( $post_id, $image_id );
    }

    wp_safe_redirect( admin_url( 'admin.php?page=tplv-contenu&tplv_success=evenement' ) );
    exit;
}

/* ─────────────────────────────────────────────
 * 3. Sélecteur d'image (médiathèque WordPress) — partagé par les 2 formulaires
 * ───────────────────────────────────────────── */

function tplv_render_media_picker_script( string $input_id, string $apercu_id, string $btn_add_id, string $btn_del_id ): void {
    wp_enqueue_media();
    ?>
    <script>
    (function() {
        var frame;
        var input  = document.getElementById( '<?php echo esc_js( $input_id ); ?>' );
        var apercu = document.getElementById( '<?php echo esc_js( $apercu_id ); ?>' );
        var btnAdd = document.getElementById( '<?php echo esc_js( $btn_add_id ); ?>' );
        var btnDel = document.getElementById( '<?php echo esc_js( $btn_del_id ); ?>' );

        btnAdd.addEventListener( 'click', function( e ) {
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media( { title: 'Choisir une image', button: { text: 'Utiliser cette image' }, multiple: false, library: { type: 'image' } } );
            frame.on( 'select', function() {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                input.value = attachment.id;
                apercu.innerHTML = '<img src="' + url + '" style="max-height:120px;max-width:240px;object-fit:contain;border:1px solid #dcdcde;border-radius:4px;padding:8px;background:#fff;">';
                apercu.style.display = '';
                btnDel.style.display = '';
            } );
            frame.open();
        } );

        btnDel.addEventListener( 'click', function( e ) {
            e.preventDefault();
            input.value = '';
            apercu.style.display = 'none';
            btnDel.style.display = 'none';
        } );
    })();
    </script>
    <?php
}

/* ─────────────────────────────────────────────
 * 4. Message de succès sur la page "Contenu"
 * ───────────────────────────────────────────── */

add_action( 'admin_notices', 'tplv_notice_succes_rapide' );
function tplv_notice_succes_rapide(): void {
    if ( empty( $_GET['page'] ) || 'tplv-contenu' !== $_GET['page'] || empty( $_GET['tplv_success'] ) ) {
        return;
    }
    $type  = sanitize_text_field( wp_unslash( $_GET['tplv_success'] ) );
    $label = [ 'actualite' => 'Actualité publiée !', 'evenement' => 'Événement publié !' ][ $type ] ?? '';
    if ( ! $label ) {
        return;
    }
    printf( '<div class="notice notice-success is-dismissible"><p>%s</p></div>', esc_html( $label ) );
}
