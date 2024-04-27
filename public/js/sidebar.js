document.addEventListener("DOMContentLoaded", main);

function main(){
    const sidebar = document.querySelector('.sidebar')
    const openSidebar = document.querySelector('.navBar li:last-child a')
    const closeSidebar = document.querySelector('li:first-child a')

    openSidebar.addEventListener('click', function () {
        sidebar.style.display = 'flex'
    })
    closeSidebar.addEventListener('click', function () {
        sidebar.style.display = 'none'
    })
}