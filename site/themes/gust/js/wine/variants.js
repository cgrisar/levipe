Vue.component('lvp-variant', {
    props: [ 'wine','vintage', 'volume', 'price', 'variantId', 'quantity', 'bo', 'gof', 'bogof_discount', 'bogof_title' ],

    data() {
        return {
            ordered: 1,
            total_gof: 0,
            cart: store.cart, 
        }   
    },

    template: ` <div :id="variantId" class="container flex flex-row">
                    <div class="w-1/5 pt-2 text-right">{{ volume }}</div>
                    <div class="w-1/5 pt-2 font-bold text-right ml-2">{{ price }} &euro;</div>
                    <template v-if="quantity > 0">
                        <div class="w-2/5 justify-center ml-2 flex flex-row" style="min-width:6rem;">
                            <button class="mr-4 lg:mr-0" @click="decVariant( $event )">
                                <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse pt-1" data-fa-transform="shrink-6"></i>
                            </button>
                            <input type="number" min="1" :max="quantity" class="w-8 text-right pr-1 rounded-sm" v-model="ordered" @focus="getVariantFromCart" />
                            <button class="ml-4 lg:ml-0" @click="incVariant( $event )">
                                <i class="fas fa-2x text-red-darker fa-plus-circle fa-inverse pt-1" data-fa-transform="shrink-6"></i>
                            </button>
                        </div>
                        <div class="w-1/5 flex flex-row">
                            <div class="w-1/2">
                                <template v-if="total_gof > 0">
                                    <span class="fa-stack -mb-4">
                                        <i class="fas fa-certificate fa-stack-2x text-green-darker" data-fa-transform="shrink-4"></i>
                                        <strong class="fa-stack-1x text-white text-sm font-semibold" style="padding-left: .90rem; padding-top: .25rem;">{{ total_gof }}</strong>
                                    </span>
                                </template>
                                <template v-if="( total_gof == 0 ) && ( gof > 0 )">
                                    <span class="fa-stack -mb-4" :title="bogof_title">
                                        <i class="fas fa-certificate fa-stack-2x text-green-darker" data-fa-transform="shrink-4"></i>
                                        <strong class="fa-stack-1x text-white text-sm font-semibold" style="padding-left: .98rem; padding-top: .25rem;">!</strong>
                                    </span>
                                </template>
                            </div>
                            <div class="w-1/2">
                                <button @click="addVariantToCart">
                                    <i class="fas fa-2x text-red-darker fa-cart-plus fa-inverse pt-2 text-right" data-fa-transform="shrink-6"></i>
                                </button>
                            </div>
                        </div>
                    </template>
                    <template v-else>
                    <div class="w-3/5 text-center ml-2 mt-2" style="min-width:6rem;">
                        <span class="bg-red-darker rounded-full text-white font-semibold uppercase text-xs px-2 py-1">Out of stock</span>
                    </div>
                </template>
                </div>`,

    methods: {

        calculateTotalGof() {
            return ( this.bo > 0 ) ? Math.floor( this.ordered / this.bo ) * this.gof : 0;
        },

        getVariantFromCart() {
            // if this product is in the shopping cart, then put its quantity in the quantity field
            this.cart.some( element => {
                if ( found = ( element.variantId === this.variantId ) ) {
                    this.ordered = element.ordered;
                }
            } );
        },

        addVariantToCart() {

            // sanitation
            this.ordered = Math.max( 1, this.ordered );
            this.total_gof = this.calculateTotalGof();
            maxquantity = ( this.bo  > 0 ) ? Math.floor( this.quantity / this.bo ) * this.gof : this.quantity;
            this.ordered = Math.min( this.ordered, maxquantity );

            var found = false ;

            if ( this.cart.length ) {

                // parse through the array and add to variantId if it is already present
                this.cart.some( element => {
                    if ( found = ( element.variantId === this.variantId ) ) {
                        element.ordered = Number( this.ordered );
                        element.gof = Number( this.total_gof );
                    };
                    return found;
                })
            };

            // add an element to the array if it is not found
            if ( !found ) {
                this.cart.push( {   'wine': this.wine,
                                    'variantId': this.variantId,
                                    'vintage': this.vintage, 
                                    'volume': this.volume, 
                                    'price': this.price,
                                    'quantity': this.quantity,
                                    'ordered': Number( this.ordered ),
                                    "total_gof": Number(this.total_gof)
                                } );
            };

            sessionStorage.cart = JSON.stringify( this.cart );
        },

        decVariant(e) {
            if ( this.ordered > 1 ) {
                this.ordered--;
                this.total_gof = this.calculateTotalGof();
                sessionStorage.cart = JSON.stringify( this.cart );
            }
        },

        incVariant(e) {
            if ( (this.ordered + this.total_gof ) < this.quantity ) {
                this.ordered++;
                this.total_gof = this.calculateTotalGof();
                sessionStorage.cart = JSON.stringify( this.cart );
            }
        }
    },
});