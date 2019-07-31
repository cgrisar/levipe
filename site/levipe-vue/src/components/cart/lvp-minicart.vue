<template>
    <template v-if="cartlines.length > 0">
        <a v-bind:href="locale_url+'cart'" class="text-black no-underline inline float-left ml-4 -mt-1">
            <div class="w-12 h-12 float-left">
                <i class="spk-icon-cart-shopkeeper absolute text-2xl font-hairline h-4 w-3 mt-1"></i>
                <lvp-cart-counter :cartlines="cartlines"></lvp-cart-counter>
            </div>
            <div class="absolute hidden group-hover:block mr-8 pin-r">
                <div class="px-4 py-1 z-10 mt-8 rounded-lg" style="left:auto; right:0; background:linear-gradient(rgba(0, 0, 0, .15), rgba(0, 0, 0, 0.15))">
                    <div v-for="cartline in cartlines" 
                        v-bind:key="cartline.id"
                        class="flex text-black mb-1">
                        <div class="inline w-32 md:w-48 mr-2">
                            <div class="font-medium -mb-1">{{ cartline.wine }}</div>
                            <div class="text-xs italic">
                                <span class="mr-2">{{ cartline.vintage }}</span>
                                <span>{{ cartline.volume }}</span>
                            </div>
                        </div>
                        <div class="inline w-8 font-medium text-right float-right mr-2">{{ cartline.ordered }}</div>
                        <div class="inline w-16 font-medium text-right float-right">{{ orderedAmountVariant(cartline) }} &euro;</div>
                    </div>
                    <div class="flex text-black mt-2">
                        <div class="w-1/2 font-semibold">Total</div>
                        <div class="w-1/2 font-semibold text-right">{{ totalAmountCart }} &euro;</div>
                    </div>
                </div>
            </div>
        </a>
    </template>
</template>

<script>

import lvpCartCounter from './lvp-cart-counter.vue'

export default {

    props: ['locale_url'],

    components: { lvpCartCounter },

    data() {
        return{
            cartlines: store.cart.cartLines
        }
    },

    methods: {
        orderedAmountVariant(cartline) {
            return (cartline.ordered * Number(cartline.price.replace(',','.'))).toFixed(2).replace('.',',')
        }
    },

    computed: {
        totalAmountCart() {
            var amount = 0;
            this.cartlines.forEach(element => {
                amount += element.ordered * Number(element.price.replace(',','.') )
            });
            return amount.toFixed(2).replace('.',',');
        }
    }
}
</script>

<style>

</style>
