document.addEventListener('DOMContentLoaded', function() {
    const dropdownUser = document.querySelector('.dropdown-user');

    if (dropdownUser) {
        const dropdownLink = dropdownUser.querySelector('a');

        dropdownLink.addEventListener('click', function(e) {
            if (window.innerWidth <= 820) {
                e.preventDefault();
                dropdownUser.classList.toggle('active');
            }
        });

        document.addEventListener('click', function(e) {
            if (!dropdownUser.contains(e.target) && dropdownUser.classList.contains('active')) {
                dropdownUser.classList.remove('active');
            }
        });
    }
});