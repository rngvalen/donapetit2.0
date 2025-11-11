(function () {
  const form = document.getElementById('loadProductForm');
  const errorsBox = document.getElementById('errors');
  const nombreSelect = document.getElementById('nombre');
  const unidadInput = document.getElementById('unidad');
  const categoriaSelect = document.getElementById('categoria');
  const cantidadInput = document.getElementById('cantidad');
  const fechaInput = document.getElementById('vencimiento');

  if (!form) return;

  if (cantidadInput) {
    cantidadInput.addEventListener('input', function () {
      this.value = this.value.replace(/\D+/g, '');
      if (this.value !== '' && parseInt(this.value, 10) <= 0) this.value = '';
    });
  }

  form.addEventListener('submit', function (e) {
    const errs = [];

    if (!nombreSelect || !nombreSelect.value) {
      errs.push('Debes seleccionar un producto del catalogo.');
    }

    const unidad = unidadInput && unidadInput.value ? String(unidadInput.value).trim() : '';
    if (!unidad) errs.push('La unidad o presentacion es obligatoria.');

    if (!categoriaSelect || !categoriaSelect.value) {
      errs.push('Debes seleccionar una categoria.');
    }

    const cantidad = cantidadInput && cantidadInput.value ? String(cantidadInput.value).trim() : '';
    if (!cantidad) errs.push('La cantidad disponible es obligatoria.');
    else if (!/^[1-9]\d*$/.test(cantidad)) errs.push('La cantidad debe ser un entero positivo.');

    if (!fechaInput || !fechaInput.value) errs.push('La fecha de vencimiento es obligatoria.');

    if (errs.length) {
      e.preventDefault();
      errorsBox.innerHTML = '<ul class="list-disc list-inside">' + errs.map(function (x) { return '<li>' + x + '</li>'; }).join('') + '</ul>';
      errorsBox.classList.remove('hidden');
      errorsBox.scrollIntoView({ behavior: 'smooth', block: 'start' });
    } else {
      errorsBox.classList.add('hidden');
    }
  });
})();
