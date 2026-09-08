// public/assets/js/app-global.js

// 1. Configuración global de Toast
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.addEventListener('mouseenter', Swal.stopTimer);
        toast.addEventListener('mouseleave', Swal.resumeTimer);
    }
});

// 2. Idioma por defecto para DataTables
const dtLanguageEs = {
    "url": "//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json"
};

// 3. Manejo global de foco en modales Bootstrap
$(document).on('show.bs.modal hide.bs.modal', '.modal', function () {
    if (document.activeElement) {
        document.activeElement.blur();
    }
});