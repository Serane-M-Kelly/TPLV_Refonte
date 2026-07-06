<?php get_header(); ?>

  <!-- EN-TÊTE DE PAGE -->
  <div class="page-header page-header--benevoles page-header--wave-surface">
    <div class="container">
      <span class="eyebrow">Engagez-vous</span>
      <h1>Devenir bénévole</h1>
      <p>TPLV, c'est 500 bénévoles qui donnent de leur temps pour changer des vies. Rejoignez-les.</p>
    </div>
  </div>

  <!-- RAISONS DE S'ENGAGER -->
  <div class="section section-surface">
    <div class="container">
      <div class="section-header section-header--center fade-in">
        <span class="eyebrow eyebrow--magenta">Pourquoi s'engager</span>
        <h2 class="section-title">5 bonnes raisons de rejoindre l'équipe</h2>
      </div>
      <div class="reasons-grid">
        <div class="reason-item fade-in">
          <div class="reason-icon"><i data-lucide="target"></i></div>
          <div>
            <h3>Un engagement qui a du sens</h3>
            <p>Chaque heure donnée contribue directement à la lutte contre le cancer. Votre engagement a un impact concret et mesurable.</p>
          </div>
        </div>
        <div class="reason-item fade-in">
          <div class="reason-icon"><i data-lucide="users"></i></div>
          <div>
            <h3>Une communauté exceptionnelle</h3>
            <p>Rejoindre TPLV, c'est intégrer une équipe soudée, bienveillante, qui partage des valeurs de solidarité et de générosité.</p>
          </div>
        </div>
        <div class="reason-item fade-in">
          <div class="reason-icon"><i data-lucide="party-popper"></i></div>
          <div>
            <h3>Un week-end inoubliable</h3>
            <p>Le week-end TPLV est une fête. Bénévoles et participants partagent une énergie unique, dans une ambiance festive et chaleureuse.</p>
          </div>
        </div>
        <div class="reason-item fade-in">
          <div class="reason-icon"><i data-lucide="sprout"></i></div>
          <div>
            <h3>Des compétences valorisées</h3>
            <p>Organisation, logistique, communication, cuisine : chaque profil a sa place. Vous apportez vos compétences, TPLV les valorise.</p>
          </div>
        </div>
        <div class="reason-item fade-in">
          <div class="reason-icon"><i data-lucide="heart-handshake"></i></div>
          <div>
            <h3>Vivre l'aventure humaine</h3>
            <p>Derrière chaque sourire de participant, il y a un bénévole. Cette reconnaissance humaine est unique et profondément enrichissante.</p>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- TÉMOIGNAGE -->
  <div class="section">
    <div class="container">
      <div class="testimonial-card fade-in">
        <blockquote>Donner de son temps à TPLV, c'est rejoindre une équipe soudée et vivre une aventure humaine forte, au service d'une cause qui touche toutes les familles.</blockquote>
        <cite>— Témoignage à confirmer avec l'association</cite>
      </div>
    </div>
  </div>

  <!-- FORMULAIRE DE CANDIDATURE — CF7 -->
  <div class="section section-sky">
    <div class="container">
      <div class="section-header fade-in">
        <span class="eyebrow eyebrow--magenta">Candidature</span>
        <h2 class="section-title">Je souhaite m'engager</h2>
      </div>
      <div class="form-narrow fade-in">
        <?php echo tplv_cf7( 'Bénévoles TPLV', 'form-benevoles' ); ?>
      </div>
    </div>
  </div>

<?php get_footer(); ?>
