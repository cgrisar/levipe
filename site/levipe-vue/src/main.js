import Vue from 'vue'
import lvpVariant from './components/lvp-variant.vue'
import lvpCart from './components/cart/lvp-cart.vue'
import lvpMinicart from './components/cart/lvp-minicart.vue'

Vue.component('star-rating', VueStarRating.default);

new Vue({
    el: '#app',
    components: { lvpVariant, lvpCart, lvpMinicart }
});
