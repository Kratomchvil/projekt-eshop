// product-modal.js - quick view modal for any product card
document.addEventListener('DOMContentLoaded', function(){
  function qs(sel, ctx){ return (ctx||document).querySelector(sel); }

  const modal = qs('#product-modal');
  if(!modal) return;
  const overlay = qs('.modal-overlay', modal);
  const closeBtn = qs('.modal-close', modal);
  const titleEl = qs('.modal-title', modal);
  const descEl = qs('.modal-desc', modal);
  const priceEl = qs('.modal-price', modal);
  const imgEl = qs('.modal-image img', modal);
  const buyBtn = qs('.modal-buy', modal);

  function buildBuyHref(title, originalHref){
    try{
      if(originalHref && originalHref.includes('kosik')) return originalHref;
      if(originalHref && originalHref.includes('?product=')) return originalHref;
    }catch(e){}
    const p = encodeURIComponent(title || 'Produkt');
    return 'kosik-krok1.html?product=' + p;
  }

  function openModal(data){
    titleEl.textContent = data.title || '';
    descEl.textContent = data.desc || '';
    priceEl.textContent = data.price || '';
    imgEl.src = data.img || '';
    imgEl.alt = data.title || '';
    buyBtn.href = buildBuyHref(data.title, data.href);
    // store last opened product info on modal for buy handler
    modal._current = { title: data.title || '', img: data.img || '', href: buyBtn.href };
    modal.setAttribute('aria-hidden','false');
    modal.style.display = 'flex';
  }

  function closeModal(){
    modal.setAttribute('aria-hidden','true');
    modal.style.display = 'none';
  }

  overlay.addEventListener('click', closeModal);
  closeBtn.addEventListener('click', closeModal);
  document.addEventListener('keydown', function(e){ if(e.key==='Escape') closeModal(); });

  // delegate clicks on product list buy/show buttons
  document.addEventListener('click', function(e){
    const btn = e.target.closest('.product-list .btn-primary, .product-card .btn-primary, .btn-primary');
    if(!btn) return;
    const card = btn.closest('.product-card') || btn.closest('.card.product-card');
    if(!card) return;
    e.preventDefault();
    const title = (card.querySelector('.title')||{}).textContent || (card.querySelector('img')||{}).alt || '';
    const desc = (card.querySelector('.muted')||{}).textContent || '';
    const price = (card.querySelector('.price')||{}).textContent || '';
    const img = (card.querySelector('img')||{}).src || '';
    const href = btn.getAttribute('href') || '';
    openModal({ title, desc, price, img, href });
  });
  
  // when user clicks Buy inside modal, add correct product to cart and show notification
  if(buyBtn){
    buyBtn.addEventListener('click', function(e){
      e.preventDefault();
      try{
        if(window.cartHelpers && typeof window.cartHelpers.addItem === 'function'){
          const info = modal._current || {};
          const price = priceEl.textContent || '0 Kč';
          window.cartHelpers.addItem({ name: info.title || titleEl.textContent, img: info.img || imgEl.src, qty: 1, price: price });
          // show notification instead of redirecting
          showNotification(info.title || titleEl.textContent);
        }
      }catch(err){}
    });
  }

  // notification function
  function showNotification(productName){
    // remove existing notification if any
    const existing = document.querySelector('.cart-notification');
    if(existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = 'cart-notification';
    notification.style.cssText = `
      position: fixed;
      top: 20px;
      right: 20px;
      background: #4CAF50;
      color: white;
      padding: 16px 24px;
      border-radius: 4px;
      z-index: 10000;
      box-shadow: 0 2px 8px rgba(0,0,0,0.2);
      animation: slideIn 0.3s ease-out;
    `;
    notification.textContent = productName + ' byl přidán do košíku';
    document.body.appendChild(notification);
    
    // remove notification after 3 seconds
    setTimeout(() => {
      notification.style.animation = 'slideOut 0.3s ease-out';
      setTimeout(() => notification.remove(), 300);
    }, 3000);
  }

  // add animations if not already in document
  if(!document.querySelector('style[data-cart-notify]')){
    const style = document.createElement('style');
    style.setAttribute('data-cart-notify', 'true');
    style.textContent = `
      @keyframes slideIn {
        from { transform: translateX(400px); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
      }
      @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(400px); opacity: 0; }
      }
    `;
    document.head.appendChild(style);
  }

  // also intercept direct .btn-primary clicks inside product cards (in case user wants instant add)
  document.addEventListener('click', function(e){
    const direct = e.target.closest('.card.product-card .btn-primary, .product-card .btn-primary');
    if(!direct) return;
    const card = direct.closest('.product-card') || direct.closest('.card.product-card');
    if(!card) return;
    const href = direct.getAttribute('href') || '';
    const title = (card.querySelector('.title')||{}).textContent || (card.querySelector('img')||{}).alt || '';
    const price = (card.querySelector('.price')||{}).textContent || '';
    const img = (card.querySelector('img')||{}).src || '';
    // if link points directly to kosik (or explicit intent), add item and navigate
    if(href && href.includes('kosik')){
      e.preventDefault();
      try{ if(window.cartHelpers) window.cartHelpers.addItem({ name: title, img: img, qty: 1, price: price }); }catch(e){}
      window.location.href = href;
      return;
    }
    // otherwise, let modal open handler handle showing correct product (which will add image when bought)
  });
});
