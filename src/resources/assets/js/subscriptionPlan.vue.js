require('lodash');
import Vue from "vue";

Vue.config.productionTip = false

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);

Vue.component('pagination', require('laravel-vue-pagination'));
Vue.component('SignatureList', require('./pages/signature/signature_list.vue'));
Vue.component('PlanList', require('./pages/plan/plan_list.vue'));
Vue.component('PlanEdit', require('./pages/plan/plan_edit.vue'));

Vue.component('PlansGrid', require('./provider_panel/components/PlansGrid.vue'));
Vue.component('CheckoutPlan', require('./provider_panel/components/CheckoutPlan.vue'));
Vue.component('ProviderCreditcard', require('./provider_panel/components/ProviderCreditCard.vue'));

// Vue.component('SetDestinationRoute', require('./provider_panel/components/SetDestinationRoute.vue'));

//Allows localization using trans()
Vue.prototype.trans = (key) => {
  return _.get(window.lang, key, key);
};

Vue.prototype.$eventBus = new Vue()

new Vue({
  el: "#VueJs",
  created: () => console.log('Signature Lib Created'),
});