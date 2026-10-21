const btnEquipamiento = document.getElementById('btnEquip');
const btnUbicacion = document.getElementById('btnUbicacion');
const btnPanel = document.getElementById('btnPanel');
const btnNotificaciones = document.getElementById("btnNotificaciones");
const btnPerfil = document.getElementById("btnPerfil");

btnEquipamiento.addEventListener('click', () => {
  window.location.href = '../pages/equipamientos.html';
});

btnUbicacion.addEventListener('click', () => {
  window.location.href = '../pages/ubicaciones.html';
});

btnPanel.addEventListener('click', () => {
  window.location.href = '../pages/usuarios.html';
});

btnNotificaciones.addEventListener('click', () => {
  window.location.href = '../pages/notificaciones.html';
});

btnPerfil.addEventListener('click', () => {
  window.location.href = '../pages/perfil.html';
});