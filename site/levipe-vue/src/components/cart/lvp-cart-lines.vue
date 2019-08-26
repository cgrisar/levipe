<template>
    <div class="flex flex-col mx-auto p-2 bg-grey-light rounded-lg">
        <template v-if="store.cart.cartLines.length > 0">
            <lvp-cart-line  v-for="variant in store.cart.cartLines" 
                            :key="variant.id" 
                            :variant="variant"
                            @removed="removeCartLine($event)"
                            @quantity="changeQuantity($event)">
            </lvp-cart-line>
            <div class="flex flex-row px-2 items-center mt-4">
                <div class="mr-2 w-8"></div>
                <div class="w-3/4 font-medium text-black">{{ label('shipping') }}</div>
                <div class="w-1/4 md:w-1/5 text-right font-semibold text-black border-b border-black">{{ displayShippingCost }}</div>
            </div>
            <div class="flex flex-row px-2 items-center mt-2">
                <div class="mr-2 w-8"></div>
                <div class="w-1/2 font-semibold text-base text-black">{{ label('total') }}</div>
                <div class="w-1/2 text-right font-semibold text-base text-black">{{ displayTotalCost }}</div>
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
</template>

<script>
import lvpCartLine from './lvp-cart-line.vue'

export default {
    
    components: { lvpCartLine },

    props: ["locale"],
    
    data() {
        return {
            store
        }
    },

    methods:{
        label(label) {
            var labelMap = new Map([
                ['nl_shipping', 'Leveringskosten'],
                ['fr_shipping', 'Frais de livraison'],
                ['nl_total', 'Totaal'],
                ['fr_total', 'Total'],
                ['nl_free', 'Gratis'],
                ['fr_free', 'Offerts'],
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key);
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

    computed: {
        displayShippingCost() {
            if (store.cart.shipping == 0) {
                return this.label('free')
            };
            return store.cart.shipping.toFixed(2).replace('.',',') + " €";
        },

        displayTotalCost() {
            return store.cart.totalAmount.toFixed(2).replace('.',',') + " €";
        },
    }
}
</script>
