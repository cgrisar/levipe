<template>
    <div>
        <div v-show="error" class="w-full py-2 rounded-lg text-white text-center font-semibold bg-red mb-4">
            {{ errorMessage }}
        </div>
        <card v-show="!confirmed" class='w-full bg-white rounded-lg p-2 mb-4'
            :class='{ complete }'
            stripe="['pk_test_A2RNf0R7K3rg2fpXHiufoRIO',  {locale: {{ locale }} }]"
            :options='stripeOptions'
            @change='complete = $event.complete'
        />
        <button v-show="!confirmed" class='bg-green-darker p-2 text-white font-semibold rounded-lg float-right' 
            @click='pay' 
            :disabled='!complete'>
            {{ payLabel('pay') }}
        </button>
    </div>
</template>

<script>
import { Card, createPaymentMethod, handleCardAction } from 'vue-stripe-elements-plus'

export default {
    props: ['locale', 'order'],

    components: { Card },

    data () {
        return {
            store,
            error: false,
            errorMessage : '',
            confirmed: false,
            complete: false,
            stripeOptions: {
                fontSize: '14px',
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
            var suffix = (label === 'pay') ? " " + store.cart.totalAmount.toFixed(2).replace('.',',') + " EUR" : "";
            return labelMap.get(key) + suffix;
        },

        pay() {
            this.complete = false;
            this.$emit('submitted');
        },

        handleServerResponse(response) {
            if (response.error) {
                this.error = true;
                this.errorMessage = "Error during payment processing!";
                return
            }

            if (response.data.success) {
                this.$emit("paymentConfirmed")
                return
            }

            if (response.data.requires_source_action) {
                // handleCardAction redirects if a 3D Secure payment is required
                handleCardAction(response.data.payment_intent_client_secret)
                    .then(result => {
                        if (result.error) {
                            // 3D Secure refused authentication
                            console.log('error after payment_intent_client_secret, ', result);
                        } else {
                            // The card action has been handled
                            // The PaymentIntent can be confirmed again on the server
                            this.axios({
                                method: 'post',
                                url: '/!/Laradoo/chargecreditcard',
                                config: { headers: {'Content-Type': 'multipart/form-data' }},
                                data: { payment_intent: result.paymentIntent.id }
                            }).then(result => {
                                console.log(result);
                                if(result.data.error) {
                                    this.error = true;
                                    this.errorMessage = result.data.error.message;
                                    return
                                }

                                // there is no error: we confirm the order, 
                                // create the invoice and reconcile the payment
                                // and send a paymentConfirmed to lvp-cart
                                this.axios({
                                    method: 'post',
                                    url: '/!/Laradoo/confirmorder',
                                    config: { headers: {'Content-Type': 'multipart/form-data' }},
                                    data: { order_id: this.order.id }
                                }).then(result => {
                                    this.$emit("paymentConfirmed")
                                })
                             })
                        }
                });
            }
        }
    },

    watch: {
        order: function(order, oldVal) {
            if(this.confirmed = (order.confirmed == "confirmed")) { return };
            createPaymentMethod().then(result => { 
                this.axios({
                    method: 'POST',
                    url: '/!/Laradoo/chargecreditcard',
                    data: { payment_method: result.paymentMethod.id,
                            amount_total: order.amount_total,
                            name: order.name },
                    config: { headers: {'Content-Type': 'multipart/form-data' }}
                }).then(result => {
                    this.handleServerResponse(result)
                });
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
