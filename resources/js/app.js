import './bootstrap';
import Swal from 'sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css' // (ตัวเลือก) ถ้าต้องการแยกไฟล์ CSS


import Alpine from 'alpinejs';

window.Alpine = Alpine;
window.Swal = Swal

Alpine.start();
