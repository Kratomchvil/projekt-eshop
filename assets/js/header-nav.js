// header-nav.js - toggles hamburger menu
(function(){
  function qs(sel){ return document.querySelector(sel); }
  const btn = qs('.hamburger');
  const menu = qs('.hamburger-menu');
  if(!btn || !menu) return;
  btn.addEventListener('click', function(){
    const open = btn.getAttribute('aria-expanded') === 'true';
    btn.setAttribute('aria-expanded', String(!open));
    menu.classList.toggle('open');
  });
  document.addEventListener('click', function(e){
    if(!menu.classList.contains('open')) return;
    if(e.target === btn || menu.contains(e.target)) return;
    menu.classList.remove('open');
    btn.setAttribute('aria-expanded','false');
  });
})();
