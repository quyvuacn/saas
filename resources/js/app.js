require('./bootstrap');
// require('./objectToFormData');

window.Vue = require('vue');

import { Form, HasError, AlertError } from 'vform'
import objectToFormData from "./objectToFormData";
window.objectToFormData = objectToFormData;

Vue.component(HasError.name, HasError);
Vue.component(AlertError.name, AlertError);

import Swal from 'sweetalert2';
import flatpickr from "flatpickr";
import moment from "moment";
import select2 from "select2";
import { Vietnamese } from "flatpickr/dist/l10n/vn.js"
// import * as FilePond from 'filepond';

window.flatpickr = flatpickr;
window.moment = moment;
window.Vietnamese = Vietnamese;
window.select2 = select2;

window.Swal = Swal;
window.From = Form;
const Toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000,
});

window.Toast = Toast;
// const files = require.context('./', true, /\.vue$/i)
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default))

Vue.component('merchant-component', require('../../modules/merchant/resources/components/CreateRechargeComponent.vue').default);
// Vue.component('fileupload-component', require('../../modules/merchant/resources/components/UploadImageComponent.vue').default);
Vue.component('product-create', require('../../modules/merchant/resources/components/ProductCreateComponent.vue').default);
Vue.component('product-edit', require('../../modules/merchant/resources/components/ProductEditComponent.vue').default);

Vue.component('ads-create', require('../../modules/merchant/resources/components/AdsCreateComponent.vue').default);
Vue.component('ads-edit', require('../../modules/merchant/resources/components/AdsEditComponent.vue').default);

const app = new Vue({
    el: '#app',
});
