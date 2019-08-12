<template>
    <div class="flex flex-col mx-auto py-2 bg-grey-light rounded-lg">
        <template v-if="store.cart.cartLines.length > 0">
            <lvp-cart-line  v-for="variant in store.cart.cartLines" 
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
            store
        }
    },

    methods:{

        calculateShippingCost() {
            if (store.cart.delivery !== '') {
                
            }
        },
        
        removeCartLine(variant) {
            var i = store.cart.cartLines.findIndex(element => element.variantId === variant.variantId);
            store.cart.cartLines.splice(i, 1);
        },

        changeQuantity(variant) {
            var i = store.cart.cartLines.findIndex(element => element.variantId === variant.variantId);
            store.cart.cartLines[i] = variant;
        },

    },

    watch: {
        cart: {
            handler: function() {
                sessionStorage.cart = JSON.stringify(store.cart.cartLines)
            },
            deep: true
        }
    }
}
</script>
