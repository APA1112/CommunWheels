document.addEventListener("DOMContentLoaded", main);

function main(){
    const sidebar = document.querySelector('.sidebar');
    const openSidebar = document.querySelector('.navbar .menuButton a');
    const closeSidebar = document.querySelector('.sidebar li:first-child a');
    const userDropdowns = document.querySelectorAll('.userDropdown');
    const dropdownContents = document.querySelectorAll('.dropdown-content');
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

    userDropdowns.forEach((dropdown, index) => {
        dropdown.addEventListener('click', function(event) {
            event.preventDefault();
            event.stopPropagation(); // Detiene la propagación del evento
            const dropdownContent = dropdownContents[index];
            const isOpen = dropdownContent.style.maxHeight === '200px';
            dropdownContent.style.maxHeight = isOpen ? '0px' : '200px';
            dropdown.classList.toggle('open', !isOpen); // Alternar la clase open
        });
    });

    // Cierra el menú desplegable si se hace clic en cualquier otro lugar de la página
    window.addEventListener('click', function(event) {
        userDropdowns.forEach((dropdown, index) => {
            const dropdownContent = dropdownContents[index];
            if (!event.target.closest('.userDropdown')) {
                dropdownContent.style.maxHeight = '0px';
                dropdown.classList.remove('open'); // Asegurarse de que el icono vuelva a su posición original
            }
        });
    });

    // Añadir clase 'active' solo a los enlaces de Grupos, Usuarios y Notificaciones
    links.forEach(link => {
        const linkPath = new URL(link.href).pathname;
        if (linkPath === currentPath && (linkPath.includes('group_main') || linkPath.includes('driver_main') || linkPath.includes('notify_main'))) {
            link.classList.add('active');
        }
    });
}
