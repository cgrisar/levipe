<template>
    <div>
      <a href="#" @click.prevent="show" class="w-full">
          <img :src="thumbnail" alt="{{ alt }}" class="w-1/2 md:w-1/3 lg:w-1/4 mt-8 shadow-md rounded-full" />
      </a>
      <div class="lightbox fixed pin z-50 flex justify-center items-center bg-black opacity-75" v-if="visible" @click="hide">
          <div class="fixed pin-r pin-t text-white cursor-pointer text-4xl p-1 mr-2" @click.stop="hide">&times;</div>
          <div class="flex">
              <div class="lightbox-image" @click.stop="">
                  <img :src="thumbnail" />
              </div>
          </div>
      </div>
  </div>
</template>

<script>
export default {
    props: {
        thumbnail: {
            type: String,
            required: true,
        },
        images: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            visible: false,
            index: 0,
        };
    },
    methods: {
        show() {
            this.visible = true;
        },
        hide() {
            this.visible = false;
            this.index = 0;
        },
        hasNext() {
            return this.index + 1 < this.images.length;
        },
        hasPrev() {
            return this.index - 1 >= 0;
        },
        prev() {
            if (this.hasPrev()) {
                this.index -= 1;
            }
        },
        next() {
            if (this.hasNext()) {
                this.index += 1;
            }
        },
        onKeydown(e) {
            if (this.visible) {
                switch (e.key) {
                    case 'ArrowRight':
                        this.next();
                        break;
                    case 'ArrowLeft':
                        this.prev();
                        break;
                    case 'ArrowDown':
                    case 'ArrowUp':
                    case ' ':
                        e.preventDefault();
                        break;
                    case 'Escape':
                        this.hide();
                        break;
                }
            }
        },
    },
    mounted() {
        window.addEventListener('keydown', this.onKeydown)
    },
    destroyed() {
        window.removeEventListener('keydown', this.onKeydown)
    },
};
</script>

<style>
.lightbox {
    background: rgba(0, 0, 0, 0.8);
}
.lightbox-image img {
    width: auto;
    height: auto;
    max-width: 100%;
    max-height: calc(100vh - 90px);
}