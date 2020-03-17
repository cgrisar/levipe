<template>
    <div class="container flex flex-row items-center px-2 py-1">
        <div class="font-bold text-right px-1" style="flex-grow:2;">{{ price }} &euro;</div>

            <div class="justify-center px-1 flex flex-row" style="flex-grow:3">
                <button class="mr-4 lg:mr-0" @click="decVariant( $event )">
                    <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse" data-fa-transform="shrink-4"></i>
                </button>
                <input type="number" min="1" 
                                    :max="8" 
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

    </div>
</template>

<script>

    export default {
        props: ['product',
                'price'],

        data() {
            return {
                ordered: 1,
                total_gof: 0,
                store,
                maxquantity: (this.bo  > 0) ? this.bo * Math.floor(this.quantity / (parseInt(this.bo) + parseInt(this.gof))) : this.quantity
            }   
        },

        methods: 
        {
            getVariantFromCart() 
            {
                // if this product is in the shopping cart, then put its quantity in the quantity field
                store.cart.cartLines.some(element => 
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

                if ( store.cart.cartLines.length ) 
                {
                    // parse through the array and add to variantId if it is already present
                    store.cart.cartLines.some(element => 
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
                    store.cart.cartLines.push({'wine': this.product,
                                                'variantId': 0,
                                                'vintage': 0, 
                                                'volume': 0, 
                                                'price': this.price,
                                                'bo': 0,
                                                'gof': 0,
                                                'discount': 0,
                                                'quantity': 8,
                                                'ordered': Number(this.ordered),
                                                'total_gof': 0,
                                                'total_ordered': Number(this.ordered)
                                            });
                 }

                sessionStorage.cart = JSON.stringify(store.cart);
            },

            decVariant() {
                if (this.ordered > 1) {
                    this.ordered--;
                    this.total_gof = this.calculateTotalGof();
                }
            },

            incVariant() {
                if (this.ordered < this.maxquantity) {
                    this.ordered++;
                    this.total_gof = this.calculateTotalGof();
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