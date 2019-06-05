<template>
    <div class="flex justify-start flex-row" style="margin-bottom:2rem;">
        <pushbutton collection="wines" 
            :file="wines[Math.floor(Math.random() * wines.length-1)].cover" 
            @added="addToCollections('wines')" 
            @removed="removeFromCollection('wines')"></pushbutton>
        <pushbutton collection="estate"
            :file="estate[Math.floor(Math.random() * estate.length-1)].cover" 
            @added="addToCollections('estate')" 
            @removed="removeFromCollection('estate')"></pushbutton>
        <pushbutton collection="grape" 
            :file="grape[Math.floor(Math.random() * grape.length-1)].cover" 
            @added="addToCollections('grape')" 
            @removed="removeFromCollection('grape')"></pushbutton>
        <pushbutton collection="region" 
            :file="region[Math.floor(Math.random() * region.length-1)].cover" 
            @added="addToCollections('region')" 
            @removed="removeFromCollection('region')"></pushbutton>
    </div>
</template>

<script>
import Vue from 'vue'
import pushbutton from './pushbutton'
import axios from 'axios'
Vue.use(axios)

export default {

    props: ['files'],

    components: { pushbutton },

    data() {
        return {
            collections: ['wines', 'grape', 'region', 'estate'],
            entries: [],
            wines: [],
            grape: [],
            region: [],
            estate: []
        }
    },

    methods: {

        addToCollections(value) 
        {
            this.collections.push(value);
        },

        removeFromCollection(value)
        {
            this.collections = this.collections.filter(
                item => item != value
            );
        },

        getCollection(collection, returnCollection) {
            axios.get('!/Fetch/collection/' + collection + '?deep=false&nested=false')
                .then(response => 
                    {
                        returnCollection.push(...response.data.data.map(
                            function(el) { 
                                return { title:el.title, cover: el.cover_image[0], url: el.url }
                            })
                        );
                        this.entries.push(...returnCollection);
                    }
                )
        }
        
    },

    mounted() {
        this.getCollection('wines', this.wines);
        this.getCollection('grape', this.grape);
        this.getCollection('region', this.region);
        this.getCollection('estate', this.estate);
    },

    watch: {
        collections: function() {
            this.entries = [];
            if(this.collections.indexOf('wines') >= 0) { console.log('we have wines'); this.entries.push(...this.wines)};
            if(this.collections.indexOf('grape') >= 0) this.entries.push(...this.grape);
            if(this.collections.indexOf('region') >= 0) this.entries.push(...this.region);
            if(this.collections.indexOf('estate') >= 0) this.entries.push(...this.estate);
        }
    },
}
</script>
