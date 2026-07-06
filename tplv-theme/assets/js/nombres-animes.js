function formatNumber(n, prefix, suffix) {
  let s = n >= 1000000 ? (n / 1000000).toFixed(1) + ' M'
        : n >= 1000    ? n.toLocaleString('fr-FR')
        : n.toString();
  return (prefix || '') + s + (suffix || '');
}

const counters = document.querySelectorAll('.stat-number[data-target]');

const counterObs = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (!entry.isIntersecting) return;
    const el     = entry.target;
    const target = parseInt(el.dataset.target, 10);
    const prefix = el.dataset.prefix || '';
    const suffix = el.dataset.suffix || '';
    let current  = 0;
    const inc    = target / (1800 / 16);

    const timer = setInterval(() => {
      current = Math.min(current + inc, target);
      el.textContent = formatNumber(Math.round(current), prefix, suffix);
      if (current >= target) clearInterval(timer);
    }, 16);

    counterObs.unobserve(el);
  });
}, { threshold: 0.3 });

counters.forEach(c => counterObs.observe(c));
