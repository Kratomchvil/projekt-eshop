// assets/js/shop.js - small helpers for demo cart
function parseQuery() {
  const params = new URLSearchParams(window.location.search);
  const obj = {};
  for (const [k,v] of params.entries()) obj[k]=v;
  return obj;
}

function renderCartFromQuery(containerSelector){
  const q = parseQuery();
  const container = document.querySelector(containerSelector);
  if(!container) return;
  const product = q.product || 'Standard Drum Set';
  const qty = parseInt(q.qty || '1', 10) || 1;
  const price = 12990; // demo static price
  const shipping = 149;
  const subtotal = price * qty;
  const total = subtotal + shipping;

  container.innerHTML = `
    <div class="card">
      <h3>${product}</h3>
      <div class="muted">Cena: <span class="price">${price.toLocaleString()} Kč</span></div>
      <div class="muted">Množství: <input type="number" min="1" max="999" value="${qty}" id="cart-qty" style="width:80px; background:transparent; border:1px solid rgba(255,255,255,0.04); padding:0.4rem; border-radius:6px"></div>
    </div>
  `;

  const summary = document.querySelector('.cart-summary');
  if(summary){
    summary.innerHTML = `
      <h4>Souhrn objednávky</h4>
      <div>Produkty: <span id="sub">${subtotal.toLocaleString()} Kč</span></div>
      <div>Poštovné: <span id="ship">${shipping.toLocaleString()} Kč</span></div>
      <div style="font-weight:700; margin-top:0.5rem">Celkem: <span id="tot">${total.toLocaleString()} Kč</span></div>
      <div style="margin-top:1rem"><a class="btn btn-primary" href="kosik-krok2.html">Pokračovat k dopravě</a></div>
    `;
  }

  const qtyInput = document.getElementById('cart-qty');
  if(qtyInput){
    qtyInput.addEventListener('change', e=>{
      const newQty = Math.max(1, parseInt(e.target.value||1,10));
      const newSub = price * newQty;
      const newTot = newSub + shipping;
      document.getElementById('sub').textContent = newSub.toLocaleString() + ' Kč';
      document.getElementById('tot').textContent = newTot.toLocaleString() + ' Kč';
    });
  }
}

// Expose for pages to call
window.shopHelpers = { parseQuery, renderCartFromQuery };
