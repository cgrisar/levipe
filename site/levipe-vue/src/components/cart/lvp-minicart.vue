<template>
    <div class="group mx-2 -mt-1" v-show="store.cart.cartLines.length > 0">
        <a v-bind:href="locale_url+'cart'" class="text-black no-underline">
            <div class="w-8 h-12 float-left">
                <i class="spk-icon-cart-shopkeeper absolute text-2xl mt-1">
                    <lvp-cart-counter :cartlines="store.cart.cartLines"></lvp-cart-counter>
                </i>
                <div class="hidden group-hover:block float-right mt-8">
                    <div class="px-4 py-2 z-10 rounded-lg" style="background:linear-gradient(rgba(0, 0, 0, .15), rgba(0, 0, 0, 0.15))">
                        <div v-for="cartline in store.cart.cartLines" 
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
            </div>
        </a>
    </div>
</template>

<script>

import lvpCartCounter from './lvp-cart-counter.vue'

export default {

    props: ['locale_url'],

    components: { lvpCartCounter },

    data() {
        return{
            store
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
            store.cart.cartLines.forEach(element => {
                amount += element.ordered * Number(element.price.replace(',','.') )
            });
            return amount.toFixed(2).replace('.',',');
        }
    }
}
</script>
