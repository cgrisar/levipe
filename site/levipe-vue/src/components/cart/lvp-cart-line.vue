<template>
    <div :id="variant.variantId" class="flex flex-row px-2 py-1 items-center">
        
        <div class="mr-2 w-8">
            <button @click="removeVariant(variant)">
                <i class="far fa-2x fa-times-circle" data-fa-transform="shrink-4"></i>
            </button>
        </div>

        <div class="w-1/3 mr-2 sm:mr-0">
            <div class="font-medium -mb-1 whitespace-no-wrap overflow-hidden" style="text-overflow:ellipsis; -o-text-overflow:ellipsis;">{{ variant.wine }}</div>
            <div class="text-xs italic">
                <span class="mr-2">{{ variant.vintage }}</span>
                <span>{{ variant.volume }}</span>
            </div>
        </div>
        
        <div class="hidden sm:block font-semibold text-right px-2" style="width: 4.5rem;">{{ variant.price }} &euro;</div>
        
        <div class="w-1/4 justify-center px-1 flex flex-row">
            <button @click="decVariant(variant)">
                <i class="fas fa-2x text-red-darker fa-minus-circle fa-inverse" data-fa-transform="shrink-4"></i>
            </button>
            <input type="number" min="1" :max="variant.quantity" class="w-8 text-right mx-1 pr-1 rounded-lg" v-model="variant.ordered" />
            <button @click="incVariant(variant)">
                <i class="fas fa-2x text-red-darker fa-plus-circle fa-inverse" data-fa-transform="shrink-4"></i>
            </button>
        </div>

        <div class="flex flex-row flex-1 items-center">
            <div class="w-1/3 md:w-1/2 lg:w-1/2">       
                <template v-if="variant.total_gof > 0">
                    <span class="fa-fw fa-layers fa-2x" style="margin-bottom:-.125rem;">
                        <i class="fas fa-certificate fa-stack-2x text-green-darker" data-fa-transform="shrink-8 left-6"></i>
                        <span class="fa-layers-text text-white font-semibold" data-fa-transform="shrink-8 left-2">{{ variant.total_gof }}</span>
                    </span>
                </template>
            </div>
            <div class="w-2/3 md:w-1/2 lg:w-2/3">
                <div class="text-right font-semibold pl-1">{{ orderedAmountVariant( variant ) }} &euro;</div>
            </div>
        </div>

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
