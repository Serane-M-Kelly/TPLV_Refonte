<?php
/**
 * Custom Post Type — Événements
 *
 * Champs personnalisés (post_meta) :
 *   _event_date          — date au format YYYY-MM-DD
 *   _event_lieu          — lieu (texte libre)
 *   _event_horaires      — horaires (ex. "09h00 – 17h00")
 *   _event_tarif         — tarif (ex. "15 € / personne")
 *   _event_lien_helloasso — URL HelloAsso
 *   _event_couleur       — variante de couleur : "default" | "sky" | "magenta" | "green"
 */

add_action( 'init', 'tplv_register_cpt_evenements' );
function tplv_register_cpt_evenements() {
    $labels = [
        'name'               => 'Événements',
        'singular_name'      => 'Événement',
        'menu_name'          => 'Événements',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter un événement',
        'edit_item'          => "Modifier l'événement",
        'new_item'           => 'Nouvel événement',
        'view_item'          => "Voir l'événement",
        'search_items'       => 'Rechercher',
        'not_found'          => 'Aucun événement trouvé',
        'not_found_in_trash' => 'Aucun événement dans la corbeille',
    ];

    register_post_type( 'evenement', [
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => 'evenements',
        'rewrite'            => [ 'slug' => 'evenements' ],
        'menu_icon'          => 'dashicons-calendar-alt',
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt' ],
        'show_in_menu'       => true,
        'menu_position'      => 6,
    ] );
}

// Enregistrement des champs meta
add_action( 'init', 'tplv_register_meta_evenements' );
function tplv_register_meta_evenements() {
    $metas = [
        '_event_date'           => 'string',
        '_event_lieu'           => 'string',
        '_event_horaires'       => 'string',
        '_event_tarif'          => 'string',
        '_event_lien_helloasso' => 'string',
        '_event_couleur'        => 'string',
    ];
    foreach ( $metas as $key => $type ) {
        register_post_meta( 'evenement', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => $type,
            'auth_callback' => '__return_true',
        ] );
    }
}

// Boîte meta
add_action( 'add_meta_boxes', 'tplv_add_metabox_evenement' );
function tplv_add_metabox_evenement() {
    add_meta_box(
        'tplv_evenement_meta',
        "Détails de l'événement",
        'tplv_render_metabox_evenement',
        'evenement',
        'normal',
        'high'
    );
}

function tplv_render_metabox_evenement( $post ) {
    wp_nonce_field( 'tplv_evenement_nonce_action', 'tplv_evenement_nonce' );
    $date     = get_post_meta( $post->ID, '_event_date',           true );
    $lieu     = get_post_meta( $post->ID, '_event_lieu',           true );
    $horaires = get_post_meta( $post->ID, '_event_horaires',       true );
    $tarif    = get_post_meta( $post->ID, '_event_tarif',          true );
    $ha_url   = get_post_meta( $post->ID, '_event_lien_helloasso', true );
    $couleur  = get_post_meta( $post->ID, '_event_couleur',        true ) ?: 'default';
    $couleurs = [ 'default' => 'Navy → Magenta', 'sky' => 'Sky', 'magenta' => 'Magenta', 'green' => 'Vert' ];
    ?>
    <table class="form-table" style="width:100%">
        <tr>
            <th><label for="event_date">Date</label></th>
            <td><input type="date" id="event_date" name="event_date" value="<?php echo esc_attr( $date ); ?>" style="width:100%"></td>
        </tr>
        <tr>
            <th><label for="event_lieu">Lieu</label></th>
            <td><input type="text" id="event_lieu" name="event_lieu" value="<?php echo esc_attr( $lieu ); ?>" placeholder="ex. Janzé, Place de l'Église" style="width:100%"></td>
        </tr>
        <tr>
            <th><label for="event_horaires">Horaires</label></th>
            <td><input type="text" id="event_horaires" name="event_horaires" value="<?php echo esc_attr( $horaires ); ?>" placeholder="ex. 09h00 – 17h00" style="width:100%"></td>
        </tr>
        <tr>
            <th><label for="event_tarif">Tarif</label></th>
            <td><input type="text" id="event_tarif" name="event_tarif" value="<?php echo esc_attr( $tarif ); ?>" placeholder="ex. 15 € / personne · 60 € / équipe" style="width:100%"></td>
        </tr>
        <tr>
            <th><label for="event_ha_url">Lien HelloAsso</label></th>
            <td><input type="url" id="event_ha_url" name="event_ha_url" value="<?php echo esc_url( $ha_url ); ?>" placeholder="https://www.helloasso.com/..." style="width:100%"></td>
        </tr>
        <tr>
            <th><label for="event_couleur">Couleur de la carte</label></th>
            <td>
                <select id="event_couleur" name="event_couleur" style="width:100%">
                    <?php foreach ( $couleurs as $val => $label ) : ?>
                        <option value="<?php echo esc_attr( $val ); ?>" <?php selected( $couleur, $val ); ?>><?php echo esc_html( $label ); ?></option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
    </table>
    <?php
}

add_action( 'save_post_evenement', 'tplv_save_meta_evenement' );
function tplv_save_meta_evenement( $post_id ) {
    if ( ! isset( $_POST['tplv_evenement_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['tplv_evenement_nonce'], 'tplv_evenement_nonce_action' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Champs texte libres
    $text_fields = [
        'event_date'     => '_event_date',
        'event_lieu'     => '_event_lieu',
        'event_horaires' => '_event_horaires',
        'event_tarif'    => '_event_tarif',
    ];
    foreach ( $text_fields as $input => $meta_key ) {
        if ( isset( $_POST[ $input ] ) ) {
            update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $input ] ) );
        }
    }
    // Couleur : valeur limitée à la liste autorisée (whitelist)
    if ( isset( $_POST['event_couleur'] ) ) {
        $allowed_couleurs = [ 'default', 'sky', 'magenta', 'green' ];
        $couleur = in_array( $_POST['event_couleur'], $allowed_couleurs, true )
            ? $_POST['event_couleur']
            : 'default';
        update_post_meta( $post_id, '_event_couleur', $couleur );
    }
    // URL HelloAsso
    if ( isset( $_POST['event_ha_url'] ) ) {
        update_post_meta( $post_id, '_event_lien_helloasso', esc_url_raw( $_POST['event_ha_url'] ) );
    }
}
