Vue.component('laradoo-fieldtype', {

    mixins: [Fieldtype],

    template: ` <div>
                    <input type="text" name="odoo-code" placeholder="ODOO CODE" v-model="productId" @blur.stop="fetchVariants" />
                    <div class="select select-full" :data-content="label">
                        <select v-el:select :name="name" v-model="data" tabindex="0" @focus="isActive = true" @blur="isActive = false">
                            <option v-for="option in selectOptions" :value="option.value">{{ option.text }}</option>
                        </select>
                    </div>
                </div>`,

    data: function() {
        return {
            //
            productId:'',
            VariantIds: [],
        };
    },

    computed: {
        //
    },

    methods: {
        //
        fetchVariants() {
            this.$http.get('http://levipe.test/!/Laradoo/winevariants', {params: {productID: this.productId }})
                .then(resp => alert(resp.data));
        }
    },

    ready: function() {
        //
    }

});
