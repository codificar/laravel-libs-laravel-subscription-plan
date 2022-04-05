require('lodash');
import Vue from "vue";

Vue.config.productionTip = false

import Vuelidate from 'vuelidate';
Vue.use(Vuelidate);

import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
Vue.use(VueSweetalert2);

import pagination from "laravel-vue-pagination";
Vue.component("pagination", pagination);

import SignatureList from "./pages/signature/signature_list.vue";
Vue.component("SignatureList", SignatureList);

import PlanList from "./pages/plan/plan_list.vue";
Vue.component("PlanList", PlanList);

import PlanEdit from "./pages/plan/plan_edit.vue";
Vue.component("PlanEdit", PlanEdit);

import PlansGrid from "./provider_panel/components/PlansGrid.vue";
Vue.component("PlansGrid", PlansGrid);

import CheckoutPlan from "./provider_panel/components/CheckoutPlan.vue";
Vue.component("CheckoutPlan", CheckoutPlan);

import ProviderCreditcard from "./provider_panel/components/ProviderCreditCard.vue";
Vue.component("ProviderCreditcard", ProviderCreditcard);

//Allows localization using trans()
Vue.prototype.trans = (key) => {
  return _.get(window.lang, key, key);
};

Vue.prototype.$eventBus = new Vue()

new Vue({
  el: "#VueJs",
  created: () => console.log('Signature Lib Created'),
});