Vue.config.devtools=false
Vue.config.debug=false
Vue.config.silent=true

import Vue from 'vue'
import swal from 'sweetalert'
import VModal from 'vue-js-modal'
import InstantSearch from 'vue-instantsearch'
import VueAnalytics from 'vue-analytics'

import lvpMinicart from './components/cart/lvp-minicart.vue'
import autocompleteSearch from './components/search/autocomplete-search.vue'
import carousel from './components/miscellaneous/carousel.vue'
import smalltabs from './components/miscellaneous/smalltabs.vue'

Vue.use(VModal);
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
                    lvpMinicart,
                    carousel,
                    smalltabs,
                    autocompleteSearch,
                }
});