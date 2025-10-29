(function () {
  const dashboard = document.getElementById('admin-dashboard');
  if (!dashboard) return;

  const setActiveButton = (buttons, activeButton) => {
    buttons.forEach((button) => {
      if (button === activeButton) {
        button.classList.add('bg-brand', 'text-white', 'border-brand', 'shadow');
        button.classList.remove('bg-white', 'text-slate-600');
        button.setAttribute('aria-pressed', 'true');
      } else {
        button.classList.remove('bg-brand', 'text-white', 'border-brand', 'shadow');
        button.classList.add('bg-white', 'text-slate-600');
        button.setAttribute('aria-pressed', 'false');
      }
    });
  };

  const alertButtons = Array.from(dashboard.querySelectorAll('[data-alert-filter]'));
  const alertItems = Array.from(dashboard.querySelectorAll('[data-alert-item]'));
  const alertEmpty = dashboard.querySelector('[data-alert-empty]');
  let currentAlertFilter = 'all';

  const updateAlerts = () => {
    let visibleCount = 0;

    alertItems.forEach((item) => {
      const type = item.getAttribute('data-alert-type') || 'stock';
      const shouldShow = currentAlertFilter === 'all' || currentAlertFilter === type;

      if (shouldShow) {
        item.classList.remove('hidden');
        visibleCount += 1;
      } else {
        item.classList.add('hidden');
      }
    });

    if (alertEmpty) {
      if (visibleCount === 0) {
        alertEmpty.classList.remove('hidden');
      } else {
        alertEmpty.classList.add('hidden');
      }
    }
  };

  if (alertButtons.length) {
    alertButtons.forEach((button) => {
      button.addEventListener('click', () => {
        currentAlertFilter = button.getAttribute('data-alert-filter') || 'all';
        setActiveButton(alertButtons, button);
        updateAlerts();
      });
    });

    const defaultButton = alertButtons.find((button) => button.getAttribute('data-alert-default') === 'true') || alertButtons[0];
    if (defaultButton) {
      defaultButton.click();
    } else {
      updateAlerts();
    }
  }

  const setupSearch = (inputId, rowSelector, emptyId) => {
    const input = document.getElementById(inputId);
    if (!input) return;

    const rows = Array.from(dashboard.querySelectorAll(rowSelector));
    if (!rows.length) return;

    const emptyMessage = emptyId ? document.getElementById(emptyId) : null;

    const applyFilter = () => {
      const term = input.value.trim().toLowerCase();
      let visible = 0;

      rows.forEach((row) => {
        const haystack = row.getAttribute('data-search-haystack') || '';
        const matches = term === '' || haystack.includes(term);

        if (matches) {
          row.classList.remove('hidden');
          visible += 1;
        } else {
          row.classList.add('hidden');
        }
      });

      if (emptyMessage) {
        if (visible === 0) {
          emptyMessage.classList.remove('hidden');
        } else {
          emptyMessage.classList.add('hidden');
        }
      }
    };

    input.addEventListener('input', applyFilter);
    applyFilter();
  };

  setupSearch('lowStockSearch', '[data-low-stock-row]', 'lowStockEmpty');
  setupSearch('expiringSearch', '[data-expiring-row]', 'expiringEmpty');

  const activityFilter = document.getElementById('activityFilter');
  const activityItems = Array.from(dashboard.querySelectorAll('[data-activity-item]'));
  const activityEmpty = document.getElementById('activityEmpty');

  const updateActivity = () => {
    if (!activityFilter || !activityItems.length) return;

    const filterValue = activityFilter.value || 'all';
    let visible = 0;

    activityItems.forEach((item) => {
      const status = item.getAttribute('data-activity-status') || 'otros';
      const matches = filterValue === 'all' || filterValue === status;

      if (matches) {
        item.classList.remove('hidden');
        visible += 1;
      } else {
        item.classList.add('hidden');
      }
    });

    if (activityEmpty) {
      if (visible === 0) {
        activityEmpty.classList.remove('hidden');
      } else {
        activityEmpty.classList.add('hidden');
      }
    }
  };

  if (activityFilter && activityItems.length) {
    activityFilter.addEventListener('change', updateActivity);
    updateActivity();
  }
})();

