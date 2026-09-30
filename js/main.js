
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.querySelector('.menuBtn'), menu = document.querySelector('.menu');
  if (btn) btn.addEventListener('click', () => menu.classList.toggle('open'));
  document.querySelectorAll('.menu a').forEach(a => { if (a.pathname === location.pathname) a.classList.add('active') });
  document.querySelectorAll('[data-gallery] img').forEach(img => img.addEventListener('click', () => {
    const m = document.querySelector('#image-modal'); if (!m) return; m.querySelector('img').src = img.src; m.classList.add('open');
  }));
  const modal = document.querySelector('#image-modal'); if (modal) modal.addEventListener('click', e => { if (e.target === modal || e.target.closest('.close')) modal.classList.remove('open') });
  document.querySelectorAll('[data-year]').forEach(x => x.textContent = new Date().getFullYear());
});
