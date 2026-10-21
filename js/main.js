(() => {
  const CATALOG = window.CATALOG || [];
  const PROMOS = window.PROMOS || [];
  const WA = document.body.dataset.wa;
  const $ = (s, r = document) => r.querySelector(s);
  const DEFAULT_MSG = 'Hola! Quiero hacer una consulta.';

  const waUrl = (msg) => `https://wa.me/${WA}?text=${encodeURIComponent(msg || DEFAULT_MSG)}`;
  const esc = (s) => String(s).replace(/[&<>"']/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const norm = (t) => String(t).toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');

  // Enlaces de WhatsApp (data-wa = mensaje opcional)
  document.querySelectorAll('[data-wa]').forEach((a) => (a.href = waUrl(a.dataset.wa)));

  $('#footCats').innerHTML = CATALOG.slice(0, 6).map((c) => `<li><i class="bi ${c.icon}"></i><a href="#" data-open="${c.id}">${esc(c.name)}</a></li>`).join('') + '<li><i class="bi bi-arrow-right-circle"></i><a href="#catalogo">Ver todo el catálogo</a></li>';
  $('#year').textContent = new Date().getFullYear();
  $('#statCats').textContent = `+${CATALOG.length} líneas`;

  // Navbar con fondo al hacer scroll
  const nav = $('#nav');
  const toTop = $('#toTop');
  const onScroll = () => {
    nav.classList.toggle('scrolled', window.scrollY > 30);
    toTop.classList.toggle('show', window.scrollY > 600);
  };
  toTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  onScroll();
  window.addEventListener('scroll', onScroll, { passive: true });

  // Navegación interna sin "#" en la URL
  const goTo = (id) => document.getElementById(id)?.scrollIntoView({ behavior: 'smooth', block: 'start' });
  document.addEventListener('click', (e) => {
    const a = e.target.closest('a[href^="#"]');
    if (!a || a.getAttribute('href').length < 2) return;
    const id = a.getAttribute('href').slice(1);
    if (!document.getElementById(id)) return;
    e.preventDefault();
    goTo(id);
  });
  if (location.hash.length > 1) {
    const id = decodeURIComponent(location.hash.slice(1));
    history.replaceState(null, '', location.pathname + location.search);
    setTimeout(() => goTo(id), 300);
  }

  // Parallax de los engranajes del inicio (se mueven y giran con el scroll)
  const g1 = document.querySelector('.hero-bg .g1');
  const g2 = document.querySelector('.hero-bg .g2');
  const heroEl = $('#inicio');
  if (g1 && g2 && !matchMedia('(prefers-reduced-motion: reduce)').matches) {
    let ticking = false;
    const parallax = () => {
      ticking = false;
      const y = window.scrollY;
      if (y > heroEl.offsetHeight * 1.3) return;
      g1.style.setProperty('--py', `${y * 0.28}px`);
      g1.style.setProperty('--rot', `${y * 0.12}deg`);
      g2.style.setProperty('--py', `${-y * 0.18}px`);
      g2.style.setProperty('--rot', `${-y * 0.22}deg`);
    };
    window.addEventListener('scroll', () => { if (!ticking) { ticking = true; requestAnimationFrame(parallax); } }, { passive: true });
    parallax();
  }

  // Cerrar menú móvil al elegir enlace
  const menu = $('#menu');
  menu.querySelectorAll('a.nav-link').forEach((a) =>
    a.addEventListener('click', () => bootstrap.Collapse.getInstance(menu)?.hide())
  );

  // ---- Mi consulta (carrito de consulta por WhatsApp) ----
  let cart = [];
  try { cart = JSON.parse(localStorage.getItem('foi-cart') || '[]').filter((id) => CATALOG.some((c) => c.id === id)); } catch {}
  const saveCart = () => { try { localStorage.setItem('foi-cart', JSON.stringify(cart)); } catch {} };

  const renderCart = () => {
    document.querySelectorAll('.cart-count').forEach((b) => { b.textContent = cart.length; b.hidden = !cart.length; });
    $('#cartEmpty').hidden = cart.length > 0;
    $('#cartList').innerHTML = cart.map((id) => {
      const c = CATALOG.find((x) => x.id === id);
      return `<li><img src="${c.images[0]}" alt=""><span>${esc(c.name)}</span><button data-del="${id}" aria-label="Quitar"><i class="bi bi-x-lg"></i></button></li>`;
    }).join('');
    const names = cart.map((id) => '- ' + CATALOG.find((x) => x.id === id).name).join('\n');
    const note = $('#cartNote').value.trim();
    $('#cartSend').href = waUrl(cart.length ? `Hola! Quiero consultar por:\n${names}${note ? '\n\nDetalle: ' + note : ''}` : DEFAULT_MSG);
  };

  const toggleCart = (id) => {
    cart = cart.includes(id) ? cart.filter((x) => x !== id) : [...cart, id];
    saveCart(); renderCart(); renderGrid();
  };

  document.addEventListener('click', (e) => {
    const a = e.target.closest('[data-add]');
    if (a) return toggleCart(a.dataset.add);
    const d = e.target.closest('[data-del]');
    if (d) toggleCart(d.dataset.del);
  });
  $('#cartNote').addEventListener('input', renderCart);
  $('#cartClear').addEventListener('click', () => { cart = []; saveCart(); renderCart(); renderGrid(); });

  // ---- Catálogo ----
  const grid = $('#catGrid');
  const chips = $('#chips');
  const tags = ['Todos', ...new Set(CATALOG.map((c) => c.tag))];
  let filter = 'Todos';
  let query = '';
  let animateCards = true; // solo animan en la primera carga (no al filtrar/buscar)

  const renderChips = () => {
    chips.innerHTML = tags.map((t) => `<button class="chip${t === filter ? ' active' : ''}" data-tag="${esc(t)}">${esc(t)}</button>`).join('');
  };

  function renderGrid() {
    const q = norm(query);
    const list = CATALOG.filter((c) => (filter === 'Todos' || c.tag === filter) && (!q || norm(`${c.name} ${c.desc} ${c.tag}`).includes(q)));
    $('#noRes').classList.toggle('d-none', list.length > 0);
    grid.innerHTML = list.map((c, i) => {
      const added = cart.includes(c.id);
      const fx = animateCards ? ` data-aos="fade-up" data-aos-delay="${(i % 4) * 90}"` : '';
      return `
      <div class="col-6 col-lg-4 col-xl-3"${fx}>
        <article class="cat-card">
          <div class="cat-media" data-open="${c.id}" role="button" tabindex="0" aria-label="Ver fotos de ${esc(c.name)}">
            <img src="${c.images[0]}" alt="${esc(c.name)}" loading="lazy">
            <span class="cat-tag">${esc(c.tag)}</span>
            <span class="cat-count"><i class="bi bi-images"></i>${c.images.length}</span>
            <span class="cat-view"><i class="bi bi-zoom-in"></i> Ver fotos</span>
          </div>
          <div class="cat-body">
            <h3>${esc(c.name)}</h3>
            <p>${esc(c.desc)}</p>
            <div class="cat-actions">
              <button class="btn btn-danger" data-open="${c.id}" aria-label="Ver fotos"><i class="bi bi-eye"></i><span class="d-none d-sm-inline"> Ver</span></button>
              <button class="btn btn-outline-dark cat-add${added ? ' added' : ''}" data-add="${c.id}" aria-label="${added ? 'Quitar de mi consulta' : 'Agregar a mi consulta'}" title="${added ? 'En tu consulta' : 'Agregar a mi consulta'}">${added ? '<i class="bi bi-check2"></i>' : '<i class="bi bi-plus-lg"></i>'}<span class="d-none d-sm-inline">${added ? ' Agregado' : ' Consulta'}</span></button>
            </div>
          </div>
        </article>
      </div>`;
    }).join('');
  }

  chips.addEventListener('click', (e) => {
    const b = e.target.closest('.chip');
    if (!b) return;
    filter = b.dataset.tag;
    renderChips();
    renderGrid();
  });
  $('#search').addEventListener('input', (e) => { query = e.target.value; renderGrid(); });

  renderChips();
  renderGrid();
  renderCart();
  animateCards = false;

  // Collage del hero: usa imágenes marcadas como portada
  const collageImgs = CATALOG.flatMap((c) => (c.portada || []).map((url) => ({ url, name: c.name }))).slice(0, 3);
  $('#collage').innerHTML = collageImgs.map((i) => `<figure><img src="${i.url}" alt="${esc(i.name)}"></figure>`).join('');

  // Contadores animados
  const targets = { cntCats: CATALOG.length, cntImgs: CATALOG.reduce((n, c) => n + c.images.length, 0), cntClients: 700 };
  const countObs = new IntersectionObserver((ents) => {
    if (!ents.some((en) => en.isIntersecting)) return;
    countObs.disconnect();
    Object.entries(targets).forEach(([id, end]) => {
      const el = $('#' + id);
      const t0 = performance.now();
      const tick = (t) => {
        const k = Math.min((t - t0) / 1400, 1);
        el.textContent = '+' + Math.round(end * (1 - Math.pow(1 - k, 3)));
        if (k < 1) requestAnimationFrame(tick);
      };
      requestAnimationFrame(tick);
    });
  }, { threshold: .4 });
  countObs.observe($('#stats'));

  // ---- Modal de galería ----
  const modalEl = $('#galleryModal');
  const modal = new bootstrap.Modal(modalEl);
  const mImg = $('#mImg');
  let cur = null, idx = 0;

  const show = (i) => {
    idx = (i + cur.images.length) % cur.images.length;
    mImg.src = cur.images[idx];
    mImg.alt = `${cur.name} - foto ${idx + 1}`;
    $('#mCount').textContent = `${idx + 1} / ${cur.images.length}`;
    document.querySelectorAll('#mThumbs button').forEach((b, k) => b.classList.toggle('active', k === idx));
  };

  const open = (id) => {
    cur = CATALOG.find((c) => c.id === id);
    if (!cur) return;
    $('#mTitle').textContent = cur.name;
    $('#mTag').textContent = cur.tag;
    $('#mDesc').textContent = cur.desc;
    $('#mWa').href = waUrl(`Hola! Quiero consultar por ${cur.name}.`);
    $('#mThumbs').innerHTML = cur.images.map((src, i) => `<button data-i="${i}" aria-label="Foto ${i + 1}"><img src="${src}" alt="" loading="lazy"></button>`).join('');
    const multi = cur.images.length > 1;
    $('#mPrev').hidden = $('#mNext').hidden = !multi;
    $('#mThumbs').hidden = !multi;
    show(0);
    modal.show();
  };

  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-open]');
    if (t) { e.preventDefault(); open(t.dataset.open); }
  });
  grid.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && e.target.dataset.open) open(e.target.dataset.open);
  });
  $('#mThumbs').addEventListener('click', (e) => {
    const b = e.target.closest('button');
    if (b) show(+b.dataset.i);
  });
  $('#mPrev').addEventListener('click', () => show(idx - 1));
  $('#mNext').addEventListener('click', () => show(idx + 1));
  modalEl.addEventListener('keydown', (e) => {
    if (e.key === 'ArrowLeft') show(idx - 1);
    if (e.key === 'ArrowRight') show(idx + 1);
  });

  // ---- Promociones (solo si hay cargadas) ----
  if (PROMOS.length) {
    $('#promos').hidden = false;
    $('#promoLink').hidden = false;
    $('#promoGrid').innerHTML = PROMOS.map((p) => `
      <div class="col-md-6 col-lg-4" data-aos="fade-up">
        <a class="promo" href="${waUrl('Hola! Vi la promoción: ' + (p.title || ''))}" target="_blank" rel="noopener">
          <img src="${esc(p.img)}" alt="${esc(p.title || 'Promoción')}" loading="lazy">
          ${p.title || p.text ? `<div><strong>${esc(p.title || '')}</strong><p class="mb-0 small text-secondary">${esc(p.text || '')}</p></div>` : ''}
        </a>
      </div>`).join('');
  }

  // ---- Formulario de contacto (PHPMailer en contact.php) ----
  const form = $('#contactForm');
  const msg = $('#formMsg');
  const btn = $('#formBtn');
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.className = 'small';
    if (!form.checkValidity()) { msg.textContent = 'Completá nombre, email y consulta.'; msg.classList.add('err'); return; }
    btn.disabled = true;
    const label = btn.textContent;
    btn.textContent = 'Enviando...';
    try {
      const res = await fetch('contact.php', { method: 'POST', body: new FormData(form) });
      const data = await res.json();
      msg.textContent = data.message;
      msg.classList.add(data.ok ? 'ok' : 'err');
      if (data.ok) form.reset();
    } catch {
      msg.textContent = 'Error de conexión. Probá por WhatsApp.';
      msg.classList.add('err');
    } finally {
      btn.disabled = false;
      btn.textContent = label;
    }
  });

  // ---- Carga: pantalla con logo (1ª visita de la sesión) + entrada escalonada del inicio ----
  const root = document.documentElement;
  const pre = $('#preloader');
  const ready = () => root.classList.add('is-ready');
  if (root.classList.contains('no-preload') || !pre) {
    pre && pre.remove();
    ready();
  } else {
    const t0 = performance.now();
    let finished = false;
    const finish = () => {
      if (finished) return;
      finished = true;
      setTimeout(() => {
        pre.classList.add('done');
        ready();
        setTimeout(() => pre.remove(), 700);
      }, Math.max(0, 900 - (performance.now() - t0)));
    };
    if (document.readyState === 'complete') finish(); else addEventListener('load', finish);
    setTimeout(finish, 3000);
  }

  window.AOS && AOS.init({ duration: 700, once: true, offset: 60 });
})();
