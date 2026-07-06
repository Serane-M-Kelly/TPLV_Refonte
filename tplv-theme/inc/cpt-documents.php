<?php
/**
 * Custom Post Type — Documents
 *
 * Champs personnalisés (post_meta) :
 *   _doc_fichier_id — ID de la pièce jointe (médiathèque WordPress)
 *   _doc_icone      — icône affichée (liste blanche, cohérente avec le design)
 *
 * Pas de page publique individuelle : les documents ne sont qu'une liste de
 * téléchargements (page-documents.php), pas des pages à parcourir une par une.
 */

add_action( 'init', 'tplv_register_cpt_documents' );
function tplv_register_cpt_documents() {
    $labels = [
        'name'               => 'Documents',
        'singular_name'      => 'Document',
        'menu_name'          => 'Documents',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter un document',
        'edit_item'          => 'Modifier le document',
        'new_item'           => 'Nouveau document',
        'view_item'          => 'Voir le document',
        'search_items'       => 'Rechercher',
        'not_found'          => 'Aucun document trouvé',
        'not_found_in_trash' => 'Aucun document dans la corbeille',
    ];

    register_post_type( 'document', [
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
        'menu_icon'           => 'dashicons-media-document',
        'menu_position'       => 7,
    ] );
}

/**
 * Liste blanche des icônes proposées dans le menu déroulant.
 */
function tplv_document_icones(): array {
    return [
        'file-text'      => 'Document générique',
        'clipboard-list' => 'Programme',
        'file-pen'       => 'Formulaire / bulletin',
        'handshake'      => 'Partenariat',
        'palette'        => 'Affiche / visuel',
        'bar-chart'      => 'Bilan / chiffres',
    ];
}

// Charge le sélecteur de médiathèque WordPress uniquement sur l'écran d'édition des documents.
add_action( 'admin_enqueue_scripts', 'tplv_enqueue_media_documents' );
function tplv_enqueue_media_documents( string $hook ): void {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }
    if ( 'document' !== get_current_screen()->post_type ) {
        return;
    }
    wp_enqueue_media();
}

add_action( 'add_meta_boxes', 'tplv_add_metabox_document' );
function tplv_add_metabox_document() {
    add_meta_box(
        'tplv_document_meta',
        'Fichier du document',
        'tplv_render_metabox_document',
        'document',
        'normal',
        'high'
    );
}

function tplv_render_metabox_document( $post ) {
    wp_nonce_field( 'tplv_document_nonce_action', 'tplv_document_nonce' );

    $fichier_id = (int) get_post_meta( $post->ID, '_doc_fichier_id', true );
    $fichier_nom = $fichier_id ? basename( get_attached_file( $fichier_id ) ) : '';
    $icone       = get_post_meta( $post->ID, '_doc_icone', true ) ?: 'file-text';
    $icones      = tplv_document_icones();
    ?>
    <p>
        <label for="tplv_doc_icone"><strong>Icône</strong></label><br>
        <select id="tplv_doc_icone" name="tplv_doc_icone" style="margin-top:4px; min-width:280px;">
            <?php foreach ( $icones as $val => $label ) : ?>
                <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $icone, $val ); ?>><?php echo esc_html( $label ); ?></option>
            <?php endforeach; ?>
        </select>
    </p>

    <p>
        <strong>Fichier PDF</strong><br>
        <span class="description" style="display:block; margin:4px 0 8px;">Le document n'apparaîtra avec un bouton "Télécharger" que si un fichier est attaché ici. Sans fichier, la page affiche "Bientôt disponible".</span>

        <input type="hidden" id="tplv_doc_fichier_id" name="tplv_doc_fichier_id" value="<?php echo esc_attr( $fichier_id ); ?>">
        <span id="tplv_doc_fichier_nom"><?php echo $fichier_nom ? esc_html( $fichier_nom ) : 'Aucun fichier sélectionné.'; ?></span>
        <br>
        <button type="button" class="button" id="tplv_doc_choisir_fichier" style="margin-top:8px;">Choisir un fichier</button>
        <button type="button" class="button" id="tplv_doc_retirer_fichier" style="margin-top:8px; <?php echo $fichier_id ? '' : 'display:none;'; ?>">Retirer le fichier</button>
    </p>

    <script>
    (function() {
        var frame;
        var input  = document.getElementById( 'tplv_doc_fichier_id' );
        var nom    = document.getElementById( 'tplv_doc_fichier_nom' );
        var btnAdd = document.getElementById( 'tplv_doc_choisir_fichier' );
        var btnDel = document.getElementById( 'tplv_doc_retirer_fichier' );

        btnAdd.addEventListener( 'click', function( e ) {
            e.preventDefault();
            if ( frame ) { frame.open(); return; }
            frame = wp.media( { title: 'Choisir un fichier', button: { text: 'Utiliser ce fichier' }, multiple: false } );
            frame.on( 'select', function() {
                var attachment = frame.state().get( 'selection' ).first().toJSON();
                input.value = attachment.id;
                nom.textContent = attachment.filename || attachment.title;
                btnDel.style.display = '';
            } );
            frame.open();
        } );

        btnDel.addEventListener( 'click', function( e ) {
            e.preventDefault();
            input.value = '';
            nom.textContent = 'Aucun fichier sélectionné.';
            btnDel.style.display = 'none';
        } );
    })();
    </script>
    <?php
}

add_action( 'save_post_document', 'tplv_save_meta_document' );
function tplv_save_meta_document( $post_id ) {
    if ( ! isset( $_POST['tplv_document_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['tplv_document_nonce'], 'tplv_document_nonce_action' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    if ( isset( $_POST['tplv_doc_fichier_id'] ) ) {
        update_post_meta( $post_id, '_doc_fichier_id', absint( $_POST['tplv_doc_fichier_id'] ) );
    }

    if ( isset( $_POST['tplv_doc_icone'] ) ) {
        $allowed = array_keys( tplv_document_icones() );
        $icone   = in_array( $_POST['tplv_doc_icone'], $allowed, true ) ? $_POST['tplv_doc_icone'] : 'file-text';
        update_post_meta( $post_id, '_doc_icone', $icone );
    }
}

/**
 * Contenu de démonstration — recrée les 6 documents affichés jusqu'ici en dur,
 * sans fichier réel attaché (à remplacer par les vrais PDF avant mise en ligne).
 * Ne s'exécute qu'une seule fois (option `tplv_documents_seeded`).
 */
add_action( 'init', 'tplv_seed_documents_demo', 20 );
function tplv_seed_documents_demo(): void {
    if ( get_option( 'tplv_documents_seeded' ) ) {
        return;
    }
    update_option( 'tplv_documents_seeded', '1' );

    $demo = [
        [ 'title' => 'Programme édition 2025',                    'icone' => 'clipboard-list' ],
        [ 'title' => "Bulletin d'inscription bénévole 2025",       'icone' => 'file-pen' ],
        [ 'title' => 'Dossier de partenariat 2025',                'icone' => 'handshake' ],
        [ 'title' => 'Affiche officielle TPLV 2025',               'icone' => 'palette' ],
        [ 'title' => 'Bilan financier 2024',                       'icone' => 'bar-chart' ],
        [ 'title' => "Statuts de l'association",                   'icone' => 'file-text' ],
    ];

    foreach ( $demo as $i => $doc ) {
        $post_id = wp_insert_post( [
            'post_type'   => 'document',
            'post_title'  => $doc['title'],
            'post_status' => 'publish',
            'menu_order'  => $i,
        ] );
        if ( $post_id && ! is_wp_error( $post_id ) ) {
            update_post_meta( $post_id, '_doc_icone', $doc['icone'] );
        }
    }
}
