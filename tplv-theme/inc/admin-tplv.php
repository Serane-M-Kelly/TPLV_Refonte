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

    // Contenu — regroupe les raccourcis de création (CPT + formulaires).
    add_submenu_page( 'tplv-dashboard', 'Contenu TPLV', 'Contenu', 'edit_posts', 'tplv-contenu', 'tplv_render_contenu_page' );

    // Réglages — administrateurs + rôle Gestionnaire TPLV (capacité dédiée, jamais manage_options).
    add_submenu_page( 'tplv-dashboard', 'Réglages TPLV', 'Réglages TPLV', 'manage_tplv_settings', 'tplv-reglages', 'tplv_render_reglages_page' );
}

/* ─────────────────────────────────────────────
 * 2. Cartes des pages d'admin (raccourcis)
 *    Chaque carte n'est ajoutée que si sa cible est disponible.
 * ───────────────────────────────────────────── */

/**
 * Cartes du Tableau de bord — vue d'ensemble minimale, les 2 raccourcis
 * les plus utilisés. Le reste des raccourcis vit dans la page "Contenu".
 */
function tplv_dashboard_cards(): array {
    $cards = [];

    // Raccourci de création le plus courant — actualité en priorité, sinon événement.
    // Pointe vers le formulaire rapide (Phase Admin 7), pas l'écran WordPress complet.
    if ( post_type_exists( 'actualite' ) ) {
        $cards[] = [ 'title' => 'Ajouter une actualité', 'desc' => 'Publier une nouvelle', 'icon' => 'dashicons-megaphone', 'url' => admin_url( 'admin.php?page=tplv-actu-rapide' ), 'blank' => false ];
    } elseif ( post_type_exists( 'evenement' ) ) {
        $cards[] = [ 'title' => 'Ajouter un événement', 'desc' => 'Créer un événement', 'icon' => 'dashicons-calendar-alt', 'url' => admin_url( 'admin.php?page=tplv-event-rapide' ), 'blank' => false ];
    }

    // Voir le site public.
    $cards[] = [ 'title' => 'Voir le site', 'desc' => 'Ouvrir le site public', 'icon' => 'dashicons-external', 'url' => home_url( '/' ), 'blank' => true ];

    // Raccourcis Réglages — administrateurs + Gestionnaire TPLV, ne s'affichent pas pour un profil bénévole.
    if ( current_user_can( 'manage_tplv_settings' ) ) {
        $reglages = admin_url( 'admin.php?page=tplv-reglages' );
        $cards[] = [ 'title' => 'Modifier les chiffres clés', 'desc' => 'Bénévoles, participants, montants…', 'icon' => 'dashicons-chart-bar', 'url' => $reglages, 'blank' => false ];
        $cards[] = [ 'title' => 'Modifier les coordonnées',   'desc' => 'Email, téléphone, adresse',         'icon' => 'dashicons-location',  'url' => $reglages, 'blank' => false ];
        $cards[] = [ 'title' => 'Modifier le lien HelloAsso', 'desc' => 'Page de don en ligne',              'icon' => 'dashicons-money-alt', 'url' => $reglages, 'blank' => false ];
    }

    return $cards;
}

/**
 * Cartes de la page "Contenu" — tous les raccourcis de création
 * (CPT existants + formulaires CF7). Chaque carte n'apparaît que si sa
 * cible existe réellement (pas de lien mort).
 */
function tplv_contenu_cards(): array {
    $cards = [];

    // Actualités et Événements — formulaires rapides (Phase Admin 7), pas l'écran WordPress complet.
    $cpt_cards_rapides = [
        'actualite' => [ 'Ajouter une actualité', 'Publier une nouvelle', 'dashicons-megaphone',    'admin.php?page=tplv-actu-rapide' ],
        'evenement' => [ 'Ajouter un événement',  'Créer un événement',   'dashicons-calendar-alt', 'admin.php?page=tplv-event-rapide' ],
    ];
    foreach ( $cpt_cards_rapides as $post_type => $c ) {
        if ( post_type_exists( $post_type ) ) {
            $cards[] = [ 'title' => $c[0], 'desc' => $c[1], 'icon' => $c[2], 'url' => admin_url( $c[3] ), 'blank' => false ];
        }
    }

    // Documents et Partenaires — pas de formulaire rapide dédié, écran WordPress standard.
    $cpt_cards = [
        'partenaire' => [ 'Ajouter un partenaire', 'Logo et lien partenaire', 'dashicons-groups' ],
        'document'   => [ 'Ajouter un document',   'Mettre en ligne un PDF',  'dashicons-media-document' ],
    ];
    foreach ( $cpt_cards as $post_type => $c ) {
        if ( post_type_exists( $post_type ) ) {
            $cards[] = [ 'title' => $c[0], 'desc' => $c[1], 'icon' => $c[2], 'url' => admin_url( 'post-new.php?post_type=' . $post_type ), 'blank' => false ];
        }
    }

    // Formulaires Contact Form 7 — uniquement si le plugin est actif.
    // Rejoindra une sous-page "Formulaires" dédiée quand elle existera (Phase Admin 8).
    if ( defined( 'WPCF7_VERSION' ) ) {
        $cards[] = [ 'title' => 'Voir les formulaires', 'desc' => 'Contact, bénévoles, APA', 'icon' => 'dashicons-email', 'url' => admin_url( 'admin.php?page=wpcf7' ), 'blank' => false ];
    }

    return $cards;
}

/* ─────────────────────────────────────────────
 * 3. Rendu commun des pages à cartes
 * ───────────────────────────────────────────── */

/**
 * Affiche une grille de cartes avec un titre et une intro.
 *
 * @param string $title Titre affiché en <h1>.
 * @param string $intro Texte d'introduction.
 * @param array  $cards Cartes à afficher (voir tplv_dashboard_cards()).
 */
function tplv_render_cards_page( string $title, string $intro, array $cards ): void {
    ?>
    <div class="wrap tplv-admin">
        <h1><?php echo esc_html( $title ); ?></h1>
        <p class="tplv-welcome"><?php echo esc_html( $intro ); ?></p>

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

function tplv_render_dashboard(): void {
    tplv_render_cards_page(
        'Tableau de bord — Tous Pour la Vie Janzé',
        "Bienvenue dans l'espace d'administration de Tous Pour la Vie Janzé.",
        tplv_dashboard_cards()
    );
}

function tplv_render_contenu_page(): void {
    tplv_render_cards_page(
        'Contenu TPLV',
        'Publiez une actualité, un événement, ou consultez les formulaires reçus.',
        tplv_contenu_cards()
    );
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
