document.addEventListener("DOMContentLoaded", () => {
  const tabButtons = document.querySelectorAll(".tab-btn");
  const tabContents = document.querySelectorAll(".tab-content");
 
  tabButtons.forEach(btn => {
    btn.addEventListener("click", () => {
      const target = btn.dataset.tab;
 
      // Quitar "active" de todos los botones y contenidos
      tabButtons.forEach(b => b.classList.remove("active"));
      tabContents.forEach(c => c.classList.remove("active"));
 
      // Activar el botón y contenido correspondiente
      btn.classList.add("active");
      document.getElementById(target).classList.add("active");
    });
  });
});
 