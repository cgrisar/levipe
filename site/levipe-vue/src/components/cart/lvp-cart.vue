<template>
    <div>
        <div v-show="confirmed"
            class="flex justify-center w-full mx-auto md:w-1/2 lg:md:w-1/4 px-4 md:px-0 mt-8 sm:mt-4">
            <div class="bg-green-dark text-white font-semibold rounded-lg p-2 text-center">
                {{ Label('confirm')}}
            </div>
        </div>
        <div v-show="!confirmed" class="flex flex-col container mx-auto lg:flex-row">
            <div class="px-4 w-full mb-4 sm:px-0 lg:w-1/2 lg:pr-4 lg:border-r lg:border-red-darker">
                <lvp-cart-lines :locale="locale"></lvp-cart-lines>
            </div>
            <div class="px-4 w-full sm:px-0 lg:w-1/2 lg:pl-4">
                <div class="flex flex-col">
                    <lvp-cart-discount :locale="locale"></lvp-cart-discount>
                    <lvp-cart-order :locale="locale"
                                    :odooId="odooId"
                                    :address="address"
                                    :zip="zip"
                                    :city="city"
                                    :phone="phone"
                                    :vat="vat"
                                    @chargesChanged="calculateCharges"
                                    @paymentConfirmed="confirmPayment"></lvp-cart-order>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import lvpCartLines from './lvp-cart-lines.vue'
import lvpCartOrder from './lvp-cart-order.vue'
import lvpCartDiscount from './lvp-cart-discount'


export default {

    data() {
        return {
            store,
            confirmed: false
        }
    },

    props: ['locale', 'odooId', 'name', 'email', 'address', 'zip', 'city', 'phone', 'vat'],
    
    components: { lvpCartLines, lvpCartOrder, lvpCartDiscount},

    methods: {
        Label(label) {
            var labelMap = new Map([
                ['nl_confirm', 'Dank u wel voor de bestelling!'],
                ['fr_confirm', 'Merci pour la commande!']
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key);
        },

        calculateCharges() {
            var shipping = 0;
            this.axios.get('/!/Fetch/entry/shipping/be')
                .then(result => {
                        result.data.data.rates.forEach(
                            rate => { if((store.cart.totalAmount - store.cart.shipping) >= parseInt(rate.minimum_amount)) {
                                shipping = parseInt(rate.charge)
                                }
                            }
                        )
                        store.cart.shipping = shipping;
                    })
        },

        confirmPayment() {
            this.confirmed = true;
            sessionStorage.removeItem('cart');
            store.cart.cartLines.length = 0;
        }
    },

    watch: {
        store: {
            handler: function() {
                // calculate shipping costs whenever the cart changes
                this.calculateCharges();

                // calculate the total amount
                var amount = 0;
                store.cart.cartLines.forEach(element => {
                    amount += element.ordered * Number(element.price.replace(',','.') )
                });
                store.cart.totalAmount = amount + store.cart.shipping;

                // store changes into session
                sessionStorage.cart = JSON.stringify(store.cart);
            },
            deep: true,
            immediate: true
        }

    }

}
</script>
