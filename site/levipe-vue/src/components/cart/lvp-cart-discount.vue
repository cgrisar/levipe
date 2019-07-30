<template>
    <div class="flex flex-row justify-between bg-grey-light rounded-lg px-2 py-4 items-center mb-4">
        <h4 class="text-black">{{ localeLabel('coupon') }}</h4>
        <div class="flex flex-row">
            <div>
                <input  size="10" 
                        class="h-7 rounded-lg mr-2 px-2" 
                        v-model="couponCode" 
                        @blur.prevent="lookupDiscount">
            </div>
            <i class="spk-icon-coupon text-3xl"></i>
        </div>
    </div>
    
</template>

<script>
function nowIsBetween(startDate, endDate) {
    var now = new Date();
    if ((now >= new Date(startDate)) && (now <= new Date(endDate))) {
        return true;
    }
    return false;
}

export default {
    props: ['couponlabel'],

    data() {
        return {
            cart: store.cart,
            couponCode: ''
        }
    },

    methods: {

        localeLabel(label) {
            var labelMap = new Map([
                ['nl_coupon', 'Couponcode'],
                ['fr_coupon', 'Code du coupon'],
            ]);

            var key = this.locale + "_" + label;
            return labelMap.get(key)
        },

        lookupDiscount() {
            if (this.couponCode == '') {
                return
            };
        
            this.axios.get("/!/Fetch/entry/discounts/" + this.couponCode)
                .then( (response) => {
                    var coupon = response.data.data;

                    // we only want to work with active coupons and valid dates
                    if (! (nowIsBetween(coupon.start_date, coupon.end_date) && coupon.active && coupon.coupon)) {
                        return;
                    }
                    coupon.ratio = (parseInt(coupon.gof) * parseInt(coupon.bogof_discount)) / (100 * parseInt(coupon.bo));
                    this.cart.forEach( (variant) => {
                        var variantRatio = 0;
                        
                        if(parseInt(variant.bo) > 0) {
                            variantRatio = (parseInt(variant.gof) * parseInt(variant.bogof_discount)) / (100 * parseInt(variant.bo));
                        }
                        
                        if (variantRatio < coupon.ratio) {
                            variant.bo = coupon.bo;
                            variant.gof = coupon.gof;
                            variant.discount = coupon.bogof_discount;
                            variant.ordered = Math.ceil(parseInt(variant.total_ordered) * parseInt(variant.bo) / (parseInt(variant.bo) + parseInt(variant.gof)));
                            variant.total_gof = variant.total_ordered - variant.ordered;
                        }
                    })
            })
        }

    }
}
</script>

