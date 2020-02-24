<template slot-scope="{pb}">
    <div class="mr-5 md:mr-12" @click="toggle()">
        <input type="checkbox"
            :id="collection" 
            :value="collection" 
            v-model="checked"
            class="hidden" />
        <label class="cursor-pointer">
            <div v-bind:style="'background-image:url(' + file + '); background-position: 50% 0px; transform:scale(.97)'" 
                class="w-12 h-12 md:w-24 md:h-24 bg-cover mb-2 relative rounded-full shadow-md"></div>
        </label>
        <h5 class="text-xs md:text-sm text-center font-normal md:font-semibold md:uppercase mt-0">{{ title }}</h5>
    </div>
</template>

<script>
    export default {
        props:  ['title', 'collection', 'file'],

        data() {
            return {
                checked: true
            }
        },
        methods: {
            toggle() {
                this.checked = !this.checked;
            }
        },
    }
</script>