<template>
    <span class="rounded-full bg-red-dark font-semibold text-white relative -mt-1"
            style="padding-top:.05rem; 
                    padding-bottom:.05rem;
                    padding-left:0.3rem;
                    padding-right:0.3rem;
                    margin-left:1.1rem; 
                    font-weight:500; 
                    font-size:0.6rem;">
        {{ bottlesInCart }}
    </span>
</template>

<script>
    export default {
        props: ['cart'],

        computed: {
            bottlesInCart() {
                var bottles = 0;
                this.cart.forEach( element => {
                    bottles += element.ordered
                });
                return bottles;
            },
        }
    }
</script>

