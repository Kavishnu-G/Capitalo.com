const sidebar = document.querySelector('.sidebar');
const toggleArrow = document.querySelector('#toggle-arrow');
const mainContent = document.querySelector('.main-content');

toggleArrow.addEventListener('click', () => {
    sidebar.classList.toggle('collapsed');
    mainContent.classList.toggle('expanded');
    toggleArrow.classList.toggle('fa-arrow-right');
    toggleArrow.classList.toggle('fa-arrow-left');
});
