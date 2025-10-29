// Minimal logic for admin notification settings: keep the
// radius badge synced with the range input.
(function () {
  'use strict';
  function init() {
    var radius = document.getElementById('radio');
    var badge = document.getElementById('radio-value');
    if (!radius || !badge) return;

    function sync() { badge.textContent = String(radius.value); }
    sync();
    radius.addEventListener('input', sync);
    radius.addEventListener('change', sync);
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();

