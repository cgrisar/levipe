let store = {
    cart: ( sessionStorage.cart ) ? JSON.parse( sessionStorage. cart ) : new Array,
};

Vue.component('lvp-cart-counter', {
    data() {
        return {
            cart: store.cart,
        }
    },

    template: ` 
        <span class="rounded-full bg-red-dark font-semibold text-white text-xs mx-auto px-1 relative ml-6 -mt-2">
            {{ bottlesInCart }}
        </span>
        `,

    computed: {
        bottlesInCart() {
            var bottles = 0;
            this.cart.forEach( element => {
                bottles += element.ordered
            });
            return bottles;
        },
    }
});

Vue.component('lvp-minicart', {
    data() {
        return {
            cart: store.cart,
        }
    },

    template: `
        <div class="px-4 py-1 z-10 mt-8 rounded-lg" style="left:auto; right:0; background:linear-gradient(rgba(0, 0, 0, .15), rgba(0, 0, 0, 0.15))">
            <div v-for="variant in cart" class="flex text-black mb-1">
                <div class="inline w-32 md:w-48 mr-2">
                    <div class="font-medium -mb-1">{{ variant.wine }}</div>
                    <div class="text-xs italic">
                        <span class="mr-2">{{ variant.vintage }}</span>
                        <span>{{ variant.volume }}</span>
                    </div>
                </div>
                <div class="inline w-8 font-medium text-right float-right mr-2">{{ variant.ordered }}</div>
                <div class="inline w-16 font-medium text-right float-right">{{ orderedAmountVariant( variant ) }} &euro;</div>
            </div>
            <div class="flex text-black mt-2">
                <div class="w-1/2 font-semibold">Total</div>
                <div class="w-1/2 font-semibold text-right">{{ totalAmountCart }} &euro;</div>
            </div>
        </div>
    `,

    methods: {
        orderedAmountVariant( variant ) {
            return ( variant.ordered * Number( variant.price.replace(',','.') ) ).toFixed(2).replace('.',',')
        }
    },

    computed: {
        totalAmountCart() {
            var amount = 0;
            this.cart.forEach( element => {
                amount += element.ordered * Number( element.price.replace(',','.') )
            });
            return amount.toFixed(2).replace('.',',');
        }
    }
});

Vue.component('lvp-cart', { 
    props: [ 'subtotal' ],

    data() {
        return {
            cart: store.cart,
        }
    },

    template: `
        <div class="container flex flex-col mx-auto my-4 mt-2 lg:flex-row">
            <template v-if="cart.length > 0">

                <div class="w-full lg:w-3/5">
                    <div class="flex flex-col">
                            <div v-for="variant in cart" class="text-grey-darker mb-1">
                            
                                <div :id="variant.variantId" class="container flex flex-row">
                                    <div class="inline w-12">
                                        <button @click="removeVariant( variant )">
                                            <i class="far fa-2x fa-times-circle" data-fa-transform="shrink-4"></i>
                                        </button>
                                    </div>
                                    <div class="inline w-1/4 md:w-64 mr-2">
                                        <div class="font-medium -mb-1">{{ variant.wine }}</div>
                                        <div class="text-xs italic">
                                            <span class="mr-2">{{ variant.vintage }}</span>
                                            <span>{{ variant.volume }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="w-1/8 font-bold text-right ml-2">{{ variant.price }} &euro;</div>
                                    
                                    <div class="w-1/6 text-right align-top ml-2">
                                        <button @click="decVariant( variant )">
                                            <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse pb-2" data-f-transform="shrink-8"></i>
                                        </button>
                                        <input type="number" min="1" class="w-8 text-right pr-1 text-grey-darker bg-grey-lighter rounded-sm align-top" v-model="variant.ordered" />
                                        <button @click="incVariant( variant )">
                                            <i class="fas fa-2x text-green-darker fa-plus-circle fa-inverse pb-2 ml-1" data-f-transform="shrink-8"></i>
                                        </button>
                                    </div>

                                    <div class="w-1/6 text-right font-medium">{{ orderedAmountVariant( variant ) }} &euro;</div>

                                </div>

                            </div>

                    </div>
                </div>

                <div class="flex flex-1 justify-end mt-4 lg:mt-0">
                    <div class="w-1/2">
                        <h5 class="mt-0 text-grey-darker">
                            {{ subtotal }}
                        </h5>
                    </div>
                    <div class="w-1/2 text-right">
                        <h5 class="mt-0 text-grey-darker">
                            {{ totalAmountCart }} &euro;
                        </h5>
                    </div>
                </div>

            </template>

            <template v-else>
                <div class="flex container justify-center">
                    <div class="text-white bg-red-darker px-2 py-1 rounded font-normal">
                    The Cart is Empty
                    </div>
                </div>
            
            </template>
        </div>
    `,


    methods: {
        calculateTotalGof( variant ) {
            return ( variant.bo > 0 ) ? Math.floor( variant.ordered / variant.bo ) * variant.gof : 0;
        },

        decVariant( variant ) {
            if ( variant.ordered > 1 ) {
                variant.ordered--;
                variant.total_gof = this.calculateTotalGof( variant );
                sessionStorage.cart = JSON.stringify( this.cart )
            }
        },

        incVariant( variant ) {
            if ( (variant.ordered + variant.total_gof ) < variant.quantity ) {
                variant.ordered++;
                variant.total_gof = this.calculateTotalGof( variant );
                sessionStorage.cart = JSON.stringify( this.cart )
            }
        },

        removeVariant( variant ) {
            i = this.cart.findIndex( element => element.variantId === variant.variantId );
            this.cart.splice( i, 1 );
            sessionStorage.cart = JSON.stringify( this.cart )
        },

        orderedAmountVariant( variant ) {
            return ( variant.ordered * Number( variant.price.replace(',','.') ) ).toFixed(2).replace('.',',')
        }
    },

    computed: {
        totalAmountCart() {
            var amount = 0;
            this.cart.forEach( element => {
                amount += element.ordered * Number( element.price.replace(',','.') )
            });
            return amount.toFixed(2).replace('.',',');
        }
    }

});