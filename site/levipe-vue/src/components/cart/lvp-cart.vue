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
                                :name="name"
                                :email="username"
                                :address="address"
                                :zip="zip"
                                :city="city"
                                :phone="phone"
                                :VAT="VAT"
                                @chargesChanged="calcutateCharges"></lvp-cart-order>
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
            cart: store.cart
        }
    },

    props: ['locale', 'name', 'email', 'address', 'zip', 'city', 'phone', 'VAT'],
    
    components: { lvpCartLines, lvpCartOrder, lvpCartDiscount},

    methods: {
        calculateCharges() {
            this.axios.get('/!/Fetch/entry/shipping/be')
                .then(data => console.log(data))
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
