<?php
/**
 * Page Dons — Intégration HelloAsso dynamique (Étape 7)
 *
 * L'URL du bouton de don est récupérée via l'API HelloAsso REST v5.
 * En cas d'indisponibilité de l'API, un fallback iframe est affiché.
 *
 * Slug WP : "dons"
 */
get_header();

// Récupération de l'URL et détection du fallback.
// tplv_ha_get_campaigns() est déjà mis en cache (transient 15 min).
// On l'appelle une seule fois pour éviter une double requête API.
// Slug HelloAsso (lien à confirmer avec l'association)
$org_slug       = tplv_ha_get_setting( 'org_slug', 'tous-pour-la-vie-janze' );
$campaigns      = tplv_ha_get_campaigns();
$api_disponible = ! empty( $campaigns );
$don_url        = tplv_ha_get_don_url(); // Utilise le cache transient chargé ci-dessus
?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header page-header-magenta">
    <div class="container">
      <span class="eyebrow eyebrow--white">Soutenez la recherche</span>
      <h1>Chaque don compte</h1>
      <p>Votre soutien finance directement la recherche contre le cancer — Institut Curie, Centre Eugène-Marquis de Rennes et CHU de Rennes — pour changer des vies.</p>
    </div>
  </div>

  <!-- SÉLECTEUR DE MONTANT + TRANSPARENCE -->
  <div class="section">
    <div class="container">
      <div class="don-layout">

        <!-- Colonne gauche : sélecteur de don -->
        <div class="fade-in">
          <span class="eyebrow eyebrow--magenta">Faire un don</span>
          <h2 class="section-title">Choisissez votre montant</h2>
          <div class="don-amount-selector">
            <button class="amount-pill" onclick="selectAmount(this, 10)">10 €</button>
            <button class="amount-pill active" onclick="selectAmount(this, 25)">25 €</button>
            <button class="amount-pill" onclick="selectAmount(this, 50)">50 €</button>
            <button class="amount-pill" onclick="selectAmount(this, 100)">100 €</button>
          </div>
          <input type="number" class="amount-custom" id="amount-custom"
                 placeholder="Autre montant (€)" min="1"
                 aria-label="Autre montant en euros"/>
          <p class="don-tax-hint">
            💡 Un don de 25 € vous revient à seulement <strong>8,25 €</strong> après déduction fiscale de 66 %.
          </p>

          <?php if ( $api_disponible ) : ?>
            <!-- Bouton dynamique HelloAsso (API disponible) -->
            <a href="<?php echo esc_url( $don_url ); ?>"
               id="btn-helloasso"
               class="btn btn-primary btn--lg"
               target="_blank" rel="noopener noreferrer">
              <i data-lucide="heart"></i> Faire un don via HelloAsso
            </a>
            <p class="don-helloasso-hint">
              ↗ Vous serez redirigé vers HelloAsso (site externe sécurisé) pour finaliser votre don.
            </p>
          <?php else : ?>
            <!-- Fallback iframe si l'API HelloAsso est indisponible -->
            <div class="don-iframe-fallback">
              <p class="don-helloasso-hint" style="margin-bottom:1rem">
                ⚠️ Le widget de don est temporairement indisponible. Vous pouvez faire un don directement via HelloAsso :
              </p>
              <iframe
                src="https://www.helloasso.com/associations/<?php echo esc_attr( $org_slug ); ?>/collectes/faire-un-don/widget"
                width="100%" height="750"
                allowtransparency="true"
                style="border:none;border-radius:var(--radius-lg);"
                title="Formulaire de don HelloAsso TPLV"
                loading="lazy">
              </iframe>
              <p class="rgpd-notice" style="margin-top:.75rem">
                ↗ Ce widget est fourni par HelloAsso (site externe sécurisé). Consultez leur <a href="https://www.helloasso.com/confidentialite" target="_blank" rel="noopener">politique de confidentialité</a>.
              </p>
            </div>
          <?php endif; ?>
        </div>

        <!-- Colonne droite : transparence -->
        <div class="fade-in">
          <span class="eyebrow eyebrow--magenta">Transparence</span>
          <h2 class="section-title">Où va votre don&nbsp;?</h2>
          <p class="don-text">
            100 % des fonds collectés lors de nos événements sont reversés à des organismes de recherche reconnus d'utilité publique. Depuis sa structuration en 2004, TPLV a soutenu l'Institut Curie, le Centre Eugène-Marquis de Rennes et le CHU de Rennes.
          </p>
          <p class="don-text">
            Chaque année, l'association publie son bilan financier complet — disponible dans la section <a href="<?php echo esc_url( home_url( '/documents/' ) ); ?>">Documents</a>. La transparence est au cœur de notre engagement.
          </p>
          <div class="don-beneficiary-list">
            <div class="don-beneficiary-item">
              <div class="don-beneficiary-item__title">Institut Curie</div>
              <div class="don-beneficiary-item__sub">Paris · Recherche oncologie</div>
            </div>
            <div class="don-beneficiary-item">
              <div class="don-beneficiary-item__title">Centre Eugène-Marquis</div>
              <div class="don-beneficiary-item__sub">Rennes · Centre de lutte contre le cancer</div>
            </div>
            <div class="don-beneficiary-item">
              <div class="don-beneficiary-item__title">CHU de Rennes</div>
              <div class="don-beneficiary-item__sub">Bretagne · Soins &amp; Recherche</div>
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>

  <!-- IMPACT — fond navy -->
  <div class="section section-navy">
    <div class="container">
      <div class="section-header section-header--center fade-in">
        <h2 class="section-title section-title-white">L'impact de votre générosité</h2>
      </div>
      <div class="don-impact-stats fade-in">
        <div class="don-stat">
          <div class="num">800 000 €</div>
          <div class="lbl">Total reversé depuis 2004</div>
        </div>
        <div class="don-stat">
          <div class="num">100 %</div>
          <div class="lbl">Des fonds reversés à la recherche</div>
        </div>
        <div class="don-stat">
          <div class="num">20 ans</div>
          <div class="lbl">D'engagement sans interruption</div>
        </div>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
