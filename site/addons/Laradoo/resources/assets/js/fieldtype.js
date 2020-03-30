Vue.component('laradoo-fieldtype', {

    mixins: [Fieldtype],

    template: ` <div style="display:flex">
                    <div style="width:25%">
                        <input type="text" name="odoo-code" placeholder="ODOO CODE" v-model="productId" @blur.stop="fetchVariants" />
                    </div>
                    <div class="select select-full" style="width:50%">
                        <select :name="name" v-model="variantId" @change="onChange($event)">
                            <option v-for="option in variants" :value="option.id">{{ option.millesime }} - {{ option.volume }}</option>
                        </select>
                    </div>
                    <div style="width:25%">
                        <input type="text" name="odoo-variant" placeholder="VariantId" v-model="variantId" />
                    </div>
                </div>`,

    data: function() {  
        return {
            //
            variantId:'',
            productId:'',
            variants: [],
        };
    },

    methods: {
        //
        fetchVariants() {
            this.$http.get('http://levipe.test/!/Laradoo/winevariants', {params: {productID: this.productId }})
                .then(resp => this.variants = resp.data.variants);
        },

        onChange($e) {
            alert(this.variantId);
        }

    },

    ready: function() {
        //
    }

});
