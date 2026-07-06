<?php
/**
 * Custom Post Type — Partenaires
 *
 * Champs personnalisés (post_meta) :
 *   _partenaire_logo_id — ID de la pièce jointe image (médiathèque WordPress)
 *   _partenaire_lien    — URL externe du partenaire (optionnel)
 *   _partenaire_type    — "partenaire" (défaut) ou "parrain" (liste blanche)
 *
 * Pas de page publique individuelle : les partenaires ne sont qu'une liste
 * de logos (page-partenaires.php), pas des pages à parcourir une par une.
 */

add_action( 'init', 'tplv_register_cpt_partenaires' );
function tplv_register_cpt_partenaires() {
    $labels = [
        'name'               => 'Partenaires',
        'singular_name'      => 'Partenaire',
        'menu_name'          => 'Partenaires',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter un partenaire',
        'edit_item'          => 'Modifier le partenaire',
        'new_item'           => 'Nouveau partenaire',
        'view_item'          => 'Voir le partenaire',
        'search_items'       => 'Rechercher',
        'not_found'          => 'Aucun partenaire trouvé',
        'not_found_in_trash' => 'Aucun partenaire dans la corbeille',
    ];

    register_post_type( 'partenaire', [
        'labels'              => $labels,
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => true,
        'show_in_rest'        => false,
        'exclude_from_search' => true,
        'has_archive'         => false,
        'rewrite'             => false,
        'query_var'           => false,
        'capability_type'     => 'post',
        'supports'            => [ 'title', 'page-attributes' ],
        'menu_icon'           => 'dashicons-groups',
        'menu_position'       => 8,
    ] );
}

/**
 * Liste blanche des types de partenaire.
 */
function tplv_partenaire_types(): array {
    return [
        'partenaire' => 'Partenaire',
        'parrain'    => 'Parrain / Marraine de l\'édition',
    ];
}

// Charge le sélecteur de médiathèque WordPress uniquement sur l'écran d'édition des partenaires.
add_action( 'admin_enqueue_scripts', 'tplv_enqueue_media_partenaires' );
function tplv_enqueue_media_partenaires( string $hook ): void {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }
    if ( 'partenaire' !== get_current_screen()->post_type ) {
        return;
    }
    wp_enqueue_media();
}

add_action( 'add_meta_boxes', 'tplv_add_metabox_partenaire' );
function tplv_add_metabox_partenaire() {
    add_meta_box(
        'tplv_partenaire_meta',
        'Détails du partenaire',
        'tplv_render_metabox_partenaire',
        'partenaire',
        'normal',
        'high'
    );
}

function tplv_render_metabox_partenaire( $post ) {
    wp_nonce_field( 'tplv_partenaire_nonce_action', 'tplv_partenaire_nonce' );

    $logo_id  = (int) get_post_meta( $post->ID, '_partenaire_logo_id', true );
    $logo_url = $logo_id ? wp_get_attachment_image_url( $logo_id, 'medium' ) : '';
    $lien     = get_post_meta( $post->ID, '_partenaire_lien', true );
    $type     = get_post_meta( $post->ID, '_partenaire_type', true ) ?: 'partenaire';
    $types    = tplv_partenaire_types();
    ?>
    <p>
        <label for="tplv_partenaire_type"><strong>Type</strong></label><br>
        <select id="tplv_partenaire_type" name="tplv_partenaire_type" style="margin-top:4px; min-width:280px;">
            <?php foreach ( $types as $val => $label ) : ?>
                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $type, $val ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
        <span class="description" style="display:block; margin-top:4px;">Les Parrains/Marraines apparaissent dans une section mise en avant, en tête de la page Partenaires.</span>
    </p>

    <p>
        <label for="tplv_partenaire_lien"><strong>Lien du site du partenaire</strong></label><br>
        <input type="url" id="tplv_partenaire_lien" name="tplv_partenaire_lien"
               value="<?php echo esc_attr( $lien ); ?>"
               placeholder="https://..."
               style="width:100%; max-width:420px; margin-top:4px;">
        <span class="description" style="display:block; margin-top:4px;">Laissez vide si le logo ne doit pas être cliquable.</span>
    </p>

    <p>
        <strong>Logo</strong><br>
        <span class="description" style="display:block; margin:4px 0 8px;">Sans logo, le nom du partenaire s'affiche en texte à la place.</span>

        <input type="hidden" id="tplv_partenaire_logo_id" name="tplv_partenaire_logo_id" value="<?php echo esc_attr( $logo_id ); ?>">
        <div id="tplv_partenaire_logo_apercu" style="margin:8px 0; <?php echo $logo_url ? '' : 'display:none;'; ?>">
            <img src="<?php echo esc_url( $logo_url ); ?>" style="max-height:80px; max-width:240px; object-fit:contain; border:1px solid #dcdcde; border-radius:4px; padding:8px; background:#fff;">
        </div>
        <button type="button" class="button" id="tplv_partenaire_choisir_logo">Choisir un logo</button>
        <button type="button" class="button" id="tplv_partenaire_retirer_logo" style="<?php echo $logo_id ? '' : 'display:none;'; ?>">Retirer le logo</button>
    </p>

    <script>
    (function() {
        var frame;
        var input   = document.getElementById( 'tplv_partenaire_logo_id' );
        var apercu  = document.getElementById( 'tplv_partenaire_logo_apercu' );
        var btnAdd  = document.getElementById( 'tplv_partenaire_choisir_logo' );
        var btnDel  = document.getElementById( 'tplv_partenaire_retirer_logo' );

        btnAdd.addEventListener( 'click', function( e ) {
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media( { title: 'Choisir un logo', button: { text: 'Utiliser ce logo' }, multiple: false, library: { type: 'image' } } );
            frame.on( 'select', function() {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                var url = attachment.sizes && attachment.sizes.medium ? attachment.sizes.medium.url : attachment.url;
                input.value = attachment.id;
                apercu.innerHTML = '<img src="' + url + '" style="max-height:80px;max-width:240px;object-fit:contain;border:1px solid #dcdcde;border-radius:4px;padding:8px;background:#fff;">';
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

add_action( 'save_post_partenaire', 'tplv_save_meta_partenaire' );
function tplv_save_meta_partenaire( $post_id ) {
    if ( ! isset( $_POST['tplv_partenaire_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['tplv_partenaire_nonce'], 'tplv_partenaire_nonce_action' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['tplv_partenaire_logo_id'] ) ) {
        update_post_meta( $post_id, '_partenaire_logo_id', absint( $_POST['tplv_partenaire_logo_id'] ) );
    }

    if ( isset( $_POST['tplv_partenaire_lien'] ) ) {
        update_post_meta( $post_id, '_partenaire_lien', esc_url_raw( $_POST['tplv_partenaire_lien'] ) );
    }

    if ( isset( $_POST['tplv_partenaire_type'] ) ) {
        $allowed = array_keys( tplv_partenaire_types() );
        $type    = in_array( $_POST['tplv_partenaire_type'], $allowed, true ) ? $_POST['tplv_partenaire_type'] : 'partenaire';
        update_post_meta( $post_id, '_partenaire_type', $type );
    }
}

/**
 * Contenu de démonstration — recrée les 14 partenaires affichés jusqu'ici en
 * dur, sans logo réel attaché (à remplacer par les vrais logos avant mise en
 * ligne). Ne s'exécute qu'une seule fois (option `tplv_partenaires_seeded`).
 */
add_action( 'init', 'tplv_seed_partenaires_demo', 20 );
function tplv_seed_partenaires_demo(): void {
    if ( get_option( 'tplv_partenaires_seeded' ) ) {
        return;
    }
    update_option( 'tplv_partenaires_seeded', '1' );

    $demo = [
        "Caisse d'Épargne Bretagne Pays de Loire",
        'Groupe Roullier',
        'MAIF',
        'Région Bretagne',
        'Département Ille-et-Vilaine',
        'Ville de Janzé',
        'Boulangerie Lelièvre',
        'Auto Boulanger',
        'Janzé Habitat',
        'Pharmacie de la Place',
        'Radio Janzé',
        'AS Janzé Football',
        'Partenaire à confirmer',
        'Imprimerie Grolleau',
    ];

    foreach ( $demo as $i => $nom ) {
        $post_id = wp_insert_post( [
            'post_type'   => 'partenaire',
            'post_title'  => $nom,
            'post_status' => 'publish',
            'menu_order'  => $i,
        ] );
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_partenaire_type', 'partenaire' );
        }
    }
}
