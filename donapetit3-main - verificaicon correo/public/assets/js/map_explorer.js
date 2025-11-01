// Minimal logic for map explorer: populate slider and business list
// and reflect current radius value. No map library dependency.
(function () {
  'use strict';

  function qs(selector, root) { return (root || document).querySelector(selector); }
  function ce(tag, props) { const el = document.createElement(tag); if (props) Object.assign(el, props); return el; }

  function init() {
    var wrapper = qs('#donapp-map');
    if (!wrapper) return;

    var configAttr = wrapper.getAttribute('data-map-config');
    var config = {};
    try { config = configAttr ? JSON.parse(configAttr) : {}; } catch (e) { config = {}; }

    var radiusInput = qs('#radius', wrapper.parentElement) || qs('#radius');
    var radiusValue = qs('#radius-value', wrapper.parentElement) || qs('#radius-value');
    var businessList = qs('#business-list');

    var radiusMin = (config.radiusMin != null ? Number(config.radiusMin) : 0.5);
    var radiusMax = (config.radiusMax != null ? Number(config.radiusMax) : 5);
    var radiusStep = (config.radiusStep != null ? Number(config.radiusStep) : 0.5);
    var radius = (config.radius != null ? Number(config.radius) : 2.5);

    if (radiusInput) {
      radiusInput.min = String(radiusMin);
      radiusInput.max = String(radiusMax);
      radiusInput.step = String(radiusStep);
      radiusInput.value = String(radius);
      if (radiusValue) radiusValue.textContent = String(radius) + ' km';
      radiusInput.addEventListener('input', function () {
        if (radiusValue) radiusValue.textContent = this.value + ' km';
      });
    }

    if (businessList && Array.isArray(config.businesses)) {
      businessList.innerHTML = '';
      config.businesses.forEach(function (b) {
        var li = ce('li');
        li.className = 'flex items-center justify-between gap-3 rounded-2xl border border-slate-200 bg-slate-50 p-3';
        var info = ce('div');
        info.innerHTML = '<div class="text-sm font-semibold text-slate-900">' + (b.nombre || 'Negocio') + '</div>' +
          '<div class="text-xs text-slate-600">' + (b.distancia || '-') + ' • ' + (b.detalle || '') + '</div>';
        var dot = ce('span');
        var status = String(b.estado || '').toLowerCase();
        dot.className = 'h-2.5 w-2.5 rounded-full ' + (status === 'online' ? 'bg-emerald-500' : 'bg-slate-400');
        li.appendChild(info);
        li.appendChild(dot);
        businessList.appendChild(li);
      });
    }

    // Placeholder background for map container
    var mapBox = qs('#map');
    if (mapBox) {
      mapBox.style.background = 'repeating-linear-gradient(45deg, #e2e8f0, #e2e8f0 10px, #edf2f7 10px, #edf2f7 20px)';
    }
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

