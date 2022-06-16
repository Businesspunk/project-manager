import './styles/app.scss';
import {Sidebar} from '@coreui/coreui';
import 'bootstrap-datepicker';

var $ = require( "jquery" );

document.addEventListener("DOMContentLoaded", function(event) {
    var sidebarToggleButton = document.getElementById('sidebar-toggle');

    if (sidebarToggleButton) {
        sidebarToggleButton.addEventListener('click', function () {
            Sidebar.getInstance(document.getElementById('sidebar')).toggle();
        });
    }
});

$(document).ready(function() {
    $('.js-datepicker').datepicker({
        format: 'dd-mm-yyyy'
    });
});