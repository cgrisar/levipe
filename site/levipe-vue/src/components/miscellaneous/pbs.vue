<template>
    <div>
        <span class="text-grey-lighter">{{ collections }}</span>
        <div class="flex flex-row -mt-6 mb-8">
            <slot></slot>
        </div>

        <div v-if="prevPage" class="flex">
            <div class="mb-4 mx-auto" @click="prevPg">
                <i class="cursor-pointer fas fa-chevron-circle-up text-red-darker fa-2x lg:fa-4x"></i>
            </div>
        </div>

        <div id="entries"></div>
        
        <div v-if="nextPage" class="flex">
            <div class="mt-4 mx-auto" @click="nextPg">
                <i class="cursor-pointer fas fa-chevron-circle-down text-red-darker fa-2x lg:fa-4x"></i>
            </div>
        </div>
    </div>
</template>

<script>
    export default {

        props: ['locale'],

        data() {
            return {
                pbs: [],
                page: 1,
                prevPage: false, 
                nextPage: false,
                colls: '',
            }
        },

        methods: {
            fetchResponse: function() {
                this.axios.get(this.locale + '/entries/' + this.colls +"?page=" + this.page)
                    .then( response => {
                        this.prevPage = (response.data.search('prevPage') > 0);
                        this.nextPage = (response.data.search('nextPage') > 0);
                        if (response.data.search('figure') < 0) this.page = 1;

                        var entries = document.getElementById('entries');
                        entries.innerHTML = "";
                        entries.insertAdjacentHTML('beforeend', response.data);

                        this.$ga.event('page', 'select', JSON.stringify(this.page));
                    });
            },

            prevPg: function() {
                this.page--
            },

            nextPg: function() {
                this.page++
            }
        },

        computed: {
            collections() {
                let collectionArray = [];

                this.pbs.forEach( function(pb) { if (pb.checked) collectionArray.push(pb.collection) } );
                if (collectionArray.length == 0) {
                    collectionArray = ['wines', 'grape', 'estate', 'region', 'article'];
                    this.pbs.forEach( function(pb) { pb.checked = true });
                };

                this.colls = collectionArray.join("|");
                return collectionArray.join("|");
            }
        },

        watch: {
            colls: function() {
                this.fetchResponse();
            },

            page: function() {
                this.fetchResponse();
            }
        },

        mounted() {
            this.pbs = this.$children;
        }
    }
</script>