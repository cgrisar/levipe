<template>
    <div class="container flex flex-col mx-auto my-4 mt-2 lg:flex-row">
        <template v-if="cart.length > 0">

            <div class="w-full lg:w-3/5">
                <div class="flex flex-col">
                    <lvp-cart-line  v-for="variant in cart" 
                                    v-bind:key="variant.id" 
                                    v-bind:variant="variant"
                                    @removed="removeCartLine"
                                    @quantityChanged="changeQuantity"
                                    class="text-grey-darker mb-1">
                    </lvp-cart-line>
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
                {{ cart }}
                </div>
            </div>
        </template>
    </div>
</template>

<script>

import lvpCartLine from './lvp-cart-line.vue'

export default {
    components: { lvpCartLine },
    
    data() {
        return {
            total: {
                amount: 0,
                bottles: 0
            },
            cart: ( sessionStorage.cart ) ? JSON.parse( sessionStorage. cart ) : new Array
        }
    },

    methods:{
        removeCartLine()
        {
            alert(this);
            i = cart.findIndex(element => element.variantId === this.variantId);
            cart.splice(i, 1);
            sessionStorage.cart = JSON.stringify(cart)
        },

        changeQuantity(variant)
        {

        }
    },


    watch: {
        cart: function() 
        {
            sessionStorage.cart = JSON.stringify( this.cart )
        }
    }
}
</script>

<style>

</style>
