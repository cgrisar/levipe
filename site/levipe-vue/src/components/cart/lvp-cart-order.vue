<template>
<div class="bg-grey-light rounded-lg px-4 py-4 items-center mb-4"> 
    <h4 class="text-black mb-4">{{ localeLabel('order') }}</h4>
    <div id="tabs" class="flex flex-row">
        <div class="w-1/5 font-thin uppercase mb-4 pb-1 border-b border-red-darker mr-6" 
                id="delivery"
                onclick="showTab(this)">{{ localeLabel('delivery') }}</div>
        <div class="w-1/5 font-thin uppercase mb-4 pb-1 mr-6" 
                id="invoice"
                onclick="showTab(this)">{{ localeLabel('invoice') }}</div>
        <div class="w-1/5 font-thin uppercase mb-4 pb-1" 
                id="order"
                onclick="showTab(this)">{{ localeLabel('payment') }}</div>
    </div>
    
    <div class="block" id="deliveryBlock">
        <input type="hidden" name="username" value="{{ email }}">
        <input type="hidden" name="name" value="{{ name }}">
        <input type="hidden" name="odooId" value="{{ odoo_id }}">
        
        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('address') }}
                </label>
            </div>
            <div class="w-4/5">
                <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                        name="delivery_address" 
                        id="delivery_address" 
                        type="text" 
                        :value="address" 
                        onblur="copyField('delivery_address', 'address')" />
            </div>
        </div>

        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('city') }}
                </label>
            </div>
            <div class="flex flex-row w-4/5">
                <div class="w-1/3 mr-4">
                    <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                            name="delivery_zip" 
                            id="delivery_zip" 
                            type="text" 
                            :value="zip"
                            onblur="copyField('delivery_zip', 'zip');"
                            @blur="shippingCost($event)" />
                </div>
                <div class="w-2/3">
                    <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                            name="delivery_city" 
                            id="delivery_city" 
                            type="text" 
                            :value="city" 
                            onblur="copyField('delivery_city', 'city')" />
                </div>
            </div>
        </div>

        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('phone') }}
                </label>
            </div>
            <div class="w-2/5">
                <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                        name="delivery_phone" 
                        type="text" 
                        :value="phone" />
            </div>
        </div>
    </div>


    <div class="hidden" id="invoiceBlock">
        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('address') }}
                </label>
            </div>
            <div class="w-4/5">
                <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                        name="address" 
                        id="address" 
                        type="text" 
                        :value="address" />
            </div>
        </div>

        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('city') }}
                </label>
            </div>
            <div class="flex flex-row w-4/5">
                <div class="w-1/3 mr-4">
                    <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                            name="zip" 
                            id="zip" 
                            type="text" 
                            :value="zip" />
                </div>
                <div class="w-2/3">
                    <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                            name="city" 
                            id="city" 
                            type="text" 
                            :value="city" />
                </div>
            </div>
        </div>

        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ localeLabel('VAT') }}
                </label>
            </div>
            <div class="w-2/5">
                <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                        name="vat" 
                        type="text" 
                        :value="VAT" />
            </div>
        </div>

    </div>
    
    <div class="hidden" id="orderBlock">
        <lvp-cart-stripe :locale="locale"
            @submitted="submitOrder"></lvp-cart-stripe>
    </div>
    
</div>
</template>

<script>
import lvpCartStripe from './lvp-cart-stripe.vue'

function getValue(id){
    if(!document.getElementById(id)){
        return ""
    };
    return document.getElementById(id).value
}

export default {
    props: ['locale', 'address', 'zip', 'city', 'phone', 'VAT', 'user'],

    data() {
        return {
            cart: store.cart
        }
    },

    components: { lvpCartStripe },

    methods: {

        localeLabel(label) {
            var labelMap = new Map([
                ['nl_order', 'Bestelling'],
                ['fr_order', 'Commande'],
                ['nl_delivery', 'Levering'],
                ['fr_delivery', 'Livraison'],
                ['nl_invoice', 'Facturatie'],
                ['fr_invoice', 'Facturation'],
                ['nl_payment', 'Betaling'],
                ['fr_payment', 'Paiement'],
                ['nl_address', 'Adres'],
                ['fr_address', 'Adresse'],
                ['nl_city', 'Gemeente'],
                ['fr_city', 'Commune'],
                ['nl_phone', 'Telefoon'],
                ['fr_phone', 'No de Téléphone'],
                ['nl_VAT', 'BTW-nummer'],
                ['fr_VAT', 'No de TVA'],
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key)
        },

        shippingCost(e) {
            this.$emit('chargesChanged', e);
        },

        submitOrder(token) {
            var orderData = new FormData();
            orderData.set('delAddress', getValue('delivery_address'));
            orderData.set('delZip', getValue('delivery_zip'));
            orderData.set('delCity', getValue('delivery_city'));
            orderData.set('delPhone', getValue('delivery_phone'));
            orderData.set('address', getValue('address'));
            orderData.set('zip', getValue('zip'));
            orderData.set('city', getValue('city'));
            orderData.set('phone', getValue('vat'));
            orderData.set('cartlines', JSON.stringify(this.cart.cartLines));
            orderData.set('token', token);
            this.$emit('submitted', orderData);
        },

        computed: {
            totalAmountCart() {
            var amount = 0;
            this.cart.forEach( element => {
                amount += element.ordered * Number( element.price.replace(',','.') )
            });
            return amount;
        }
    }
    }
}
</script>
