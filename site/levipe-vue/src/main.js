Vue.config.devtools=true

import Vue from 'vue'
import swal from 'sweetalert'
import VModal from 'vue-js-modal'
import axios from 'axios'
import VueAxios from 'vue-axios'
import InstantSearch from 'vue-instantsearch'
import StarRating from 'vue-star-rating'

import lvpVariant from './components/lvp-variant.vue'
import lvpCart from './components/cart/lvp-cart.vue'
import lvpMinicart from './components/cart/lvp-minicart.vue'
import autocompleteSearch from './components/search/autocomplete-search.vue'
import pushbutton from './components/search/pushbutton.vue'
import lightbox from './components/miscellaneous/lightbox.vue'
import wineandfood from './components/miscellaneous/wineandfood.vue'

Vue.use(VModal);
Vue.use(VueAxios, axios)
Vue.use(InstantSearch);


new Vue({
    el: '#app',

    data() {
        return {
            rating: 3
        }
    },
    
    components: {   swal, 
                    lvpVariant, 
                    lvpCart, 
                    lvpMinicart, 
                    StarRating, 
                    autocompleteSearch, 
                    pushbutton, 
                    lightbox,
                    wineandfood }
});