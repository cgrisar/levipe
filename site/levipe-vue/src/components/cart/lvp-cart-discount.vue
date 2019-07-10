<template>
    <div class="flex flex-row justify-between bg-grey-light rounded-lg px-2 py-4 items-center mb-4">
        <h4 class="text-black">{{ couponlabel }}</h4>
        <div class="flex flex-row">
            <div>
                <input size="10" class="h-7 rounded-lg mr-2" @blur.prevent="lookupDiscount">
            </div>
            <i class="spk-icon-coupon text-3xl"></i>
        </div>
    </div>
    
</template>

<script>

import {Engine} from 'json-rules-engine'
let engine = new Engine()

let rule = {};
rule.conditions = {};
rule.conditions.all = [];
rule.conditions.all.push({
    fact:"product-code",
    operator:"equal",
    value:"HPPW"});
rule.conditions.all.push({
    fact:"product-vintage",
    operator:"equal",
    value:"75cl" });
rule.event = {};
rule.event.type="product-discount";
rule.event.params= {};
rule.event.params.message="discount = 15%";
engine.addRule(rule);


function nowIsBetween(startDate, endDate) {
    var now = new Date();
    return (now >= new Date(startDate) && now <= new Date(endDate));
}

function dataFromResponse(response) {
    let rule = {};

    response.data.data.forEach( discount => {
        // we want only the active and current rules
        if (! (nowIsBetween(discount.start_data, discount.end_date) && discount.active )) {
            return
        }

        if (discount.discount_type === "bogof") {

        }

    })
}

export default {
    props: ['couponlabel'],

    methods: {
        lookupDiscount() {
            this.axios.get("/!/Fetch/collection/discounts")
                .then( (response) => console.log(dataFromResponse(response) ))
        }
    }

}
</script>

