import './bootstrap';
import Swal from 'sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';

import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import 'datatables.net-responsive-dt';

window.$ = window.jQuery = $;
DataTable.use($);
window.DataTable = DataTable;

import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.Swal = Swal;

Alpine.start();

