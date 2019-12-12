<template>
    <div>
        <a v-if="page > 1" href="prev-page" @click="getPrevEntries()">
        </a>
        <a v-if="page > 1" href="next-page" @click="getNextEntries()">
        </a>
    </div>
</template>

<script>
export default {

    props: ['prev-page', 'next-page']

    data() {
        return {
            page = 1,
            collection = "wines",
        }
    },

    methods: {

        getPrevEntries() {
            if(page > 1) {
                page--
            }
            getEntries(page)
        },

        getNextEntries() {
            page++;
            getEntries(page)
        },

        getEntries(page) {
            this.axios.get("http://levipe.test/api/{collection}/?page={page}");
        }
    }

}
</script>
