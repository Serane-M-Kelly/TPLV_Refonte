<?php get_header(); ?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header page-header-sky page-header--wave-surface">
    <div class="container">
      <span class="eyebrow">Santé &amp; Mouvement</span>
      <h1>Activité Physique Adaptée</h1>
      <p>Des séances conçues pour les personnes touchées par le cancer et leurs proches — encadrées par un professionnel certifié en APA.</p>
    </div>
  </div>

  <!-- BÉNÉFICES -->
  <div class="section section-surface">
    <div class="container">
      <div class="section-header section-header--center fade-in">
        <span class="eyebrow eyebrow--magenta">Pourquoi l'APA</span>
        <h2 class="section-title">Les bénéfices prouvés de l'activité physique adaptée</h2>
      </div>
      <div class="benefits-grid">
        <div class="benefit-item fade-in">
          <div class="benefit-icon"><i data-lucide="biceps-flexed"></i></div>
          <h3>Forme physique</h3>
          <p>Maintien et développement de la condition physique durant et après les traitements.</p>
        </div>
        <div class="benefit-item fade-in">
          <div class="benefit-icon"><i data-lucide="heart-pulse"></i></div>
          <h3>Gestion du stress</h3>
          <p>Réduction de l'anxiété et amélioration de la qualité du sommeil grâce à l'activité régulière.</p>
        </div>
        <div class="benefit-item fade-in">
          <div class="benefit-icon"><i data-lucide="smile"></i></div>
          <h3>Bien-être moral</h3>
          <p>Regain de confiance en soi, sentiment d'appartenance à un groupe bienveillant.</p>
        </div>
        <div class="benefit-item fade-in">
          <div class="benefit-icon"><i data-lucide="hospital"></i></div>
          <h3>Soutien au soin</h3>
          <p>L'APA s'intègre dans le parcours de soins comme un outil complémentaire reconnu par la médecine.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- PROFIL INTERVENANT -->
  <div class="section">
    <div class="container">
      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">Votre intervenant</span>
        <h2 class="section-title">Un professionnel à vos côtés</h2>
      </div>
      <div class="profile-card fade-in">
        <div class="profile-info">
          <h3>Un encadrement professionnel</h3>
          <div class="role">Enseignant en Activité Physique Adaptée (STAPS APA-S)</div>
          <p>Les séances sont encadrées par un intervenant qualifié en Activité Physique Adaptée. Le nom de l'intervenant et les informations pratiques sont à confirmer auprès de l'association.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- INFOS PRATIQUES -->
  <div class="section section-sky">
    <div class="container">
      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">Organisation</span>
        <h2 class="section-title">Infos pratiques</h2>
        <p class="section-sub" style="font-style:italic">Jours, créneaux, lieu et tarif à confirmer auprès de l'association.</p>
      </div>
      <table class="info-table fade-in">
        <thead>
          <tr><th>Infos</th><th>Détails</th></tr>
        </thead>
        <tbody>
          <tr><td><strong>Jours</strong></td><td>Mardi &amp; Jeudi</td></tr>
          <tr><td><strong>Créneaux</strong></td><td>Mardi 10h00–11h30 · Jeudi 18h00–19h30</td></tr>
          <tr><td><strong>Lieu</strong></td><td>Salle de la Boulonnière, Janzé (35150)</td></tr>
          <tr><td><strong>Matériel</strong></td><td>Tenue de sport confortable. Matériel fourni sur place.</td></tr>
          <tr><td><strong>Tarif</strong></td><td>Gratuit — financé par l'association TPLV</td></tr>
          <tr><td><strong>Conditions</strong></td><td>Certificat médical de non contre-indication à l'APA requis</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <!-- GALERIE PHOTOS -->
  <div class="section section-surface">
    <div class="container">
      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">En images</span>
        <h2 class="section-title">Nos séances en photos</h2>
      </div>
      <div class="gallery-grid">
        <div class="gallery-item fade-in">Photo séance APA<br>800×600 px</div>
        <div class="gallery-item gallery-item--alt fade-in">Photo séance APA<br>800×600 px</div>
        <div class="gallery-item fade-in">Photo séance APA<br>800×600 px</div>
        <div class="gallery-item gallery-item--alt fade-in">Photo séance APA<br>800×600 px</div>
      </div>
    </div>
  </div>

  <!-- FORMULAIRE D'INSCRIPTION APA — CF7 + RGPD -->
  <div class="section">
    <div class="container">
      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">Rejoindre le programme</span>
        <h2 class="section-title">Inscription APA</h2>
      </div>
      <div class="apa-form-wrap fade-in">

        <!-- Bannière RGPD Santé — OBLIGATOIRE (art. 9 RGPD) -->
        <div class="notice-rgpd notice-health">
          <strong>⚠️ Données sensibles — RGPD Santé (article 9)</strong><br>
          Les informations saisies dans ce formulaire sont transmises uniquement à l'intervenant APA de TPLV. <strong>Elles ne sont pas conservées sur ce serveur ni stockées en base de données.</strong> Elles sont supprimées après traitement de votre demande. Aucune donnée médicale n'est partagée avec des tiers.
        </div>

        <?php echo tplv_cf7( 'APA TPLV', 'form-apa' ); ?>

      </div>
    </div>
  </div>

<?php get_footer(); ?>
