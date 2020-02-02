<template>
<div class="mr-2 md:mr-4">
    <a class="text-black no-underline"
        @click="$modal.show('search-modal')"
    >
        <i class="spk-icon-search text-2xl"></i>
    </a>
    <modal 
        name="search-modal"
        classes="py-8 px-8 bg-red-darker text-white opacity-75"
        height="100%"
        width="100%"
    >
        <div class="flex container mx-auto">
            <ais-instant-search
                :search-client="searchClient"
                :index-name="localeIndex('collections_wines_')"
                class="w-full">
                <ais-configure :attributesToSnippet="['title', 'description', 'g', 'r']"
                                snippetEllipsisText="[…]"/>


                <ais-autocomplete :indices="[ { label: 'region', value: localeIndex('collections_region_') },
                                              { label: 'grape', value: localeIndex('collections_grape_') },
                                              { label: 'estate', value: localeIndex('collections_estate_') }]">
                    <div slot-scope="{ currentRefinement, indices, refine }">
                        <div class="flex">
                            <input
                              type="search"
                              autofocus
                              :value="currentRefinement"
                              :placeholder="localePlaceholder()"
                              @input="this.$ga.event('search', 'key', $event.currentTarget.value); refine($event.currentTarget.value)"
                              class="levipe-Searchbox-input flex-1">
                            <a 
                              class="font-regular uppercase no-underline mt-3"
                              @click="$modal.hide('search-modal')">
                                <i class="spk-icon-close text-2xl text-white"></i>
                            </a>
                        </div>

                        <div v-if="currentRefinement">
                          <div class="flex flex-col md:flex-row">
                            <div v-for="index in indices" :key="index.label" class="pr-4 w-full md:w-1/4 mb-8" v-show="index.hits.length > 0">
                                <h2 class="text-xl font-semibold text-white mb-2">{{ localeLabel(index.label) }}</h2>
                                
                                <div v-for="hit in index.hits" :key="hit.objectID" class="mb-2">
                                    <a :href="url(hit.slug, index.label)" class="link">
                                        <ais-highlight attribute="title" :hit="hit" style="font-weight:500;" /><br/>
                                    </a>
                                    <div v-if="index.label == 'primary'" :id="hit.objectID">
                                        {{ snippetArray(hit._snippetResult.g, hit.objectID, index.label) }}
                                        {{ snippetArray(hit._snippetResult.r, hit.objectID, index.label) }}
                                    </div>
                                  
                                    <div v-if="index.label == 'region'" :id="hit.objectID">
                                        {{ snippetArray(hit._snippetResult.description, hit.objectID, index.label) }}
                                    </div>

                                    <ais-snippet attribute="description" :hit="hit" class="text-xs"/>
                                </div>
                            </div>
                        </div>
                    </div>
                </ais-autocomplete>
            </ais-instant-search>
        </div>
    </modal>
</div>
</template>

<script>
    import algoliasearch from 'algoliasearch/lite';

    export default {

        props: ['locale'],
        
        data() {
            return {          
                searchClient: algoliasearch(
                  'FX7PZZ3MWT',
                  '0237b562da0c8ab6fd1efa4168be9df3'
                ),
            }
        },

        methods: {
            onSelect(selected) {
                if (selected) {
                  this.$emit('selected', selected.item );
                }
            },

            indicesToSuggestions(indices) {
                return indices.map(({ hits }) => ({ data: hits }));
            },

            getSuggestion(value) {
              return value.item[this.$props.attribute];
            },

            localeIndex(index) {
              return index + this.locale;
            },

            localeLabel(label) {
                var labelMap = new Map([
                  ['nl_primary', 'Wijnen'],
                  ['fr_primary', 'Vins'],
                  ['nl_estate', 'Domeinen'],
                  ['fr_estate', 'Domaines'],
                  ['nl_grape', 'Druiven'],
                  ['fr_grape', 'Cépages'],
                  ['nl_region', 'Streken'],
                  ['fr_region', 'Régions']
                ]);

                var key = this.locale + "_" + label;
                return labelMap.get(key)
            },

            localePlaceholder() {
              var placeholderMap = new Map([
                ['nl', 'Zoekterm...'],
                ['fr', 'Recherche...']
              ]);

              return placeholderMap.get(this.locale);
            },

            snippetArray(snippets, hitID, index) {
                if(typeof snippets === 'undefined') {
                    return
                };

                var matchAvailable = function(snippet) {
                  return snippet.matchLevel === 'full'
                };

                if (index === 'primary' && !snippets.some(matchAvailable)) {
                  console.log('no match');
                  return
                };
                
                if (index === 'primary') {
                    var snippetString = snippets.map(function(snippet){
                        return snippet.value;
                    }).join(", ");
                };

                if (index === 'region') {
                    var snippetString = snippets[0].text.value
                };

                if (document.getElementById(hitID)) {
                    document.getElementById(hitID).innerHTML = '';
                    document.getElementById(hitID).insertAdjacentHTML('beforeend', '<span class="text-xs">' + snippetString + '</span><br/>');
                }
            },

            url(slug, index) {
              return '/' + this.locale + "/" + ((index === 'primary') ? 'wines' : index) + "/" + slug;
            }
        }
    }
</script>


<style>
  a.link {
    text-decoration: none;
    color: white;
  }

  a.link:hover {
    font-weight: 600;
  }

  mark {
    color: lightsteelblue;
    background-color: transparent;
    text-decoration: underline;
    font-weight: 500;
  }

  .levipe-Searchbox, .levipe-SearchBox-form {
    width:100%;
    margin-bottom:2em;
  }

  .levipe-Searchbox-input {
      height: 2.5em;
      border-bottom-color: white;
      border-bottom-width: 1px;
      background: #621b18;
      color: white;
      font-size:1.25em;
      font-weight: 300;
      width:100%;
      margin-bottom: 1.5em;
  }

  .levipe-Searchbox-submit, .levipe-Searchbox-reset {
    display: none;
  }
</style>