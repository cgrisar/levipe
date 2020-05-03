<template>
    <div v-if="this.slides.length > 0">
        <h5 v-if="title" class="text-right uppercase mb-4">{{ title }}</h5>
        <div>
            <transition-group name="fade" tag="div" class="static md:relative">
                <div v-for="i in [currentIndex]" :key="i" class="relative h-36 md:h-48">
                    <a :href="slides[slideIndex].url">
                        <img  :src="slides[slideIndex].cover_image[0].replace('/nl/', '/').replace('/fr/', '/')" class="md:rounded-lg w-full h-full"  style="object-fit:cover" />
                        <div class="md:rounded-lg absolute pin-t w-full h-full" style="background:rgba(0, 0, 0, .25)">
                            <h6 class="text-sm md:text-sm opacity-100 font-bold text-white text-center absolute pin-b pb-2 pl-4">{{ slides[slideIndex].title }}</h6>
                        </div>
                    </a>
                    <button @click="prev">
                        <i class="fas fa-2x fa-chevron-circle-left text-white opacity-75 hover:opacity-100 absolute pin-l ml-3" style="top: 45%;" data-fa-transform="shrink-5"></i>
                    </button>
                    <button @click="next">
                        <i class="fas fa-2x fa-chevron-circle-right text-white opacity-75 hover:opacity-100 absolute pin-r mr-3" style="top: 45%;" data-fa-transform="shrink-5"></i>
                    </button>
                </div>
            </transition-group>
        </div>
    </div>
</template>

<script>
    export default {
        props:[ 'collection', 'taxonomy', 'language', 'title' ],

        data() {
            return {
                slides: [],
                timer: null,
                currentIndex: 0,
            }
        },
    
        mounted() {
            let url = '/!/Fetch/collection/' + this.collection + '?taxonomy=' + this.taxonomy + '&locale=' + this.language;
            this.axios.get(url)
                .then(response => { 
                        this.slides = response.data.data 
                        if(this.slides.length >0) this.startSlide()
                });
        },
    
        methods: {
            startSlide: function() {
                this.timer = setInterval(this.next, 5000);
            },
        
            next: function() {
                this.currentIndex += 1
            },
            prev: function() {
                this.currentIndex -= 1
            }
        },
    
        computed: {
            slideIndex() {
                if (this.slides.length > 0) {
                    return Math.abs(this.currentIndex) % this.slides.length;
                }
            }
        }
    
    }
</script>

<style>
.fade-enter-active,
.fade-leave-active {
  transition: all 0.9s ease;
  position: absolute;
  width:100%;
  opacity: 1;
}
.fade-enter,
.fade-leave-to {
  visibility: hidden;
  width:100%;
  opacity: 0;
}

/* Position the "next button" to the right */
.next {
  right: 0;
}
.prev {
  left: 0;
}
/* On hover, add a black background color with a little bit see-through */
.prev:hover, .next:hover {
  background-color: rgba(0,0,0,0.9);
}
</style>
