<?php
/**
 * Back-office TPLV — menu principal + tableau de bord.
 *
 * Point d'entrée centralisé et guidé pour l'association.
 * - Menu top-level "TPLV" (icône cœur).
 * - Page "Tableau de bord" avec raccourcis vers les actions principales.
 * - Aucun lien mort : un raccourci n'apparaît que si sa cible existe.
 *
 * La page "Réglages TPLV" est rendue par inc/reglages-tplv.php
 * (callback tplv_render_reglages_page).
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/* ─────────────────────────────────────────────
 * 1. Menu admin "TPLV"
 * ───────────────────────────────────────────── */

add_action( 'admin_menu', 'tplv_register_admin_menu' );
function tplv_register_admin_menu(): void {
    // Menu top-level — visible par les gestionnaires de contenu (edit_posts).
    add_menu_page(
        'TPLV',
        'TPLV',
        'edit_posts',
        'tplv-dashboard',
        'tplv_render_dashboard',
        'dashicons-heart',
        3
    );

    // Premier sous-menu = renomme l'entrée dupliquée du top-level.
    add_submenu_page( 'tplv-dashboard', 'Tableau de bord TPLV', 'Tableau de bord', 'edit_posts', 'tplv-dashboard', 'tplv_render_dashboard' );

    // Réglages — administrateurs uniquement.
    add_submenu_page( 'tplv-dashboard', 'Réglages TPLV', 'Réglages TPLV', 'manage_options', 'tplv-reglages', 'tplv_render_reglages_page' );

    // Raccourcis de création — uniquement si le type de contenu existe (pas de lien mort).
    $shortcuts = [
        'actualite'  => 'Ajouter une actualité',
        'evenement'  => 'Ajouter un événement',
        'partenaire' => 'Ajouter un partenaire',
        'document'   => 'Ajouter un document',
    ];
    foreach ( $shortcuts as $post_type => $label ) {
        if ( post_type_exists( $post_type ) ) {
            add_submenu_page( 'tplv-dashboard', $label, $label, 'edit_posts', 'post-new.php?post_type=' . $post_type );
        }
    }
}

/* ─────────────────────────────────────────────
 * 2. Cartes du tableau de bord (raccourcis)
 *    Chaque carte n'est ajoutée que si sa cible est disponible.
 * ───────────────────────────────────────────── */

function tplv_dashboard_cards(): array {
    $cards = [];

    // Raccourcis réglages — administrateurs uniquement.
    if ( current_user_can( 'manage_options' ) ) {
        $reglages = admin_url( 'admin.php?page=tplv-reglages' );
        $cards[] = [ 'title' => 'Modifier les chiffres clés', 'desc' => 'Bénévoles, participants, montants…', 'icon' => 'dashicons-chart-bar',  'url' => $reglages, 'blank' => false ];
        $cards[] = [ 'title' => 'Modifier les coordonnées',   'desc' => 'Email, téléphone, adresse',         'icon' => 'dashicons-location',   'url' => $reglages, 'blank' => false ];
        $cards[] = [ 'title' => 'Modifier le lien HelloAsso', 'desc' => 'Page de don en ligne',              'icon' => 'dashicons-money-alt',  'url' => $reglages, 'blank' => false ];
    }

    // Raccourcis de création de contenu — uniquement si le CPT existe.
    $cpt_cards = [
        'actualite'  => [ 'Ajouter une actualité', 'Publier une nouvelle',     'dashicons-megaphone' ],
        'evenement'  => [ 'Ajouter un événement',  'Créer un événement',       'dashicons-calendar-alt' ],
        'partenaire' => [ 'Ajouter un partenaire', 'Logo et lien partenaire',  'dashicons-groups' ],
        'document'   => [ 'Ajouter un document',   'Mettre en ligne un PDF',   'dashicons-media-document' ],
    ];
    foreach ( $cpt_cards as $post_type => $c ) {
        if ( post_type_exists( $post_type ) ) {
            $cards[] = [ 'title' => $c[0], 'desc' => $c[1], 'icon' => $c[2], 'url' => admin_url( 'post-new.php?post_type=' . $post_type ), 'blank' => false ];
        }
    }

    // Formulaires Contact Form 7 — uniquement si le plugin est actif.
    if ( defined( 'WPCF7_VERSION' ) ) {
        $cards[] = [ 'title' => 'Voir les formulaires', 'desc' => 'Contact, bénévoles, APA', 'icon' => 'dashicons-email', 'url' => admin_url( 'admin.php?page=wpcf7' ), 'blank' => false ];
    }

    // Voir le site public.
    $cards[] = [ 'title' => 'Voir le site', 'desc' => 'Ouvrir le site public', 'icon' => 'dashicons-external', 'url' => home_url( '/' ), 'blank' => true ];

    return $cards;
}

/* ─────────────────────────────────────────────
 * 3. Rendu de la page "Tableau de bord TPLV"
 * ───────────────────────────────────────────── */

function tplv_render_dashboard(): void {
    $cards = tplv_dashboard_cards();
    ?>
    <div class="wrap tplv-admin">
        <h1>Tableau de bord — Tous Pour la Vie Janzé</h1>
        <p class="tplv-welcome">Bienvenue dans l'espace d'administration de Tous Pour la Vie Janzé. Depuis cette page, vous pouvez mettre à jour les contenus principaux du site sans toucher au code.</p>

        <div class="tplv-cards">
            <?php foreach ( $cards as $card ) : ?>
                <a class="tplv-card" href="<?php echo esc_url( $card['url'] ); ?>"<?php echo $card['blank'] ? ' target="_blank" rel="noopener"' : ''; ?>>
                    <span class="dashicons <?php echo esc_attr( $card['icon'] ); ?>"></span>
                    <span class="tplv-card-title"><?php echo esc_html( $card['title'] ); ?></span>
                    <span class="tplv-card-desc"><?php echo esc_html( $card['desc'] ); ?></span>
                </a>
            <?php endforeach; ?>
        </div>

        <div class="tplv-help">
            <h2>Comment ça marche&nbsp;?</h2>
            <p>1. Cliquez sur une carte ci-dessus. 2. Remplissez le formulaire. 3. Enregistrez : le site se met à jour automatiquement.</p>
            <p>En cas de doute, vous pouvez fermer une page sans enregistrer : rien ne sera modifié.</p>
        </div>
    </div>
    <?php
    tplv_dashboard_styles();
}

/**
 * Styles du tableau de bord (chargés uniquement sur cette page d'admin).
 * N'affecte pas le front-end.
 */
function tplv_dashboard_styles(): void {
    ?>
    <style>
        .tplv-welcome { font-size:15px; max-width:760px; line-height:1.6; }
        .tplv-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(240px,1fr)); gap:16px; margin:24px 0; }
        .tplv-card { display:flex; flex-direction:column; gap:6px; padding:20px; background:#fff; border:1px solid #dcdcde; border-radius:8px; text-decoration:none; color:#1d2327; transition:box-shadow .15s, border-color .15s; }
        .tplv-card:hover { border-color:#C4145A; box-shadow:0 2px 8px rgba(0,0,0,.08); }
        .tplv-card .dashicons { font-size:28px; width:28px; height:28px; color:#C4145A; }
        .tplv-card-title { font-weight:600; font-size:14px; }
        .tplv-card-desc { font-size:12px; color:#646970; }
        .tplv-help { max-width:760px; padding:16px 20px; background:#f6f7f7; border-radius:8px; }
        .tplv-help h2 { margin-top:0; font-size:15px; }
    </style>
    <?php
}
