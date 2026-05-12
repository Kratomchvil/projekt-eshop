// assets/js/filter.js - client-side filtering for produkty.html
document.addEventListener('DOMContentLoaded', function(){
  const links = document.querySelectorAll('[data-filter]');
  const products = Array.from(document.querySelectorAll('.product-card'));

  function applyFilter(cat){
    if(!cat || cat==='all'){
      products.forEach(p=> p.style.display='');
      return;
    }
    products.forEach(p=>{
      const c = p.getAttribute('data-category') || '';
      if(c === cat) p.style.display=''; else p.style.display='none';
    });
  }

  links.forEach(a=>{
    a.addEventListener('click', function(e){
      e.preventDefault();
      const cat = a.getAttribute('data-filter');
      applyFilter(cat);
      // update url without reload
      if(history && history.replaceState){
        const url = new URL(window.location);
        if(cat && cat!=='all') url.searchParams.set('category', cat); else url.searchParams.delete('category');
        history.replaceState({}, '', url);
      }
    });
  });

  // apply filter from url on load
  const params = new URLSearchParams(window.location.search);
  const initial = params.get('category');
  if(initial){ applyFilter(initial); }
});
