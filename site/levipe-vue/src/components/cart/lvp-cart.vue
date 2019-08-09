<template>
    <div class="flex flex-col container mx-auto lg:flex-row">
        <div class="px-4 w-full mb-4 sm:px-0 lg:w-1/2 lg:pr-4 lg:border-r lg:border-red-darker">
            <lvp-cart-lines></lvp-cart-lines>
            {{ totalAmountCart }}
        </div>
        <div class="px-4 w-full sm:px-0 lg:w-1/2 lg:pl-4">
            <div class="flex flex-col">
                <lvp-cart-discount :locale="locale"></lvp-cart-discount>
                <lvp-cart-order :locale="locale"
                                :address="address"
                                :zip="zip"
                                :city="city"
                                :phone="phone"
                                :vat="vat"
                                @chargesChanged="calcutateCharges"
                                @submitted="submitPayment"></lvp-cart-order>
            </div>
        </div>
    </div>
</template>

<script>
import lvpCartLines from './lvp-cart-lines.vue'
import lvpCartOrder from './lvp-cart-order.vue'
import lvpCartDiscount from './lvp-cart-discount'

function handleServerResponse(response) {

    if (response.data.requires_source_action) {
        // Use Stripe.js to handle required card action
        stripe.handleCardAction(response.data.payment_intent_client_secret)
            .then(function(result) {
                if (result.data.error) {
                    console.log('error after payment_intent_client_secret, ', result);
                } else {
                    // The card action has been handled
                    // The PaymentIntent can be confirmed again on the server
                    this.axios({
                        method: 'post',
                        url: '/!/Laradoo/chargecard',
                        config: { headers: {'Content-Type': 'multipart/form-data' }},
                        data: JSON.stringify({ payment_intent: result.data.paymentIntent.id })
                    }).then(confirmResult => { return confirmResult.data })
                      .then(handleServerResponse);
                }
        });
        return
    }

    if (response.error) {
        // Show error from server on payment form
    } else {
        console.log('success');
    }
}

export default {

    data() {
        return {
            cart: store.cart
        }
    },

    props: ['locale', 'odooId', 'name', 'email', 'address', 'zip', 'city', 'phone', 'vat'],
    
    components: { lvpCartLines, lvpCartOrder, lvpCartDiscount},

    methods: {
        calculateCharges() {
            this.axios.get('/!/Fetch/entry/shipping/be')
                .then(data => console.log(data))
        },

        submitPayment(orderData) {
            orderData.set('odooId', this.odooId);
            this.axios({
                method: 'post',
                url: '/!/Laradoo/order',
                data: orderData,
                config: { headers: {'Content-Type': 'multipart/form-data' }}
                })
                .then(result => {
                    handleServerResponse(result)
                });
        }
    },

    computed: {
        totalAmountCart() {
            var amount = 0;
            this.cart.cartLines.forEach(element => {
                amount += element.ordered * Number(element.price.replace(',','.') )
            });
            this.cart.totalAmount = amount;
            return amount
        }
    }

}
</script>

<style>

</style>
