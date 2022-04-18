import './styles/app.scss';

import {Sidebar} from '@coreui/coreui';

document.addEventListener("DOMContentLoaded", function(event) {
    var sidebarToggleButton = document.getElementById('sidebar-toggle');

    if (sidebarToggleButton) {
        sidebarToggleButton.addEventListener('click', function () {
            Sidebar.getInstance(document.getElementById('sidebar')).toggle();
        });
    }
});
