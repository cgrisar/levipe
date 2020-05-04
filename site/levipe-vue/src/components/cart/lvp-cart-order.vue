<template>
<div class="bg-grey-light rounded-lg px-4 py-4 items-center mb-4"> 
    <h4 class="text-black -mb-1">{{ label('order') }}</h4>
    <span class="italic block text-xs text-red mb-4">{{ label('mandatory') }}</span>
    <div id="tabs" class="flex flex-row mb-4">
        <div class="cursor-pointer w-1/3 md:w-1/5 text-xs text-center text-black bg-grey-light font-semibold uppercase rounded-full mr-4 text-white bg-red-darker" 
                style="height:1.45rem;"
                id="delivery"
                onclick="showTab(this)">{{ label('delivery') }}</div>
        <div class="cursor-pointer w-1/3 md:w-1/5 text-xs text-center text-black bg-grey-light font-semibold uppercase rounded-full mr-4" 
                style="height:1.45rem;"
                id="invoice"
                onclick="showTab(this)">{{ label('invoice') }}</div>
        <div class="cursor-pointer w-1/3 md:w-1/5 text-xs text-center text-black bg-grey-light font-semibold uppercase rounded-full" 
                style="height:1.45rem;"                
                id="order"
                onclick="showTab(this)">{{ label('payment') }}</div>
    </div>
    
    <div class="block" id="deliveryBlock">
        <input type="hidden" name="username" value="{{ email }}">
        <input type="hidden" name="name" value="{{ name }}">
        <input type="hidden" name="odooId" value="{{ odoo_id }}">
        
        <div class="items-center mb-6 flex flex-row">
            <div class="w-1/5">
                <label class="block text-black font-semibold md:text-right mb-1 md:mb-0 pr-4" for="inline-full-name">
                    {{ label('address') }}
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
                    {{ label('city') }}
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
                    {{ label('phone') }}
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
                    {{ label('address') }}
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
                    {{ label('city') }}
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
                    {{ label('VAT') }}
                </label>
            </div>
            <div class="w-2/5">
                <input class="bg-white appearance-none w-full rounded-lg py-2 px-4 text-black" 
                        :class="{'bg-red-lighter text-red-darker': vatNOK}"
                        name="vat"
                        id="vat"
                        type="text"
                        placeholder="BE0123456789"
                        v-model="vatNumber"
                        @focus= "vatNOK = false;"
                        @blur="checkVAT" />
            </div>
        </div>

    </div>
    
    <div class="hidden" id="orderBlock">
        <lvp-cart-stripe 
            :locale="locale" 
            :order="order"
            @submitted="createOrder"
            @paymentConfirmed="confirmOrder"></lvp-cart-stripe>
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
    props: ['locale', 'odooId', 'address', 'zip', 'city', 'phone', 'vat', 'user'],

    data() {
        return {
            store,
            order: {},
            vatNOK: false,
            vatNumber: this.vat
        }
    },

    components: { lvpCartStripe },

    methods: {

        label(label) {
            var labelMap = new Map([
                ['nl_order', 'Bestelling'],
                ['fr_order', 'Commande'],
                ['nl_mandatory', 'Alle velden (behalve BTW) zijn verplicht!'],
                ['fr_mandatory', 'Tous les champs (sauf TVA) sont obligatoires!'],
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
                ['fr_phone', 'Téléphone'],
                ['nl_VAT', 'BTW'],
                ['fr_VAT', 'No TVA'],
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key)
        },


        shippingCost() {
            this.$emit('chargesChanged');
        },

        checkVAT() {

            if (this.vatNumber.length == 0) {
                return;
            }
            
            var regex = /[.,\s]/g;
            this.vatNumber = this.vatNumber.replace(regex,'');

            var orderData = new FormData();
            orderData.set('vat', this.vatNumber);

            this.axios({
                method: 'POST',
                url: '/!/Laradoo/checkVIES',
                data: orderData,
                config: { headers: {"Content-Type": "multipart/form-data"}},
            }).then(result => this.vatNOK = !result.data.vatValid);
        },

        createOrder() {
            var orderData = new FormData();
            orderData.set('odooId', this.odooId);
            orderData.set('delAddress', getValue('delivery_address'));
            orderData.set('delZip', getValue('delivery_zip'));
            orderData.set('delCity', getValue('delivery_city'));
            orderData.set('delPhone', getValue('delivery_phone'));
            orderData.set('address', getValue('address'));
            orderData.set('zip', getValue('zip'));
            orderData.set('city', getValue('city'));
            orderData.set('vat', getValue('vat'));
            orderData.set('cartlines', JSON.stringify(store.cart.cartLines));

            this.axios({
                method: 'POST',
                url: '/!/Laradoo/createorder',
                data: orderData,
                config: { headers: {"Content-Type": "multipart/form-data"}},
            }).then(result => {
                    this.order = result.data
                })
        },

        confirmOrder() {
            this.$emit("paymentConfirmed");
        }
    },

    computed: {
            totalAmountCart() {
            var amount = 0;
            store.cart.cartLines.forEach( element => {
                amount += element.ordered * Number( element.price.replace(',','.') )
            });
            return amount + store.cart.shipping;
        }
    }
}
</script>
