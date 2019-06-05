<template>
    <div style="margin-right:3rem;" @click="toggle()">
        <input type="checkbox" 
            :id="collection" 
            :value="collection" 
            v-model="checked"
            class="hidden" />
        <label>
            <div v-bind:style="'background-image:url(' + file + '); background-position: 50% 0px; transform:scale(.97)'" 
                class="w-24 h-24 bg-cover mb-2 relative rounded-full shadow-md"></div>
        </label>
        <h5 class="text-sm text-center font-semibold uppercase mt-0">{{ collection_to_label(this.collection) }}</h5>
    </div>
</template>

<script>
export default {
    props: ['collection', 'file', 'locale'],

    data() {
        return {
            checked: true,
        }
    },

    methods: {
        collection_to_label(collection){
            var labelMap = new Map([
                  ['nl_wines', 'Wijnen'],
                  ['fr_wines', 'Vins'],
                  ['nl_estate', 'Domeinen'],
                  ['fr_estate', 'Domaines'],
                  ['nl_grape', 'Druiven'],
                  ['fr_grape', 'Cépages'],
                  ['nl_region', 'Streken'],
                  ['fr_region', 'Régions']
                ]);

            var key = this.locale + "_" + collection;
            return labelMap.get(key)
        },

        toggle() {
            if(!this.checked) {
                Array.from(document.getElementsByClassName(this.collection)).forEach(element=>{ element.classList.toggle('hidden') });
                Array.from(document.getElementsByClassName(this.collection)).forEach(element=>{ element.classList.toggle('opacity-0') });
                Array.from(document.getElementsByClassName(this.collection)).forEach(element=>{ element.classList.toggle('opacity-100') });
            } else {
                Array.from(document.getElementsByClassName(this.collection))
                    .forEach(element=>{ element.addEventListener('animationend', () => element.classList.toggle('hidden'), {once:true}) });
                Array.from(document.getElementsByClassName(this.collection)).forEach(element=>{ element.classList.toggle('opacity-0') });
                Array.from(document.getElementsByClassName(this.collection)).forEach(element=>{ element.classList.toggle('opacity-100') });
            }
            this.checked = !this.checked;
        }
    }
}
</script>

