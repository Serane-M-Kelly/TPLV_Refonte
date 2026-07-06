<?php
/**
 * Réglages TPLV — page d'options + helper de lecture.
 *
 * - Stockage : une seule option `tplv_settings` (tableau associatif).
 * - Accès : administrateurs uniquement (manage_options).
 * - Aucune valeur n'est lue par le front à ce stade (câblage ultérieur).
 *
 * Le helper tplv_opt() renvoie un fallback quand le champ est vide, afin que
 * le câblage futur des templates ne casse jamais l'affichage public.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─────────────────────────────────────────────
 * 1. Helper de lecture avec fallback
 * ───────────────────────────────────────────── */

/**
 * Récupère une valeur de réglage TPLV, ou le fallback si elle est vide/absente.
 *
 * @param string $key      Clé du réglage (ex. "email").
 * @param mixed  $fallback Valeur de repli si le réglage est vide.
 * @return mixed
 */
function tplv_opt( string $key, $fallback = '' ) {
    $opts = get_option( 'tplv_settings', [] );
    if ( ! is_array( $opts ) ) {
        $opts = [];
    }
    $val = $opts[ $key ] ?? '';
    if ( is_string( $val ) ) {
        $val = trim( $val );
    }
    return ( '' === $val || null === $val ) ? $fallback : $val;
}

/* ─────────────────────────────────────────────
 * 2. Schéma des réglages (libellé + aide + type), groupé par section
 *    Sert à la fois au rendu des champs et à la sanitization.
 * ───────────────────────────────────────────── */

function tplv_settings_schema(): array {
    return [
        'accueil' => [
            'titre'  => "Page d'accueil",
            'fields' => [
                'accroche'       => [ 'label' => "Phrase d'accroche de l'accueil", 'type' => 'text', 'help' => "Le slogan affiché en haut de la page d'accueil." ],
                'date_evenement' => [ 'label' => "Date du prochain événement principal", 'type' => 'date', 'help' => "Utilisée plus tard pour le décompte avant l'événement." ],
            ],
        ],
        'chiffres' => [
            'titre'  => "Chiffres clés",
            'fields' => [
                'total_redistribue' => [ 'label' => "Montant total redistribué (€)", 'type' => 'number', 'help' => "Montant total reversé à la recherche depuis la création." ],
                'nb_benevoles'      => [ 'label' => "Nombre de bénévoles", 'type' => 'number', 'help' => "Nombre de bénévoles mobilisés." ],
                'nb_participants'   => [ 'label' => "Participants cumulés", 'type' => 'number', 'help' => "Nombre total de participants depuis la création." ],
                'nb_communes'       => [ 'label' => "Communes impliquées", 'type' => 'number', 'help' => "Nombre de communes partenaires." ],
                'derniere_montant'  => [ 'label' => "Montant de la dernière édition (€)", 'type' => 'number', 'help' => "Montant collecté lors de la dernière édition." ],
                'derniere_annee'    => [ 'label' => "Année de la dernière édition", 'type' => 'number', 'help' => "Ex. 2025." ],
            ],
        ],
        'coordonnees' => [
            'titre'  => "Coordonnées",
            'fields' => [
                'email'     => [ 'label' => "Adresse email", 'type' => 'email', 'help' => "Adresse email de contact de l'association." ],
                'telephone' => [ 'label' => "Téléphone", 'type' => 'text', 'help' => "Numéro de téléphone affiché sur le site." ],
                'adresse'   => [ 'label' => "Adresse postale", 'type' => 'textarea', 'help' => "Adresse complète du siège de l'association." ],
            ],
        ],
        'dons' => [
            'titre'  => "Dons & réseaux sociaux",
            'fields' => [
                'helloasso'     => [ 'label' => "Lien HelloAsso", 'type' => 'url', 'help' => "Collez ici le lien complet de la page de don HelloAsso." ],
                'facebook'      => [ 'label' => "Lien Facebook", 'type' => 'url', 'help' => "Adresse de la page Facebook." ],
                'instagram'     => [ 'label' => "Lien Instagram", 'type' => 'url', 'help' => "Adresse du compte Instagram." ],
                'beneficiaires' => [ 'label' => "Bénéficiaires des dons", 'type' => 'text', 'help' => "Ex. Institut Curie, Centre Eugène-Marquis de Rennes, CHU de Rennes." ],
            ],
        ],
    ];
}

/* ─────────────────────────────────────────────
 * 3. Enregistrement Settings API (sections + champs)
 * ───────────────────────────────────────────── */

add_action( 'admin_init', 'tplv_register_settings' );
function tplv_register_settings(): void {
    register_setting( 'tplv_settings_group', 'tplv_settings', [
        'type'              => 'array',
        'sanitize_callback' => 'tplv_sanitize_settings',
        'default'           => [],
    ] );

    foreach ( tplv_settings_schema() as $section_id => $section ) {
        add_settings_section(
            'tplv_section_' . $section_id,
            esc_html( $section['titre'] ),
            '__return_false',
            'tplv-reglages'
        );
        foreach ( $section['fields'] as $key => $field ) {
            add_settings_field(
                'tplv_field_' . $key,
                esc_html( $field['label'] ),
                'tplv_field_render',
                'tplv-reglages',
                'tplv_section_' . $section_id,
                [
                    'key'       => $key,
                    'type'      => $field['type'],
                    'help'      => $field['help'],
                    'label_for' => 'tplv_field_' . $key,
                ]
            );
        }
    }
}

/**
 * Rendu d'un champ de réglage (la valeur affichée est la valeur stockée brute,
 * sans fallback : un champ vide doit rester vide dans le formulaire).
 */
function tplv_field_render( array $args ): void {
    $opts = get_option( 'tplv_settings', [] );
    $key  = $args['key'];
    $val  = ( is_array( $opts ) && isset( $opts[ $key ] ) ) ? $opts[ $key ] : '';
    $id   = 'tplv_field_' . $key;
    $name = 'tplv_settings[' . $key . ']';
    $type = $args['type'];

    if ( 'textarea' === $type ) {
        printf(
            '<textarea id="%s" name="%s" rows="3" class="large-text">%s</textarea>',
            esc_attr( $id ),
            esc_attr( $name ),
            esc_textarea( $val )
        );
    } else {
        $input_type = in_array( $type, [ 'email', 'url', 'number', 'date' ], true ) ? $type : 'text';
        printf(
            '<input type="%s" id="%s" name="%s" value="%s" class="regular-text">',
            esc_attr( $input_type ),
            esc_attr( $id ),
            esc_attr( $name ),
            esc_attr( $val )
        );
    }

    if ( ! empty( $args['help'] ) ) {
        printf( '<p class="description">%s</p>', esc_html( $args['help'] ) );
    }
}

/* ─────────────────────────────────────────────
 * 4. Sanitization (selon le type de chaque champ)
 * ───────────────────────────────────────────── */

function tplv_sanitize_settings( $input ): array {
    $input = is_array( $input ) ? $input : [];
    $out   = [];

    $absint_keys = [ 'total_redistribue', 'nb_benevoles', 'nb_participants', 'nb_communes', 'derniere_montant', 'derniere_annee' ];
    $url_keys    = [ 'helloasso', 'facebook', 'instagram' ];

    foreach ( tplv_settings_schema() as $section ) {
        foreach ( $section['fields'] as $key => $field ) {
            $raw = $input[ $key ] ?? '';
            if ( is_string( $raw ) ) {
                $raw = trim( $raw );
            }

            if ( 'email' === $key ) {
                $out[ $key ] = $raw ? sanitize_email( $raw ) : '';
            } elseif ( in_array( $key, $url_keys, true ) ) {
                $out[ $key ] = $raw ? esc_url_raw( $raw ) : '';
            } elseif ( in_array( $key, $absint_keys, true ) ) {
                // Conserver une chaîne vide si non renseigné (sinon absint('') = 0
                // écraserait le fallback côté front).
                $out[ $key ] = ( '' === $raw ) ? '' : (string) absint( $raw );
            } elseif ( 'date_evenement' === $key ) {
                $out[ $key ] = tplv_sanitize_date( $raw );
            } elseif ( 'adresse' === $key ) {
                $out[ $key ] = sanitize_textarea_field( $raw );
            } else {
                $out[ $key ] = sanitize_text_field( $raw );
            }
        }
    }

    return $out;
}

/**
 * Valide une date au format Y-m-d (sinon chaîne vide).
 */
function tplv_sanitize_date( string $raw ): string {
    if ( '' === $raw ) {
        return '';
    }
    $d = DateTime::createFromFormat( 'Y-m-d', $raw );
    return ( $d && $d->format( 'Y-m-d' ) === $raw ) ? $raw : '';
}

/* ─────────────────────────────────────────────
 * 5. Rendu de la page "Réglages TPLV"
 * ───────────────────────────────────────────── */

function tplv_render_reglages_page(): void {
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }
    ?>
    <div class="wrap">
        <h1>Réglages TPLV</h1>
        <p class="description" style="max-width:680px">Renseignez ici les informations principales du site. Ces réglages seront progressivement reliés aux pages publiques. Laissez un champ vide pour conserver la valeur par défaut actuelle.</p>
        <form action="options.php" method="post">
            <?php
            settings_fields( 'tplv_settings_group' );
            do_settings_sections( 'tplv-reglages' );
            submit_button( 'Enregistrer les réglages' );
            ?>
        </form>
    </div>
    <?php
}
