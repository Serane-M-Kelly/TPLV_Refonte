/* ── Page loader ── */
const loader = document.getElementById('page-loader');
if (loader) {
  const hasVisited = sessionStorage.getItem('tplv_visited');
  const path = window.location.pathname;
  // Détecte la page d'accueil (index.html, / ou le dossier racine)
  const isHomePage = path.endsWith('index.html') || path === '/' || path.endsWith('/TPLV/');

  if (!hasVisited || isHomePage) {
    loader.classList.add('loader-visible');
    const startTime = Date.now();

    window.addEventListener('load', () => {
      const hideLoader = () => {
        loader.classList.add('loader-done');
        setTimeout(() => loader.remove(), 550);
      };

      if (!hasVisited) {
        sessionStorage.setItem('tplv_visited', 'true');
        const elapsed = Date.now() - startTime;
        const remaining = Math.max(0, 3000 - elapsed);
        setTimeout(hideLoader, remaining);
      } else {
        hideLoader();
      }
    });
  } else {
    // N'est pas la première visite et n'est pas la page d'accueil : pas de loader
    loader.remove();
  }
}

/* ── Header sticky ── */
const header = document.getElementById('site-header');
window.addEventListener('scroll', () => {
  header.classList.toggle('scrolled', window.scrollY > 8);
}, { passive: true });

/* ── Hamburger ── */
const hamburger = document.getElementById('hamburger');
const overlay   = document.getElementById('mobile-overlay');

hamburger.addEventListener('click', () => {
  const open = overlay.classList.toggle('open');
  hamburger.classList.toggle('open', open);
  hamburger.setAttribute('aria-expanded', open);
  document.body.style.overflow = open ? 'hidden' : '';
});

overlay.querySelectorAll('.mobile-link').forEach(link => {
  link.addEventListener('click', () => {
    overlay.classList.remove('open');
    hamburger.classList.remove('open');
    hamburger.setAttribute('aria-expanded', 'false');
    document.body.style.overflow = '';
  });
});

/* ── Fade-in au scroll ── */
const faders = document.querySelectorAll('.fade-in');
const fadeObs = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if (e.isIntersecting) { e.target.classList.add('visible'); fadeObs.unobserve(e.target); }
  });
}, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });
faders.forEach(el => fadeObs.observe(el));


/* ── Pills de montant (page Dons) ── */
function selectAmount(btn, amount) {
  document.querySelectorAll('.amount-pill').forEach(p => p.classList.remove('active'));
  btn.classList.add('active');
  const custom = document.querySelector('.amount-custom');
  if (custom) custom.value = '';
}

/* ── Initialisation des icônes Lucide ── */
if (typeof lucide !== 'undefined') {
  lucide.createIcons();
}

/* ── Redirection CF7 après envoi réussi uniquement ──
   En cas d'échec d'envoi (wpcf7mailfailed), on ne redirige pas : le message
   d'erreur natif de CF7 s'affiche sur la page, pour que la personne sache
   que son message n'est pas parti (sinon la redirection masquait l'échec). */
function tplvCf7Redirect(event) {
  const id = event.target.id;
  let type = 'contact';
  if (id === 'form-benevoles') type = 'benevoles';
  else if (id === 'form-apa')  type = 'apa';
  window.location.href = '/confirmation/?type=' + type;
}
document.addEventListener('wpcf7mailsent', tplvCf7Redirect);
