import Vue from 'vue'
import lvpVariant from './components/lvp-variant.vue'

Vue.component('star-rating', VueStarRating.default);

let store = {
  cart: ( sessionStorage.cart ) ? JSON.parse( sessionStorage. cart ) : new Array,
};

new Vue({

    el: '#app',
    components: { lvpVariant },
    watch: {
        cart: function() {
            sessionStorage.cart = JSON.stringify( this.cart )
        }
    }

});
