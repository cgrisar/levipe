Vue.config.devtools=true

import Vue from 'vue'
import lvpVariant from './components/lvp-variant.vue'
import lvpCart from './components/cart/lvp-cart.vue'
import lvpMinicart from './components/cart/lvp-minicart.vue'
import StarRating from 'vue-star-rating'

new Vue({
    el: '#app',

    data() {
        return {
            rating: 2
        }
    },
    
    components: { lvpVariant, lvpCart, lvpMinicart, StarRating }
});