<template>
    <div>
        <card class='w-full bg-white rounded-lg p-2 mb-4'
            :class='{ complete }'
            stripe='pk_test_A2RNf0R7K3rg2fpXHiufoRIO'
            :options='stripeOptions'
            @change='complete = $event.complete'
        />
        <button class='bg-green-darker px-2 py-2 text-white font-semibold rounded-lg float-right' 
            @click='pay' 
            :disabled='!complete'>
            {{ payLabel('pay') }}
        </button>
    </div>
</template>

<script>
import { Card, createPaymentMethod } from 'vue-stripe-elements-plus'

export default {
    props: ['locale'],

    components: { Card },

    data () {
        return {
            cart: store.cart,
            complete: false,
            stripeOptions: {
                // see https://stripe.com/docs/stripe.js#element-options for details
            }
        }
    },

    methods: {
        payLabel(label) {
            var labelMap = new Map([
                ['nl_pay', 'Betaal'],
                ['fr_pay', 'Paiement'],
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key) + " " + this.cart.totalAmount.toFixed(2).replace('.',',') + " EUR";
        },

        pay() {
            createPaymentMethod().then(result => { 
                if(result.error){

                } else {
                    this.$emit('submitted', result.paymentMethod.id);
                }
            // createToken().then(data => { 
            //     this.$emit('submitted', data.token.id);
            })
        },
    }
}
</script>

<style>
.stripe-card {
  border: 1px solid grey;
}
.stripe-card.complete {
  border-color: green;
}
</style>
