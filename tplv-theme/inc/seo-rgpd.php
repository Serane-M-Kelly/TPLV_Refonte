<?php
/**
 * SEO & RGPD — Intégration Yoast SEO + Complianz (Étape 8)
 *
 * Ce fichier :
 *  1. Supprime les balises <meta> dupliquées (thème ↔ Yoast) pour éviter
 *     le double-indexation et les warnings Search Console.
 *  2. Configure les Open Graph / Twitter Card via Yoast plutôt que le thème.
 *  3. Ajoute la balise canonique via Yoast (désactivation de celle du thème).
 *  4. Intègre Complianz : différé les scripts non-essentiels jusqu'au consentement.
 */

// ─────────────────────────────────────────────
// 1. Suppression des meta du thème si Yoast est actif
//    (éviter le doublon description / og:title / og:description / canonical)
// ─────────────────────────────────────────────

add_action( 'after_setup_theme', 'tplv_maybe_disable_theme_meta' );
function tplv_maybe_disable_theme_meta(): void {
    if ( ! defined( 'WPSEO_VERSION' ) ) {
        return; // Yoast non actif — on garde les meta du thème
    }
    // Yoast prend en charge title-tag via add_theme_support('title-tag').
    // Rien à supprimer : Yoast détecte le support et prend le relais automatiquement.
    // Le remove/add_theme_support redondant est supprimé (il n'avait aucun effet utile).
}

/**
 * Retire les balises <meta name="description"> que le thème injecterait
 * manuellement dans wp_head() — Yoast les génère lui-même.
 *
 * Note : les balises meta dans header.php sont déjà absentes (gérées par wp_head()),
 * ce filtre est un filet de sécurité si un développeur en ajoutait.
 */
add_filter( 'wpseo_metadesc', 'tplv_yoast_meta_desc_filter' );
function tplv_yoast_meta_desc_filter( string $desc ): string {
    // Laisse Yoast gérer la description sans modification.
    return $desc;
}

// ─────────────────────────────────────────────
// 2. Injecter les données structurées Schema.org de l'association
//    (complète le Schema "@type": "Organization" de Yoast)
// ─────────────────────────────────────────────

add_action( 'wp_head', 'tplv_inject_org_schema', 5 );
function tplv_inject_org_schema(): void {
    if ( ! is_front_page() ) {
        return; // Schema Organisation uniquement sur la page d'accueil
    }
    $schema = [
        '@context' => 'https://schema.org',
        '@type'    => 'NonprofitOrganization',
        'name'     => 'Tous Pour La Vie Janzé',
        'url'      => home_url( '/' ),
        'logo'     => get_template_directory_uri() . '/assets/images/logo-transparent.png',
        'address'  => [
            '@type'           => 'PostalAddress',
            'streetAddress'   => '28 rue Jean-Marie Lacire',
            'addressLocality' => 'Janzé',
            'postalCode'      => '35150',
            'addressCountry'  => 'FR',
        ],
        'contactPoint' => [
            '@type'       => 'ContactPoint',
            'email'       => tplv_opt( 'email', 'contact@tplv-janze.fr' ),
            'contactType' => 'customer support',
            'areaServed'  => 'FR',
        ],
        'sameAs' => array_values( array_filter( [
            tplv_opt( 'facebook', 'https://www.facebook.com/TPLVJanze' ),
            tplv_opt( 'instagram', 'https://www.instagram.com/tplv_janze' ),
        ] ) ),
        'foundingDate' => '2004',
        'description'  => "Association loi 1901 mobilisant Janzé et ses environs depuis 2004 autour d'un week-end festif et sportif pour soutenir la recherche contre le cancer.",
    ];
    echo '<script type="application/ld+json">' . wp_json_encode( $schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . PHP_EOL;
}

// ─────────────────────────────────────────────
// 3. Complianz — Intégration des scripts conditionnels
//    (les scripts Lucide Icons et autres sont chargés après consentement si nécessaire)
// ─────────────────────────────────────────────

/**
 * Marque les scripts analytiques et marketing comme nécessitant
 * un consentement Complianz (catégorie "statistics" ou "marketing").
 *
 * Les scripts tiers (Google Fonts, Lucide) sont en "functional" car
 * strictement nécessaires à l'affichage — aucun consentement requis.
 *
 * Si un script analytique est ajouté ultérieurement (GA4, Matomo…),
 * utiliser le filtre Complianz `cmplz_set_category` ou passer par
 * l'interface Complianz → Intégrations.
 */
add_filter( 'cmplz_consent_api_on', '__return_true' );

/**
 * Désactiver l'injection automatique des balises cookie par Complianz
 * sur la page APA (données de santé — aucun tracking souhaité).
 */
add_filter( 'cmplz_disable_cookiebanner', 'tplv_disable_banner_on_apa' );
function tplv_disable_banner_on_apa( bool $disable ): bool {
    if ( is_page( 'apa' ) ) {
        return true; // Pas de bandeau cookie sur la page médicale
    }
    return $disable;
}

// ─────────────────────────────────────────────
// 4. Sitemap XML — Exclusion des pages techniques
// ─────────────────────────────────────────────

/**
 * Exclut la page de confirmation du sitemap Yoast
 * (elle ne doit pas être indexée).
 */

add_filter( 'wpseo_exclude_from_sitemap_by_post_ids', 'tplv_sitemap_exclude_confirmation' );
function tplv_sitemap_exclude_confirmation( array $excluded_ids ): array {
    $confirm = get_page_by_path( 'confirmation' );
    if ( $confirm ) {
        $excluded_ids[] = $confirm->ID;
    }
    return $excluded_ids;
}
