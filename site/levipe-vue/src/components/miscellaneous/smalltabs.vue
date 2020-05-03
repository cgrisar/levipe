<template>
    <div class="flex block md:hidden mb-4">
        <h6 v-for="h in hs" 
                class="mr-4 text-sm text-white font-semibold bg-red-darker px-2 rounded-full" 
                style="padding-bottom: 2px;"
                @click="show(h.tab)">
            {{ h.title }}
        </h6>
    </div>
</template>

<script>
export default {
    props: ['tabs'],

    data() {
        return {
            hs:[]
        }
    },

    methods: {
        show(tab) {
            this.hs.forEach(h => document.getElementById(h.tab).classList.add("hidden"));
            document.getElementById(tab).classList.remove("hidden");
        }
    },

    created() {
        this.hs = JSON.parse(this.tabs);
    },

}
</script>