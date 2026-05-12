// cart.js - enhanced cart with price tracking
(function(){
  const KEY = 'drumshop_cart_v1';
  
  function getCart(){
    try{ return JSON.parse(localStorage.getItem(KEY) || '[]'); }catch(e){ return []; }
  }
  
  function saveCart(cart){ localStorage.setItem(KEY, JSON.stringify(cart)); }
  
  // Parse price string like "12 990 Kč" to number
  function parsePrice(priceStr){
    if(typeof priceStr === 'number') return priceStr;
    if(!priceStr) return 0;
    const cleaned = String(priceStr).replace(/[^\d]/g, '');
    return parseInt(cleaned, 10) || 0;
  }
  
  function formatPrice(num){
    return String(num).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' Kč';
  }
  
  function addItem(item){
    if(!item) return;
    let name = typeof item === 'string' ? item : (item.name || 'Produkt');
    const img = (typeof item === 'object' && item.img) ? item.img : '';
    const qtyToAdd = (typeof item === 'object' && item.qty) ? parseInt(item.qty,10) : 1;
    const price = (typeof item === 'object' && item.price) ? parsePrice(item.price) : 0;
    
    const cart = getCart();
    const existing = cart.find(i=>i.name === name);
    if(existing){ 
      existing.qty = (existing.qty||0) + qtyToAdd;
      if(img) existing.img = img;
      if(price && !existing.price) existing.price = price;
    }
    else cart.push({ name: name, img: img, qty: qtyToAdd, price: price });
    saveCart(cart);
    renderCount();
  }
  
  function totalCount(){
    return getCart().reduce((s,i)=>s + (i.qty||0), 0);
  }
  
  function totalPrice(){
    return getCart().reduce((sum, item) => {
      const itemPrice = item.price || 0;
      const qty = item.qty || 0;
      return sum + (itemPrice * qty);
    }, 0);
  }
  
  function renderCount(){
    const els = document.querySelectorAll('.cart-count');
    els.forEach(el => el.textContent = totalCount());
  }
  
  window.cartHelpers = { addItem, getCart, saveCart, totalCount, totalPrice, parsePrice, formatPrice, renderCount };
  document.addEventListener('DOMContentLoaded', renderCount);
})();
