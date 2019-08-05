<template>
    <div class="flex flex-col mx-auto py-2 bg-grey-light rounded-lg">
        <template v-if="cart.length > 0">
            <lvp-cart-line  v-for="variant in cart" 
                            v-bind:key="variant.id" 
                            v-bind:variant="variant"
                            @removed="removeCartLine($event)"
                            @quantity="changeQuantity($event)">
            </lvp-cart-line>
        </template>

        <template v-else>
            <div class="flex container justify-center">
                <div class="text-white bg-red-darker px-2 py-1 rounded font-normal">
                The Cart is Empty
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
            cart: store.cart.cartLines
        }
    },

    methods:{

        calculateShippingCost() {
            if (this.cart.delivery !== '') {
                
            }
        },
        
        removeCartLine(variant) {
            var i = this.cart.cartLines.findIndex(element => element.variantId === variant.variantId);
            this.cart.cartLines.splice(i, 1);
        },

        changeQuantity(variant) {
            var i = this.cart.cartLines.findIndex(element => element.variantId === variant.variantId);
            this.cart.cartLines[i] = variant;
        },

    },

    watch: {
        cart: {
            handler: function() {
                sessionStorage.cart = JSON.stringify(this.cart.cartLines)
            },
            deep: true
        }
    }
}
</script>
