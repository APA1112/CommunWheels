document.addEventListener("DOMContentLoaded", main);

function main(){
    const sidebar = document.querySelector('.sidebar');
    const openSidebar = document.querySelector('.navbar .menuButton a');
    const closeSidebar = document.querySelector('.sidebar li:first-child a');

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
}
