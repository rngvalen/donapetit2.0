</main>

  <!-- Pie de página del sitio -->
  <footer class="mt-auto w-full bg-slate-900 text-slate-100">
    <div class="mx-auto flex max-w-6xl flex-col gap-2 px-4 py-6 text-sm sm:flex-row sm:items-center sm:justify-between">
      <!-- Texto de copyright con el año actual -->
      <span>&copy; <?php echo date('Y'); ?> DonAppétit</span>
      <!-- Enlaces de contacto, privacidad y ayuda -->
      <div class="flex items-center gap-4 opacity-80">
        <a href="?controller=Info&action=contacto" class="hover:opacity-100 transition">Contacto</a>
        <a href="?controller=Info&action=privacidad" class="hover:opacity-100 transition">Privacidad</a>
        <a href="?controller=Info&action=ayuda" class="hover:opacity-100 transition">Ayuda</a>
      </div>
    </div>
  </footer>

  <!-- Script principal de la aplicación, cargado de forma diferida -->
  <script defer src="assets/js/principal.js"></script>
</body>
</html>