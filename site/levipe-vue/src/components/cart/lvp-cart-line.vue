<template>
    <div :id="variant.variantId" class="container flex flex-row">
        <div class="w-1/8 mr-2">
            <button @click="removeVariant(variant)">
                <i class="far fa-2x fa-times-circle" data-fa-transform="shrink-4"></i>
            </button>
        </div>
        <div class="w-1/4 md:w-64 mr-2">
            <div class="font-medium -mb-1">{{ variant.wine }}</div>
            <div class="text-xs italic">
                <span class="mr-2">{{ variant.vintage }}</span>
                <span>{{ variant.volume }}</span>
            </div>
        </div>
        
        <div class="w-1/8 font-semibold text-right ml-2">{{ variant.price }} &euro;</div>
        
        <div class="w-1/5 text-right ml-2">
            <button @click="decVariant(variant)">
                <i class="fas fa-lg text-red-darker fa-minus-circle fa-inverse mr-1" data-f-transform="shrink-8"></i>
            </button>
            <input type="number" min="1" class="w-8 text-right pr-1 text-black bg-grey-light rounded-lg" v-model="variant.ordered" />
            <button @click="incVariant(variant)">
                <i class="fas fa-lg text-green-darker fa-plus-circle fa-inverse ml-1" data-f-transform="shrink-8"></i>
            </button>
        </div>

        <div class="w-1/6 text-right font-semibold">{{ orderedAmountVariant( variant ) }} &euro;</div>

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
            this.$emit('quantity', variant);
        },

        incVariant(variant)
        {
            if ((variant.ordered + variant.total_gof) < variant.quantity) {
                variant.ordered++;
                variant.total_gof = this.calculateTotalGof(variant);
            }
            this.$emit('quantity', variant);
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
