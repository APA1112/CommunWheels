document.addEventListener('DOMContentLoaded', function () {
    const expandableLinks = document.querySelectorAll('.expandable');
    const contents = document.querySelectorAll('.content');
    let expandedContent = null;

    expandableLinks.forEach((link, index) => {
        link.addEventListener('click', function (e) {
            // Excluir los clics en los botones de edición y eliminación
            if (e.target.closest('.btn')) {
                return;
            }

            e.preventDefault();

            if (expandedContent === contents[index]) {
                contents[index].classList.remove('expanded');
                link.classList.remove('expanded');
                expandedContent = null;
            } else {
                if (expandedContent) {
                    expandedContent.classList.remove('expanded');
                    expandedContent.previousElementSibling.classList.remove('expanded');
                }

                contents[index].classList.add('expanded');
                link.classList.add('expanded');
                expandedContent = contents[index];
            }
        });
    });
});
