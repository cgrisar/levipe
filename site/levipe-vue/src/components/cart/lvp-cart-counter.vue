<template>
    <span class="rounded-full bg-red-dark font-semibold text-white text-xs mx-auto px-1 relative ml-6 -mt-2">
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

