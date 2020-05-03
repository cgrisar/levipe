Vue.config.devtools=true

import Vue from 'vue'
import swal from 'sweetalert'
import VModal from 'vue-js-modal'
import axios from 'axios'
import VueAxios from 'vue-axios'
import InstantSearch from 'vue-instantsearch'
import StarRating from 'vue-star-rating'
import VueAnalytics from 'vue-analytics'

import lvpVariant from './components/lvp-variant.vue'
import lvpNoVariant from './components/lvp-novariant.vue'
import lvpCart from './components/cart/lvp-cart.vue'
import lvpMinicart from './components/cart/lvp-minicart.vue'
import autocompleteSearch from './components/search/autocomplete-search.vue'
import pb from './components/miscellaneous/pb.vue'
import pbs from './components/miscellaneous/pbs.vue'
import lightbox from './components/miscellaneous/lightbox.vue'
import wineandfood from './components/miscellaneous/wineandfood.vue'
import carousel from './components/miscellaneous/carousel.vue'
import smalltabs from './components/miscellaneous/smalltabs.vue'

Vue.use(VModal);
Vue.use(VueAxios, axios)
Vue.use(InstantSearch);
Vue.use(VueAnalytics, { id:'UA-43848540-3'});


new Vue({
    el: '#app',

    data() {
        return {
            rating: 3
        }
    },
    
    components: {   swal, 
                    lvpVariant,
                    lvpNoVariant, 
                    lvpCart, 
                    lvpMinicart, 
                    StarRating,
                    carousel,
                    smalltabs,
                    autocompleteSearch,
                    lightbox,
                    wineandfood,
                    pb,
                    pbs
                }
});