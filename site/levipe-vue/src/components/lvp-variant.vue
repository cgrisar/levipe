<template>
    <div :id="variantId" class="container flex flex-row items-center px-2 py-1">
        <div class="text-right pr-1" style="flex-grow:1;">{{ volume }}</div>
        <div class="font-bold text-right px-1" style="flex-grow:2;">{{ price }} &euro;</div>
        
        <template v-if="quantity > 0">
            <div class="justify-center px-1 flex flex-row" style="flex-grow:3">
                <button class="mr-4 lg:mr-0" @click="decVariant( $event )">
                    <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse" data-fa-transform="shrink-4"></i>
                </button>
                <input type="number" min="1" 
                                    :max="maxquantity" 
                                    class="w-8 text-right mx-1 pr-1 rounded-sm" 
                                    v-model="ordered" 
                                    @focus="getVariantFromCart" 
                                    @blur="setVariant" />
                <button class="ml-4 lg:ml-0" @click="incVariant( $event )">
                    <i class="fas fa-2x text-red-darker fa-plus-circle fa-inverse" data-fa-transform="shrink-4"></i>
                </button>
            </div>

            <div class="flex flex-row pl-1" style="flex-grow:4;">
                <div class="flex w-1/2">
                    <template v-if="total_gof > 0">
                        <span class="fa-fw fa-layers fa-2x" :key="total_gof">
                            <i class="fas fa-certificate fa-stack-2x text-green-darker" data-fa-transform="shrink-8 left-6"></i>
                            <span class="fa-layers-text text-white font-semibold" data-fa-transform="shrink-7 left-2.25">{{ total_gof }}</span>
                        </span>
                    </template>
                    
                    <template v-if="( total_gof === 0 ) && ( gof > 0 )">
                        <span class="fa-fw fa-layers fa-2x" :title="bogof_title" key="euro">
                            <i class="fas fa-certificate fa-stack-2x text-green-darker" data-fa-transform="shrink-8 left-6"></i>
                            <i class="fas fa-euro-sign text-white" data-fa-transform="shrink-7 left-2.25"></i>
                        </span>
                    </template>
                </div>
                
                <div class="w-1/2">
                    <button @click="addVariantToCart">
                        <span class="fa-fw fa-layers fa-2x text-right">
                            <i class="fas fa-circle text-red-darker"></i>
                            <i class="fas fa-cart-plus text-white" data-fa-transform="shrink-8 left-0.25"></i>
                        </span>
                    </button>
                </div>
            </div>
        </template>

        <template v-else>
            <div class="w-3/5 text-center ml-2 text-right" style="min-width:6rem;">
                <span   class="bg-red-darker rounded-full text-white font-semibold uppercase text-xs px-2" 
                        style="padding-top:.125rem; padding-bottom:.125rem;">Out of stock</span>
            </div>
        </template>
    </div>
</template>

<script>

    export default {
        props: ['wine',
                'vintage', 
                'volume', 
                'price', 
                'variantId', 
                'quantity', 
                'bo', 
                'gof', 
                'bogof_discount', 
                'bogof_title' ],

        data() {
            return {
                ordered: 1,
                total_gof: 0,
                cart: store.cart,
                maxquantity: (this.bo  > 0) ? this.bo * Math.floor(this.quantity / (parseInt(this.bo) + parseInt(this.gof))) : this.quantity
            }   
        },

        methods: 
        {
            getVariantFromCart() 
            {
                // if this product is in the shopping cart, then put its quantity in the quantity field
                this.cart.some(element => 
                {
                    if (element.variantId === this.variantId) 
                    {
                        this.ordered = element.ordered;
                    }
                });
            },

            addVariantToCart() 
            {
                // sanitation
                this.ordered = Math.max( 1, this.ordered );
                this.total_gof = this.calculateTotalGof();
                this.ordered = Math.min( this.ordered, this.maxquantity );

                let found = false ;

                if ( this.cart.length ) 
                {
                    // parse through the array and add to variantId if it is already present
                    this.cart.some(element => 
                    {
                        if (found = (element.variantId === this.variantId)) 
                        {
                            element.ordered = Number( this.ordered );
                            element.gof = Number( this.total_gof );
                        }
                        return found;
                    })
                }

                // add an element to the array if it is not found
                if (!found) 
                {
                    this.cart.push({'wine': this.wine,
                                    'variantId': this.variantId,
                                    'vintage': this.vintage, 
                                    'volume': this.volume, 
                                    'price': this.price,
                                    'bo': this.bo,
                                    'gof': this.gof,
                                    'discount': this.bogof_discount,
                                    'quantity': this.quantity,
                                    'ordered': Number(this.ordered),
                                    'total_gof': Number(this.total_gof),
                                    'total_ordered': Number(this.ordered) + Number(this.total_gof)
                                    });
                 }

                sessionStorage.cart = JSON.stringify( this.cart );
            },

            calculateTotalGof() {
                return ( this.bo > 0 ) ? Math.floor( this.ordered / this.bo ) * this.gof : 0;
            },

            decVariant() {
                if (this.ordered > 1) {
                    this.ordered--;
                    this.total_gof = this.calculateTotalGof();
                    sessionStorage.cart = JSON.stringify( this.cart );
                }
            },

            incVariant() {
                if (this.ordered < this.maxquantity) {
                    this.ordered++;
                    this.total_gof = this.calculateTotalGof();
                    sessionStorage.cart = JSON.stringify( this.cart );
                }
            },

            setVariant() {
                if (this.ordered < 1) {
                    this.ordered = 1;
                }
                
                if (this.ordered > this.maxquantity) {
                    this.ordered = this.maxquantity
                }

                this.total_gof = this.calculateTotalGof();
            },

        },
    }
</script>