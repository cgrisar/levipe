<template>
    <span class="rounded-full bg-red-dark text-white text-xs font-semibold h-4 px-1 flex items-center justify-center absolute ml-4"
            style="font-family:source sans pro; margin-top:-1.75rem">
        {{ bottlesInCart }}
    </span>
</template>

<script>
    export default {
        props: ['cartlines'],

        computed: {
            bottlesInCart() {
                var bottles = 0;
                this.cartlines.forEach( element => {
                    bottles += element.ordered + element.total_gof
                });
                return bottles;
            },
        }
    }
</script>

