document.addEventListener("DOMContentLoaded", main);

function main(){
    const sidebar = document.querySelector('.sidebar');
    const openSidebar = document.querySelector('.navbar .menuButton a');
    const closeSidebar = document.querySelector('.sidebar li:first-child a');
    const userDropdown = document.getElementById('userDropdown');
    const dropdownContent = document.querySelector('.dropdown-content');
    const currentPath = window.location.pathname;
    const links = document.querySelectorAll('.navbar a');

    // Asegurarse de que el sidebar esté oculto al cargar la página
    sidebar.style.display = 'none';

    openSidebar.addEventListener('click', function (event) {
        event.preventDefault();
        sidebar.style.display = 'flex';
    });

    closeSidebar.addEventListener('click', function (event) {
        event.preventDefault();
        sidebar.style.display = 'none';
    });

    userDropdown.addEventListener('click', function(event) {
        event.preventDefault();
        if (dropdownContent.style.maxHeight === '0px' || dropdownContent.style.maxHeight === '') {
            dropdownContent.style.maxHeight = '200px'; // Ajusta esto según el contenido del menú
        } else {
            dropdownContent.style.maxHeight = '0px';
        }
    });

    // Cierra el menú desplegable si se hace clic en cualquier otro lugar de la página
    window.addEventListener('click', function(event) {
        if (!event.target.matches('#userDropdown')) {
            dropdownContent.style.maxHeight = '0px';
        }
    });

    // Añadir clase 'active' solo a los enlaces de Grupos, Usuarios y Notificaciones
    links.forEach(link => {
        if (link.href.includes(currentPath) && (link.href.includes('group_main') || link.href.includes('driver_main') || link.href.includes('notify_main'))) {
            link.classList.add('active');
        }
    });
}
