import { runInThisContext } from "vm";

export default {
    methods: {

        calculateTotalGof() {
            return ( this.bo > 0 ) ? Math.floor( this.ordered / this.bo ) * this.gof : 0;
        },

        decVariant() {
            if (this.ordered > 1) {
                this.ordered--;
                this.total_gof = this.calculateTotalGof();
                sessionStorage.cart = JSON.stringify( this.cart );
            }
        },

        incVariant() {
            if ((this.ordered + this.total_gof) < this.quantity) {
                this.ordered++;
                this.total_gof = this.calculateTotalGof();
                sessionStorage.cart = JSON.stringify( this.cart );
            }
        },

        setVariant() {
            if (this.ordered < 1) {
                this.ordered = 1;
            }
            
            if (this.ordered > this.maxquantity) {
                this.ordered = this.maxquantity
            }

            this.total_gof = this.calculateTotalGof();
        }
        
    }
}