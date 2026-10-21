$(document).ready(function () {
  // El toggle del sidebar (assets/js/main.js) dispara $(window).resize()
  // apenas se hace click, pero .main-panel tarda 300ms en terminar su
  // transición de ancho (transition: all 0.3s, ver assets/css/main.css).
  // DataTables recalcula el ancho de las columnas con ese resize inmediato,
  // usando el ancho "a mitad de camino" de la animación, y las tablas
  // visibles quedan con las columnas desalineadas. Se soluciona repitiendo
  // el resize una vez terminada la transición.
  $('.toggle-sidebar').on('click', function () {
    setTimeout(function () {
      $(window).trigger('resize');
    }, 350);
  });
});
