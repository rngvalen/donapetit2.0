(function () {
  const form = document.getElementById('catalogFilters');
  if (!form) return;

  const searchInput = document.getElementById('catalog-search');
  const estadoSelect = document.getElementById('catalog-estado');
  const orderSelect = document.getElementById('catalog-order');
  const resetButton = document.getElementById('catalog-reset');
  const tableBody = document.getElementById('catalogTableBody');
  const tableWrapper = document.getElementById('catalog-table-wrapper');
  const emptyState = document.getElementById('catalog-empty-state');

  const summary = {
    total: document.getElementById('catalog-total-count'),
    activos: document.getElementById('catalog-activos-count'),
    bajos: document.getElementById('catalog-bajos-count'),
  };

  const rows = tableBody
    ? Array.from(tableBody.querySelectorAll('[data-product-row]')).map((row, index) => {
        const cantidadRaw = parseInt(row.dataset.cantidad || '', 10);
        const cantidad = Number.isFinite(cantidadRaw) ? cantidadRaw : null;
        return {
          row,
          index,
          name: (row.dataset.name || '').toLowerCase(),
          unit: (row.dataset.unit || '').toLowerCase(),
          comments: (row.dataset.comments || '').toLowerCase(),
          categoria: (row.dataset.categoria || '').toLowerCase(),
          estado: (row.dataset.estado || '').toLowerCase(),
          cantidad,
        };
      })
    : [];

  const defaultFilters = {
    search: searchInput ? searchInput.value : '',
    estado: estadoSelect ? estadoSelect.value : '',
    order: orderSelect ? orderSelect.value : 'recent',
  };

  const updateSummary = (visibleItems) => {
    const visibles = Array.isArray(visibleItems) ? visibleItems : [];
    const total = visibles.length;
    const activos = visibles.filter((item) => item.estado === 'disponible' || item.estado === 'activo').length;
    const bajos = visibles.filter((item) => typeof item.cantidad === 'number' && item.cantidad <= 5).length;

    if (summary.total) summary.total.textContent = String(total);
    if (summary.activos) summary.activos.textContent = String(activos);
    if (summary.bajos) summary.bajos.textContent = String(bajos);
  };

  const toggleEmptyState = (visibleCount) => {
    if (!tableWrapper || !emptyState) return;
    if (visibleCount === 0) {
      tableWrapper.classList.add('hidden');
      emptyState.classList.remove('hidden');
    } else {
      tableWrapper.classList.remove('hidden');
      emptyState.classList.add('hidden');
    }
  };

  const getSortedRows = (orderValue) => {
    const sorted = rows.slice();
    switch (orderValue) {
      case 'oldest':
        sorted.sort((a, b) => a.index - b.index);
        break;
      case 'cantidad_desc':
        sorted.sort((a, b) => {
          const aVal = typeof a.cantidad === 'number' ? a.cantidad : Number.NEGATIVE_INFINITY;
          const bVal = typeof b.cantidad === 'number' ? b.cantidad : Number.NEGATIVE_INFINITY;
          return bVal - aVal;
        });
        break;
      case 'cantidad_asc':
        sorted.sort((a, b) => {
          const aVal = typeof a.cantidad === 'number' ? a.cantidad : Number.POSITIVE_INFINITY;
          const bVal = typeof b.cantidad === 'number' ? b.cantidad : Number.POSITIVE_INFINITY;
          return aVal - bVal;
        });
        break;
      case 'recent':
      default:
        sorted.sort((a, b) => a.index - b.index);
        break;
    }
    return sorted;
  };

  const renderRows = (orderValue, visibilityMap) => {
    if (!tableBody) return;
    const sortedRows = getSortedRows(orderValue);
    sortedRows.forEach((item) => {
      const isVisible = visibilityMap.get(item);
      if (isVisible) {
        item.row.classList.remove('hidden');
      } else {
        item.row.classList.add('hidden');
      }
      tableBody.appendChild(item.row);
    });
  };

  const applyFilters = (event) => {
    if (event) event.preventDefault();

    if (!rows.length) {
      updateSummary([]);
      toggleEmptyState(0);
      return;
    }

    const searchTerm = (searchInput && searchInput.value ? searchInput.value : '').trim().toLowerCase();
    const estadoValue = (estadoSelect && estadoSelect.value ? estadoSelect.value : '').trim().toLowerCase();
    const orderValue = orderSelect && orderSelect.value ? orderSelect.value : 'recent';

    const visibility = new Map();
    const visibles = [];

    rows.forEach((item) => {
      let matches = true;
      if (searchTerm) {
        const haystack = `${item.name} ${item.unit} ${item.comments} ${item.categoria}`.trim();
        matches = haystack.includes(searchTerm);
      }

      if (matches && estadoValue) {
        matches = item.estado === estadoValue;
      }

      visibility.set(item, matches);
      if (matches) {
        visibles.push(item);
      }
    });

    renderRows(orderValue, visibility);
    updateSummary(visibles);
    toggleEmptyState(visibles.length);
  };

  form.addEventListener('submit', applyFilters);

  if (searchInput) {
    searchInput.addEventListener('input', () => applyFilters());
  }

  if (estadoSelect) {
    estadoSelect.addEventListener('change', () => applyFilters());
  }

  if (orderSelect) {
    orderSelect.addEventListener('change', () => applyFilters());
  }

  if (resetButton) {
    resetButton.addEventListener('click', () => {
      if (searchInput) searchInput.value = defaultFilters.search;
      if (estadoSelect) estadoSelect.value = defaultFilters.estado;
      if (orderSelect) orderSelect.value = defaultFilters.order;
      applyFilters();
    });
  }

  applyFilters();
})();