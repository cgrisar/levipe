<template>
    <div :id="variant.variantId" class="flex flex-row px-2 py-1 items-center">
        
        <div class="pr-2" style="flex-grow:1;">
            <button @click="removeVariant(variant)">
                <i class="far fa-2x fa-times-circle" data-fa-transform="shrink-4"></i>
            </button>
        </div>

        <div class="px-1 w-1/3" style="flex-grow:4">
            <div class="font-medium -mb-1">{{ variant.wine }}</div>
            <div class="text-xs italic">
                <span class="mr-2">{{ variant.vintage }}</span>
                <span>{{ variant.volume }}</span>
            </div>
        </div>
        
        <div class="font-semibold text-right px-1" style="flex-grow:2;">{{ variant.price }} &euro;</div>
        
        <div class="justify-center px-1 flex flex-row" style="flex-grow:3">
                <button class="mr-4 lg:mr-0" @click="decVariant(variant)">
                    <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse" data-fa-transform="shrink-4"></i>
                </button>
                <input type="number" min="1" :max="variant.quantity" class="w-8 text-right mx-1 pr-1 rounded-sm" v-model="variant.ordered" />
                <button class="ml-4 lg:ml-0" @click="incVariant(variant)">
                    <i class="fas fa-2x text-red-darker fa-plus-circle fa-inverse" data-fa-transform="shrink-4"></i>
                </button>
            </div>

        <div class="text-right font-semibold pl-1" style="flex-grow:2;">{{ orderedAmountVariant( variant ) }} &euro;</div>

    </div>
</template>

<script>
export default {
    props: ['variant'],

    methods:
    {
        removeVariant(variant)
        {
            this.$emit('removed', variant);
        },

        decVariant(variant)
        {
            if (variant.ordered > 1) {
                variant.ordered--;
                variant.total_gof = this.calculateTotalGof(variant);
            }
        },

        incVariant(variant)
        {
            if ((variant.ordered + variant.total_gof) < variant.quantity) {
                variant.ordered++;
                variant.total_gof = this.calculateTotalGof(variant);
            }
        },

        calculateTotalGof(variant) {
            return (variant.bo > 0) ? Math.floor(variant.ordered / variant.bo) * variant.gof : 0;
        },

        orderedAmountVariant(variant) {
            return (variant.ordered * Number(variant.price.replace(',','.'))).toFixed(2).replace('.',',')
        }
    }

}
</script>
