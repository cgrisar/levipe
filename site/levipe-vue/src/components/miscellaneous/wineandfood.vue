<template>
    <div>
        <h5 class="mb-4 text-red-darker">{{ heading[1][this.language] }}</h5>
        <multiselect    v-model="value" 
                        :options="ingredients"
                        group-values="ingredient" 
                        :group-label="language"
                        :multiple="true"
                        placeholder="Ingredient" 
                        track-by="code" 
                        :label="language"
                        @input="onChange"></multiselect>
        <div v-if="presence >= 0">
            <h5 class="mt-4 text-red-darker">{{ labels[presence][this.language] }}</h6>
        </div>
    </div>
</template>

<script>
import Multiselect from 'vue-multiselect'

var pairings = [
        [2, 1, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 2, 2, 0, 2, 1, 1, 1, 1],
        [1, 2, 0, 1, 0, 0, 0, 1, 0, 0],
        [0, 1, 2, 1, 2, 2, 1, 1, 0, 0],
        [0, 0, 0, 0, 0, 1, 1, 2, 0, 0],
        [0 ,0, 0, 0, 1, 2, 2, 1, 0, 0],
        [0, 0, 0, 1, 2, 2, 1, 0, 1, 0],
        [2, 1, 1, 0, 0, 0, 0, 1, 1, 0],
        [0, 0, 2, 1, 1, 1, 1, 1, 0, 0],
        [1, 2, 1, 1, 0, 0, 0, 1, 0, 1],
        [2, 1, 1, 1, 0, 0, 0, 0, 1, 0],
        [0, 0, 0, 0, 1, 2, 2, 1, 1, 0],
        [0, 1, 2, 1, 2, 2, 1, 1, 1, 1],
        [1, 2, 0, 1, 0, 1, 1, 1, 1, 2],
        [2, 1, 0, 1, 1, 1, 0, 1, 0, 0],
        [1, 2, 1, 1, 1, 1, 1, 1, 1, 0],
        [0, 0, 0, 0, 0, 1, 2, 1, 0, 0],
        [0, 0, 0, 2, 1, 1, 0, 0, 1, 0],
        [1, 2, 0, 1, 0, 0, 0, 0, 1, 0],
        [1, 2, 2, 0, 2, 2, 0, 0, 0, 0],
        [0, 0, 1, 1, 1, 1, 1, 1, 2, 0],
        [0, 1, 0, 1, 0, 1, 2, 1, 0, 0],
        [2, 1, 0, 0, 0, 0, 0, 0, 0, 0],
        [1, 2, 0, 1, 0, 1, 1, 1, 1, 0],
        [0, 0, 0, 0, 0, 1, 1, 1, 2, 0],
        [0, 1, 1, 1, 1, 2, 2, 0, 0, 0],
        [0, 1, 0, 1, 0, 0, 0, 0, 1, 2],
        [0, 2, 1, 1, 0, 0, 0, 1, 2, 0],
        [1, 1, 1, 1, 1, 1, 1, 1, 1, 1],
        [0, 0, 1, 1, 1, 1, 0, 0, 2, 0],
        [0, 0, 0, 1, 0, 0, 0, 0, 2, 0],
        [1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
        [0, 0, 0, 0, 0, 0, 0, 1, 2, 1],
        [0, 0, 0, 0, 0, 0, 0, 0, 1, 1],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 2]
    ]

    function sumArrays(sumArray, tagElement){
        for(var i = 0; i < sumArray.length; i++) {
            sumArray[i] += tagElement[i]
        }
        return sumArray;
    }

    function countArrays(countArray, tagElement){
        for(var i = 0; i < countArray.length; i++) {
            countArray[i] += tagElement[i] > 0
        }
        return countArray;
    }

    function returnPresence(tags) {
        var counts = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        tags.forEach(element =>
            counts = countArrays(counts, pairings[element.code])
        );

        var sums = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        tags.forEach(element =>
            sums = sumArrays(sums, pairings[element.code])
        );
        // make list with indices and values
        let indexSort = counts.map(function(e,i){return {index: i, count: e, sum:sums[i]}});
        // sort index/value couples, based on values
        indexSort.sort(function(x, y){
            if ((x.count == y.count) && (x.sum < y.sum)) return 1;
            if (x.count < y.count) return 1;
            return -1
        });

        return indexSort
    }

export default {
    props: ['language'],

    components: {
        Multiselect
    },

    data () {
        return {
            heading: [
                { nl: "Enkele ideeën", fr: "Quelques idées" },
                { nl: "Ingrediëntenlijst", fr: "Liste d'ingrédients" },
            ],
            labels : [
                { "nl" : "Een rode, krachtige wijn", "fr" : "Un vin rouge puissant", code:"bold-red" },
                { "nl" : "Een rode, elegante wijn", "fr" : "Un vin rouge élégant", code: "elegant-red" },
                { "nl" : "Een rode, lichte wijn", "fr" : "Un vin rouge léger", code: "light-red" },
                { "nl" : "Een rosé wijn", "fr" : "Un vin rosé", code: "rose" },
                { "nl" : "Een witte, rijke wijn", "fr" : "Un vin blanc riche", code: "rich-white" },
                { "nl" : "Een witte, halfrijke wijn", "fr" : "Un vin blanc moyennement riche", code: "medium-white" },
                { "nl" : "Een witte, lichte wijn", "fr" : "Un vin blanc léger", code: "light-white" },
                { "nl" : "Een schuimwijn", "fr" : "Un vin effervescent", code: "sparkling" },
                { "nl" : "Een zoete wijn", "fr" : "Un vin doux", code: "sweet" },
                { "nl" : "Een dessertwijn", "fr" : "Un vin de dessert", code: "dessert" },
            ],

            ingredients: [
                {
                    foodGroup: { fr: 'Chair', nl: 'Vlees' },
                    ingredient: [
                        { fr: 'Viande Rouge', nl:'Rood vlees', code: 0 },
                        { fr: 'Charcuterie', nl: 'Charcuterie', code: 1},
                        { fr: 'Viande de porc', nl:'Varkensvlees', code: 2 },
                        { fr: 'Vollaile', nl:'Gevogelte', code: 3 },
                        { fr: 'Coquillages', nl:'Schelpdieren', code: 4 },
                        { fr: 'Poisson', nl:'Vissen', code: 5},
                        { fr: 'Crustacés', nl: 'Schaaldieren', code: 6},
                    ]
                },
                {
                    foodGroup: { fr: 'Préparation', nl: 'Bereiding' },
                    ingredient: [
                        { fr: 'Grillé / Barbecue', nl: 'Gegrild / Barbeque', code: 7 },
                        { fr: 'Sauté / Frit', nl: 'Gebakken / Frituur', code: 8 },
                        { fr: 'Fumé', nl: 'Gerookt', code: 9 },
                        { fr: 'Rôti', nl: 'Geroosterd', code: 10 },
                        { fr: 'Poché / Vapeur', nl: 'Gepocheerd / Gestoomd', code: 11 }
                ]
                },
                {
                foodGroup: { fr: 'Produits laitiers', nl: 'Melkproducten' },
                ingredient: [
                        { fr: 'Fromages à pâte molle / Crème', nl: 'Zachte kazen / Room', code: 12 },
                        { fr: 'Fromage puissant', nl: 'Sterke kazen', code: 13 },
                        { fr: 'Fromage à pâte dure', nl: 'Harde kazen',code: 14 },
                ]
                },
                {
                    foodGroup: { fr: 'Légumes', nl: 'Groenten' },
                    ingredient: [
                        { fr: 'Allium', nl: 'Allium', code: 15 },
                        { fr: 'Légumes verts', nl: 'Groene groenten', code: 16 },
                        { fr: 'Légumes-racines', nl: 'Wortels', code: 17 },
                        { fr: 'Solonacées', nl: 'Nachtschade', code: 18 },
                        { fr: 'Champignons', nl: 'Champignons', code: 19 },
                        { fr: 'Noix et Pépins', nl: 'Noten en Zaden', code: 20 },
                        { fr: 'Haricots et Pois', nl: 'Bonen en Erwten', code: 21 },
                    ]
                },
                {
                    foodGroup: { fr: 'Herbes et Epices', nl: 'Kruiden en specerijen' },
                    ingredient: [
                        { fr: 'Poivre Noir', nl: 'Zwarte peper', code: 22 },
                        { fr: 'Poivre Rouge', nl: 'Rode peper', code: 23 },
                        { fr: 'Epicé', nl: 'Pikant', code: 24 },
                        { fr: 'Herbes', nl: 'Kruidig', code: 25 },
                        { fr: 'Epices de cuisson au four', nl: 'Bakspecerijen', code: 26 },
                        { fr: 'Epices exotiques', nl: 'Exotische kruiden', code: 27 },
                    ]
                },
                {
                    foodGroup: { fr: 'Féculents', nl: 'Zetmelen' },
                    ingredient: [
                        { fr: 'Féculents blancs', nl: 'Witte zetmelen', code: 28 },
                        { fr: 'Grains entiers ou sauvages', nl: 'Wilde granen', code: 29 },
                        { fr: 'Féculents doux', nl: 'Zoete zetmelen', code: 30 },
                        { fr: 'Pommes de terres', nl: 'Aardappelen', code: 31 },
                    ]
                },
                {
                    foodGroup: { fr: 'Sucré', nl: 'Zoet' },
                    ingredient: [
                            { fr: 'Fruits et baies', nl: 'Vruchten en bessen',  code: 32 },
                            { fr: 'Vanille et Caramel', nl: 'Vanille en caramel',  code: 33 },
                            { fr: 'Chocolat et café', nl: 'Chocolade en koffie', code: 34 },
                    ]
                }
            ],

            value: [],

            presence: -1
        }
  },
  methods: {
      onChange(value) {
        this.value = value;

        var winePairing = document.getElementById('winePairing');
        winePairing.innerHTML = "";

        // if all tags are deselected
        if (this.value.length == 0) {
            this.presence = -1
            return
        };

        this.$ga.event('ingredient', 'choose', 'Wine And Food Pairing', this.value);
        
        this.presence = returnPresence(this.value)[0].index;
        var taxonomy = this.labels[this.presence].code;

        this.axios.get(this.language + '/wines/style/' + taxonomy)
            .then(response => winePairing.insertAdjacentHTML('beforeend', response.data));

      }
  }
}
</script>

<style>
fieldset[disabled] .multiselect {
  pointer-events: none;
}

.multiselect__spinner {
  position: absolute;
  right: 1px;
  top: 1px;
  width: 48px;
  height: 35px;
  background: #fff;
  display: block;
}

.multiselect__spinner:before,
.multiselect__spinner:after {
  position: absolute;
  content: "";
  top: 50%;
  left: 50%;
  margin: -8px 0 0 -8px;
  width: 16px;
  height: 16px;
  border-radius: 100%;
  border-color: #1a4731 transparent transparent;
  border-style: solid;
  border-width: 2px;
  box-shadow: 0 0 0 1px transparent;
}

.multiselect__spinner:before {
  animation: spinning 2.4s cubic-bezier(0.41, 0.26, 0.2, 0.62);
  animation-iteration-count: infinite;
}

.multiselect__spinner:after {
  animation: spinning 2.4s cubic-bezier(0.51, 0.09, 0.21, 0.8);
  animation-iteration-count: infinite;
}

.multiselect__loading-enter-active,
.multiselect__loading-leave-active {
  transition: opacity 0.4s ease-in-out;
  opacity: 1;
}

.multiselect__loading-enter,
.multiselect__loading-leave-active {
  opacity: 0;
}

.multiselect,
.multiselect__input,
.multiselect__single {
  font-family: inherit;
  font-size: 16px;
  touch-action: manipulation;
}

.multiselect {
  box-sizing: content-box;
  display: block;
  position: relative;
  width: 100%;
  min-height: 40px;
  text-align: left;
  color: #35495e;
}

.multiselect * {
  box-sizing: border-box;
}

.multiselect:focus {
  outline: none;
}

.multiselect--disabled {
  background: #ededed;
  pointer-events: none;
  opacity: 0.6;
}

.multiselect--active {
  z-index: 50;
}

.multiselect--active:not(.multiselect--above) .multiselect__current,
.multiselect--active:not(.multiselect--above) .multiselect__input,
.multiselect--active:not(.multiselect--above) .multiselect__tags {
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
}

.multiselect--active .multiselect__select {
  transform: rotateZ(180deg);
}

.multiselect--above.multiselect--active .multiselect__current,
.multiselect--above.multiselect--active .multiselect__input,
.multiselect--above.multiselect--active .multiselect__tags {
  border-top-left-radius: 0;
  border-top-right-radius: 0;
}

.multiselect__input,
.multiselect__single {
  position: relative;
  display: inline-block;
  min-height: 20px;
  line-height: 20px;
  border: none;
  border-radius: 5px;
  background: #fff;
  padding: 0 0 0 5px;
  width: calc(100%);
  transition: border 0.1s ease;
  box-sizing: border-box;
  margin-bottom: 8px;
  vertical-align: top;
}

.multiselect__input::placeholder {
  color: #35495e;
}

.multiselect__tag ~ .multiselect__input,
.multiselect__tag ~ .multiselect__single {
  width: auto;
}

.multiselect__input:hover,
.multiselect__single:hover {
  border-color: #cfcfcf;
}

.multiselect__input:focus,
.multiselect__single:focus {
  border-color: #a8a8a8;
  outline: none;
}

.multiselect__single {
  padding-left: 5px;
  margin-bottom: 8px;
}

.multiselect__tags-wrap {
  display: inline;
}

.multiselect__tags {
  min-height: 40px;
  display: block;
  padding: 8px 40px 0 8px;
  border-radius: 5px;
  border: 1px solid #e8e8e8;
  background: #fff;
  font-size: 14px;
}

.multiselect__tag {
  position: relative;
  display: inline-block;
  padding: 4px 26px 4px 10px;
  border-radius: 5px;
  margin-right: 10px;
  color: #fff;
  line-height: 1;
  background: #621b18;
  margin-bottom: 5px;
  white-space: nowrap;
  overflow: hidden;
  max-width: 100%;
  text-overflow: ellipsis;
}

.multiselect__tag-icon {
  cursor: pointer;
  margin-left: 7px;
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  font-weight: 700;
  font-style: initial;
  width: 22px;
  text-align: center;
  line-height: 22px;
  transition: all 0.2s ease;
  border-radius: 5px;
}

.multiselect__tag-icon:after {
  content: "×";
  color: white;
  font-size: 14px;
  font-weight:normal;
}

.multiselect__tag-icon:focus,
.multiselect__tag-icon:hover {
  font-weight: bolder;
}

.multiselect__tag-icon:focus:after,
.multiselect__tag-icon:hover:after {
  color: white;
}

.multiselect__current {
  line-height: 16px;
  min-height: 40px;
  box-sizing: border-box;
  display: block;
  overflow: hidden;
  padding: 8px 12px 0;
  padding-right: 30px;
  white-space: nowrap;
  margin: 0;
  text-decoration: none;
  border-radius: 5px;
  border: 1px solid #e8e8e8;
  cursor: pointer;
}

.multiselect__select {
  line-height: 16px;
  display: block;
  position: absolute;
  box-sizing: border-box;
  width: 40px;
  height: 38px;
  right: 1px;
  top: 1px;
  padding: 4px 8px;
  margin: 0;
  text-decoration: none;
  text-align: center;
  cursor: pointer;
  transition: transform 0.2s ease;
}

.multiselect__select:before {
  position: relative;
  right: 0;
  top: 65%;
  color: #999;
  margin-top: 4px;
  border-style: solid;
  border-width: 5px 5px 0 5px;
  border-color: #999999 transparent transparent transparent;
  content: "";
}

.multiselect__placeholder {
  color: #adadad;
  display: inline-block;
  margin-bottom: 10px;
  padding-top: 2px;
}

.multiselect--active .multiselect__placeholder {
  display: none;
}

.multiselect__content-wrapper {
  position: absolute;
  display: block;
  background: #fff;
  width: 100%;
  max-height: 240px;
  overflow: auto;
  border: 1px solid #e8e8e8;
  border-top: none;
  border-bottom-left-radius: 5px;
  border-bottom-right-radius: 5px;
  z-index: 50;
  -webkit-overflow-scrolling: touch;
}

.multiselect__content {
  list-style: none;
  display: inline-block;
  padding: 0;
  margin: 0;
  min-width: 100%;
  vertical-align: top;
}

.multiselect--above .multiselect__content-wrapper {
  bottom: 100%;
  border-bottom-left-radius: 0;
  border-bottom-right-radius: 0;
  border-top-left-radius: 5px;
  border-top-right-radius: 5px;
  border-bottom: none;
  border-top: 1px solid #e8e8e8;
}

.multiselect__content::webkit-scrollbar {
  display: none;
}

.multiselect__element {
  display: block;
}

.multiselect__option {
  display: block;
  padding: 12px;
  min-height: 40px;
  line-height: 16px;
  text-decoration: none;
  text-transform: none;
  vertical-align: middle;
  position: relative;
  cursor: pointer;
  white-space: nowrap;
}

.multiselect__option:after {
  top: 0;
  right: 0;
  position: absolute;
  line-height: 40px;
  padding-right: 12px;
  padding-left: 20px;
  font-size: 13px;
}

.multiselect__option--highlight {
  background: #1a4731;
  outline: none;
  color: white;
}

.multiselect__option--highlight:after {
  content: attr(data-select);
  background: #1a4731;
  color: white;
}

.multiselect__option--selected {
  background: #f3f3f3;
  color: #35495e;
  font-weight: bold;
}

.multiselect__option--selected:after {
  content: attr(data-selected);
  color: silver;
}

.multiselect__option--selected.multiselect__option--highlight {
  background: #ff6a6a;
  color: #fff;
}

.multiselect__option--selected.multiselect__option--highlight:after {
  background: #ff6a6a;
  content: attr(data-deselect);
  color: #fff;
}

.multiselect--disabled .multiselect__current,
.multiselect--disabled .multiselect__select {
  background: #ededed;
  color: #a6a6a6;
}

.multiselect__option--disabled {
  background: #ededed !important;
  color: #a6a6a6 !important;
  cursor: text;
  pointer-events: none;
}

.multiselect__option--group {
  background: #ededed;
  color: #621b62;
}

.multiselect__option--group.multiselect__option--highlight {
  background: #621b62;
  color: #fff;
}

.multiselect__option--group.multiselect__option--highlight:after {
  background: #621b62;
}

.multiselect__option--disabled.multiselect__option--highlight {
  background: #dedede;
}

.multiselect__option--group-selected.multiselect__option--highlight {
  background: #ff6a6a;
  color: #fff;
}

.multiselect__option--group-selected.multiselect__option--highlight:after {
  background: #ff6a6a;
  content: attr(data-deselect);
  color: #fff;
}

.multiselect-enter-active,
.multiselect-leave-active {
  transition: all 0.15s ease;
}

.multiselect-enter,
.multiselect-leave-active {
  opacity: 0;
}

.multiselect__strong {
  margin-bottom: 8px;
  line-height: 20px;
  display: inline-block;
  vertical-align: top;
}

*[dir="rtl"] .multiselect {
  text-align: right;
}

*[dir="rtl"] .multiselect__select {
  right: auto;
  left: 1px;
}

*[dir="rtl"] .multiselect__tags {
  padding: 8px 8px 0px 40px;
}

*[dir="rtl"] .multiselect__content {
  text-align: right;
}

*[dir="rtl"] .multiselect__option:after {
  right: auto;
  left: 0;
}

*[dir="rtl"] .multiselect__clear {
  right: auto;
  left: 12px;
}

*[dir="rtl"] .multiselect__spinner {
  right: auto;
  left: 1px;
}

@keyframes spinning {
  from {
    transform: rotate(0);
  }
  to {
    transform: rotate(2turn);
  }
}
</style>