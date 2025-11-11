(function () {
  // Gestiona la interactividad del menu principal sin exponer variables globales.
  const mobileToggle = document.getElementById('btnMenu');
  const mobileMenu = document.getElementById('mobileMenu');
  const dropdownToggle = document.getElementById('navDropdownToggle');
  const dropdownMenu = document.getElementById('navDropdownMenu');

  // Referencias reutilizables para cerrar los menus cuando sea necesario.
  const closeDropdown = () => {
    if (dropdownMenu) dropdownMenu.classList.add('hidden');
    if (dropdownToggle) dropdownToggle.setAttribute('aria-expanded', 'false');
  };

  const closeMobileMenu = () => {
    if (mobileMenu) mobileMenu.classList.add('hidden');
    if (mobileToggle) mobileToggle.setAttribute('aria-expanded', 'false');
  };

  // Alterna el menu desplegable del encabezado manteniendo la accesibilidad.
  if (dropdownToggle && dropdownMenu) {
    dropdownToggle.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = !dropdownMenu.classList.contains('hidden');
      if (isOpen) {
        closeDropdown();
      } else {
        dropdownMenu.classList.remove('hidden');
        dropdownToggle.setAttribute('aria-expanded', 'true');
      }
    });
  }

  // Controla la visibilidad del menu movil en pantallas pequenas.
  if (mobileToggle && mobileMenu) {
    mobileToggle.addEventListener('click', (event) => {
      event.stopPropagation();
      const isOpen = !mobileMenu.classList.contains('hidden');
      if (isOpen) {
        closeMobileMenu();
      } else {
        mobileMenu.classList.remove('hidden');
        mobileToggle.setAttribute('aria-expanded', 'true');
      }
    });
  }

  // Cierra menus si el usuario hace clic en el documento fuera de ellos.
  document.addEventListener('click', (event) => {
    if (dropdownMenu && !dropdownMenu.contains(event.target) && event.target !== dropdownToggle) {
      closeDropdown();
    }
    if (mobileMenu && !mobileMenu.contains(event.target) && event.target !== mobileToggle) {
      closeMobileMenu();
    }
  });

  // Permite cerrar menus mediante la tecla Escape para mejorar la accesibilidad.
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') {
      closeDropdown();
      closeMobileMenu();
    }
  });

  // Actualiza el contenido con el ano actual en el elemento indicado.
  const yearEl = document.getElementById('year');
  if (yearEl) {
    yearEl.textContent = new Date().getFullYear();
  }
})();