<?php
/**
 * HelloAsso — Intégration API REST v5 (Étape 7)
 *
 * Connexion à l'API HelloAsso pour récupérer les campagnes de dons et
 * les formulaires d'inscription aux événements de l'association TPLV.
 *
 * Configuration :
 *   Dans l'admin WP → Réglages → Lecture, ou via wp-config.php :
 *     define( 'HELLOASSO_CLIENT_ID',     'votre_client_id' );
 *     define( 'HELLOASSO_CLIENT_SECRET', 'votre_client_secret' );
 *     define( 'HELLOASSO_ORG_SLUG',      'tous-pour-la-vie-janze' );
 *
 *   Alternativement, ces valeurs peuvent être définies via des options WP
 *   (voir tplv_ha_get_setting() ci-dessous).
 *
 * Usage dans un template :
 *   $campagnes = tplv_ha_get_campaigns();          // Liste des campagnes actives
 *   $don_url   = tplv_ha_get_don_url();            // URL de la campagne de dons
 *   $total_dons = tplv_ha_get_total_raised();      // Total collecté (en centimes)
 */

// ─────────────────────────────────────────────
// 1. Configuration et helpers
// ─────────────────────────────────────────────

/**
 * Récupère un paramètre HelloAsso (constante wp-config.php > option BDD > défaut).
 */
function tplv_ha_get_setting( string $key, string $default = '' ): string {
    $const_map = [
        'client_id'     => 'HELLOASSO_CLIENT_ID',
        'client_secret' => 'HELLOASSO_CLIENT_SECRET',
        'org_slug'      => 'HELLOASSO_ORG_SLUG',
    ];
    if ( isset( $const_map[ $key ] ) && defined( $const_map[ $key ] ) ) {
        return constant( $const_map[ $key ] );
    }
    return get_option( 'tplv_ha_' . $key, $default );
}

// ─────────────────────────────────────────────
// 2. Authentification OAuth2 (token client_credentials)
// ─────────────────────────────────────────────

/**
 * Récupère ou renouvelle le token d'accès HelloAsso (OAuth2 client_credentials).
 * Le token est mis en cache dans les transients WP (durée : 55 min).
 */
function tplv_ha_get_token(): string|false {
    $cached = get_transient( 'tplv_helloasso_token' );
    if ( $cached ) {
        return $cached;
    }

    $client_id     = tplv_ha_get_setting( 'client_id' );
    $client_secret = tplv_ha_get_setting( 'client_secret' );

    if ( ! $client_id || ! $client_secret ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[TPLV HelloAsso] client_id ou client_secret manquant.' );
        }
        return false;
    }

    $response = wp_remote_post( 'https://api.helloasso.com/oauth2/token', [
        'timeout' => 15,
        'body'    => [
            'grant_type'    => 'client_credentials',
            'client_id'     => $client_id,
            'client_secret' => $client_secret,
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[TPLV HelloAsso] Erreur token : ' . $response->get_error_message() );
        }
        return false;
    }

    $body = json_decode( wp_remote_retrieve_body( $response ), true );
    if ( empty( $body['access_token'] ) ) {
        return false;
    }

    $expires_in = (int) ( $body['expires_in'] ?? 3600 );
    set_transient( 'tplv_helloasso_token', $body['access_token'], $expires_in - 300 );

    return $body['access_token'];
}

// ─────────────────────────────────────────────
// 3. Appel API générique
// ─────────────────────────────────────────────

/**
 * Effectue un appel GET authentifié vers l'API HelloAsso v5.
 * Résultat mis en cache (transient) pendant $cache_seconds secondes.
 *
 * @param string $endpoint     Chemin relatif, ex. "/v5/organizations/slug/campaigns"
 * @param int    $cache_seconds Durée du cache WP (0 = pas de cache)
 * @return array|false         Données décodées ou false en cas d'erreur
 */
function tplv_ha_request( string $endpoint, int $cache_seconds = 900 ): array|false {
    $cache_key = 'tplv_ha_' . md5( $endpoint );

    if ( $cache_seconds > 0 ) {
        $cached = get_transient( $cache_key );
        if ( false !== $cached ) {
            return $cached;
        }
    }

    $token = tplv_ha_get_token();
    if ( ! $token ) {
        return false;
    }

    $response = wp_remote_get( 'https://api.helloasso.com' . $endpoint, [
        'timeout' => 15,
        'headers' => [
            'Authorization' => 'Bearer ' . $token,
            'Accept'        => 'application/json',
        ],
    ] );

    if ( is_wp_error( $response ) ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[TPLV HelloAsso] Erreur GET ' . $endpoint . ' : ' . $response->get_error_message() );
        }
        return false;
    }

    $code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== (int) $code ) {
        if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
            error_log( '[TPLV HelloAsso] HTTP ' . $code . ' sur ' . $endpoint );
        }
        return false;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( $cache_seconds > 0 && is_array( $data ) ) {
        set_transient( $cache_key, $data, $cache_seconds );
    }

    return is_array( $data ) ? $data : false;
}

// ─────────────────────────────────────────────
// 4. Fonctions métier
// ─────────────────────────────────────────────

/**
 * Récupère les campagnes actives de l'organisation TPLV.
 * Résultat mis en cache 15 min.
 *
 * @return array  Liste de campagnes ou tableau vide.
 */
function tplv_ha_get_campaigns(): array {
    $slug = tplv_ha_get_setting( 'org_slug', 'tous-pour-la-vie-janze' );
    $data = tplv_ha_request( "/v5/organizations/{$slug}/campaigns?pageSize=20" );
    return $data['data'] ?? [];
}

/**
 * Retourne l'URL de la première campagne de type "Donation"
 * (collecte de dons classique), avec fallback sur la page org HelloAsso.
 *
 * @return string URL HelloAsso
 */
function tplv_ha_get_don_url(): string {
    $slug      = tplv_ha_get_setting( 'org_slug', 'tous-pour-la-vie-janze' );
    $fallback  = "https://www.helloasso.com/associations/{$slug}/collectes";
    $campaigns = tplv_ha_get_campaigns();

    foreach ( $campaigns as $c ) {
        if ( isset( $c['type'] ) && 'Donation' === $c['type'] && ! empty( $c['url'] ) ) {
            return esc_url( $c['url'] );
        }
    }
    return esc_url( $fallback );
}

/**
 * Récupère le total collecté (en centimes) sur toutes les collectes.
 * Résultat mis en cache 30 min.
 *
 * @return int  Total en centimes (diviser par 100 pour avoir les euros).
 */
function tplv_ha_get_total_raised(): int {
    $slug = tplv_ha_get_setting( 'org_slug', 'tous-pour-la-vie-janze' );
    $data = tplv_ha_request( "/v5/organizations/{$slug}/campaigns?pageSize=100", 1800 );

    if ( empty( $data['data'] ) ) {
        return 0;
    }

    $total = 0;
    foreach ( $data['data'] as $c ) {
        $total += (int) ( $c['amountCollected'] ?? 0 );
    }
    return $total;
}

// ─────────────────────────────────────────────
// 5. Shortcode [tplv_don_button]
// ─────────────────────────────────────────────

/**
 * Shortcode permettant d'insérer un bouton "Faire un don" dynamique.
 *
 * Paramètres :
 *   label  — texte du bouton (défaut : "Faire un don via HelloAsso")
 *   class  — classes CSS supplémentaires (défaut : "btn btn-primary btn--lg")
 *
 * Exemple : [tplv_don_button label="Je soutiens TPLV"]
 */
add_shortcode( 'tplv_don_button', 'tplv_shortcode_don_button' );
function tplv_shortcode_don_button( array $atts ): string {
    $atts = shortcode_atts( [
        'label' => 'Faire un don via HelloAsso',
        'class' => 'btn btn-primary btn--lg',
    ], $atts, 'tplv_don_button' );

    $url = tplv_ha_get_don_url();

    return sprintf(
        '<a href="%s" class="%s" target="_blank" rel="noopener noreferrer">'
        . '<i data-lucide="heart"></i> %s'
        . '</a>'
        . '<p class="don-helloasso-hint">↗ Vous serez redirigé vers HelloAsso (site externe sécurisé) pour finaliser votre don.</p>',
        $url,
        esc_attr( $atts['class'] ),
        esc_html( $atts['label'] )
    );
}
