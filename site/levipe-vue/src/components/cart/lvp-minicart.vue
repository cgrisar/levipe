<template>
    <div class="px-4 py-1 z-10 mt-8 rounded-lg" style="left:auto; right:0; background:linear-gradient(rgba(0, 0, 0, .15), rgba(0, 0, 0, 0.15))">
        <div v-for="variant in cart" 
             v-bind:key="variant.id"
             class="flex text-black mb-1">
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
</template>

<script>

import lvpCartCounter from './lvp-cart-counter.vue'

export default {
    components: { lvpCartCounter },
}
</script>

<style>

</style>
