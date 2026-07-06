<?php
/**
 * Custom Post Type — Actualités
 *
 * Champs personnalisés (post_meta) :
 *   _badge_actualite  — étiquette de catégorie (menu déroulant, valeur libre conservée si existante)
 *   _actu_video_url   — lien Facebook / Instagram / YouTube (optionnel)
 *   _actu_featured    — "1" si l'actualité est mise à la une sur l'accueil
 */

add_action( 'init', 'tplv_register_cpt_actualites' );
function tplv_register_cpt_actualites() {
    $labels = [
        'name'               => 'Actualités',
        'singular_name'      => 'Actualité',
        'menu_name'          => 'Actualités',
        'add_new'            => 'Ajouter',
        'add_new_item'       => 'Ajouter une actualité',
        'edit_item'          => "Modifier l'actualité",
        'new_item'           => 'Nouvelle actualité',
        'view_item'          => "Voir l'actualité",
        'search_items'       => 'Rechercher',
        'not_found'          => 'Aucune actualité trouvée',
        'not_found_in_trash' => 'Aucune actualité dans la corbeille',
    ];

    register_post_type( 'actualite', [
        'labels'             => $labels,
        'public'             => true,
        'show_in_rest'       => true,
        'has_archive'        => 'actualites',
        'rewrite'            => [ 'slug' => 'actualites' ],
        'menu_icon'          => 'dashicons-megaphone',
        'supports'           => [ 'title', 'editor', 'thumbnail', 'excerpt', 'author' ],
        'show_in_menu'       => true,
        'menu_position'      => 5,
    ] );
}

/**
 * Liste blanche des badges/catégories proposés dans le menu déroulant.
 */
function tplv_actualite_badges(): array {
    return [
        "Vie de l'association",
        "Édition annuelle",
        "APA",
        "Partenaires",
        "Presse / Médias",
        "Bénévoles",
        "Dons",
        "Autre",
    ];
}

// Enregistrement des champs meta de l'actualité
add_action( 'init', 'tplv_register_meta_actualites' );
function tplv_register_meta_actualites() {
    $metas = [ '_badge_actualite', '_actu_video_url', '_actu_featured' ];
    foreach ( $metas as $key ) {
        register_post_meta( 'actualite', $key, [
            'show_in_rest'  => true,
            'single'        => true,
            'type'          => 'string',
            'auth_callback' => '__return_true',
        ] );
    }
}

// Boîte meta dans l'éditeur classique
add_action( 'add_meta_boxes', 'tplv_add_metabox_actualite' );
function tplv_add_metabox_actualite() {
    add_meta_box(
        'tplv_actualite_meta',
        "Informations de l'actualité",
        'tplv_render_metabox_actualite',
        'actualite',
        'normal',
        'high'
    );
}

function tplv_render_metabox_actualite( $post ) {
    wp_nonce_field( 'tplv_actualite_nonce_action', 'tplv_actualite_nonce' );
    $badge    = get_post_meta( $post->ID, '_badge_actualite', true );
    $video    = get_post_meta( $post->ID, '_actu_video_url', true );
    $featured = get_post_meta( $post->ID, '_actu_featured', true );
    $badges   = tplv_actualite_badges();
    ?>
    <p style="margin-top:0; color:#646970;">Remplissez ces informations pour aider le site à afficher l'actualité correctement. Le titre, le contenu, l'image et la date se gèrent avec les champs WordPress habituels.</p>

    <p>
        <label for="tplv_badge"><strong>Badge / catégorie</strong></label><br>
        <select id="tplv_badge" name="tplv_badge" style="margin-top:4px; min-width:280px;">
            <option value="">— Aucun —</option>
            <?php
            $in_list = false;
            foreach ( $badges as $b ) {
                if ( $b === $badge ) {
                    $in_list = true;
                }
                printf(
                    '<option value="%s"%s>%s</option>',
                    esc_attr( $b ),
                    selected( $badge, $b, false ),
                    esc_html( $b )
                );
            }
            // Conserver une ancienne valeur hors liste (aucune perte de donnée).
            if ( $badge && ! $in_list ) {
                printf(
                    '<option value="%s" selected>%s (valeur existante)</option>',
                    esc_attr( $badge ),
                    esc_html( $badge )
                );
            }
            ?>
        </select>
    </p>

    <p>
        <label for="tplv_video"><strong>Lien Facebook, Instagram ou YouTube</strong></label><br>
        <input type="url" id="tplv_video" name="tplv_video"
               value="<?php echo esc_attr( $video ); ?>"
               placeholder="https://..."
               style="width:100%; max-width:520px; margin-top:4px;">
        <span class="description" style="display:block; margin-top:4px;">Collez ici le lien d'une publication Facebook, Instagram ou YouTube si l'actualité vient des réseaux sociaux. Laissez vide si ce n'est pas nécessaire.</span>
    </p>

    <p>
        <label for="tplv_featured">
            <input type="checkbox" id="tplv_featured" name="tplv_featured" value="1" <?php checked( $featured, '1' ); ?>>
            <strong>Mettre cette actualité à la une sur l'accueil</strong>
        </label>
    </p>
    <?php
}

add_action( 'save_post_actualite', 'tplv_save_meta_actualite' );
function tplv_save_meta_actualite( $post_id ) {
    if ( ! isset( $_POST['tplv_actualite_nonce'] ) ) return;
    if ( ! wp_verify_nonce( $_POST['tplv_actualite_nonce'], 'tplv_actualite_nonce_action' ) ) return;
    if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
    if ( ! current_user_can( 'edit_post', $post_id ) ) return;

    // Badge : on n'accepte que les valeurs de la liste blanche, MAIS on conserve
    // une ancienne valeur déjà enregistrée si elle est resoumise (pas de perte).
    if ( isset( $_POST['tplv_badge'] ) ) {
        $submitted = sanitize_text_field( wp_unslash( $_POST['tplv_badge'] ) );
        $allowed   = tplv_actualite_badges();
        $existing  = get_post_meta( $post_id, '_badge_actualite', true );
        if ( '' === $submitted || in_array( $submitted, $allowed, true ) || $submitted === $existing ) {
            update_post_meta( $post_id, '_badge_actualite', $submitted );
        }
        // Valeur inattendue (ni vide, ni liste, ni existante) : ignorée, l'existant reste intact.
    }

    // Lien social / vidéo (optionnel) — esc_url_raw à l'enregistrement.
    if ( isset( $_POST['tplv_video'] ) ) {
        update_post_meta( $post_id, '_actu_video_url', esc_url_raw( wp_unslash( $_POST['tplv_video'] ) ) );
    }

    // À la une : "1" si coché, "" sinon (permet de décocher).
    update_post_meta( $post_id, '_actu_featured', isset( $_POST['tplv_featured'] ) ? '1' : '' );
}

/**
 * Éditeur classique pour les Actualités uniquement.
 *
 * Interface plus simple et linéaire pour les bénévoles : titre, contenu, puis le
 * bloc "Informations de l'actualité" directement visible en dessous — sans le
 * tiroir "Boîtes méta" de l'éditeur de blocs. N'affecte que le type "actualite"
 * (ni les Articles, ni les Pages, ni Gutenberg ailleurs). 100 % natif WordPress.
 */
add_filter( 'use_block_editor_for_post_type', 'tplv_actualite_classic_editor', 10, 2 );
function tplv_actualite_classic_editor( $use_block_editor, $post_type ) {
    return ( 'actualite' === $post_type ) ? false : $use_block_editor;
}
