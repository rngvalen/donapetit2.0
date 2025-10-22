<?php 
  $title = "Registro";
?>

<div class="container">
  <div class="logo">
    <img src="/donapetit3-main/app/view/LogoDon.png" alt="Logo DonAppétit">
  </div>

  <h1 class="brand">DonAppétit</h1>
  <h2 class="subtitle">Registrarse</h2>

  <?php if (!empty($error)): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form class="register-form" method="post" action="/donapetit3-main/public/?controller=Auth&action=registrar">
    <input type="text" name="nombre" placeholder="Nombre de usuario" required>
    <input type="email" name="email" placeholder="Email" required>
    <input type="password" name="contrasena" placeholder="Contraseña" required>
    <input type="text" name="telefono" placeholder="Teléfono (opcional)">

    <!-- Captura automática de ubicación -->
    <input type="hidden" name="latitud" id="latitud">
    <input type="hidden" name="longitud" id="longitud">

    <div class="radio-group">
      <label><input type="radio" name="rol" value="donante" required> Donante</label>
      <label><input type="radio" name="rol" value="receptor"> Receptor</label>
    </div>

    <button type="submit" class="btn">Registrarse</button>

    <p class="alt-login">
      ¿Ya tienes cuenta? 
      <a href="/donapetit3-main/view/?action=login">Iniciar sesión</a>
    </p>
  </form>
</div>

<!-- Script para obtener ubicación -->
<script>
navigator.geolocation.getCurrentPosition(pos => {
  document.getElementById('latitud').value = pos.coords.latitude;
  document.getElementById('longitud').value = pos.coords.longitude;
}, err => {
  console.warn("No se pudo obtener la ubicacion:", err);
});
</script>

<link rel="stylesheet" href="/donapetit3-main/app/view/css/registro_style.css">