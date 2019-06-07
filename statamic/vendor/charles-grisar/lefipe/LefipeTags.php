<?php

namespace Statamic\Addons\Lefipe;

use Statamic\Extend\Tags;
use Statamic\API\Entry;
use Statamic\API\Term;
use Statamic\Data\Data;

class LefipeTags extends Tags
{
    private function wines( $filters ) {
        return "";
    }

    private function templateFilters( $filtered, $remainingFilters ) {
        $template = '<div class="mb-4">';
    
        foreach( $remainingFilters as $remainingFilter => $v ) {
            $remainingFilterTemplate = '<div class="mb-4">';
            $remainingFilterTemplate .= '<h6 class="text-black font-semibold uppercase mb-1">' . $v . '</h6>';
            foreach( $filtered[ $remainingFilter ] as $term ) {
                $remainingFilterTemplate .= '<input type="checkbox" value="' . $term . '"/>';
                $remainingFilterTemplate .= '<label for="' .$term . '">' . $term . '</label></br>';
            };
            $remainingFilterTemplate .= '</div>';
            $template .= $remainingFilterTemplate;
        };

        return $template . '</div>';
    }

    /**
     * The {{ lefipe }} tag
     *
     * @return string|array
     */
    public function index()
    {
        $filters = [];
        $filtered = [];
        $filter = $this->getParam( "filter" );
        $filterValue = $this->getParam( "filterValue" );

        $remainingFilters = array_diff_key( array( 'g' => 'grape', 'r' => 'region', 'c' => 'color' ), array( $filter => 'dummy' ) );
        foreach( $remainingFilters as $remainingFilter => $value ){
            $filters[ $remainingFilter ] = [];
        };

        //
        $entries = Entry::whereCollection( 'wines' );

        foreach ( $entries as $entry ) {
            if ( $entry->get( $filter ) && ( array_search( $filterValue, $entry->get( $filter ) ) !== false ) ) {
                $filtered[] = $entry->get('id');
                foreach( $remainingFilters as $remainingFilter => $value ) {
                    if ( $remainingFilterValue = $entry->get( $remainingFilter ) ) {
                        $filters[ $remainingFilter ] = array_merge( $filters[ $remainingFilter ], $remainingFilterValue );
                    };
                    if( !empty( $filters[ $remainingFilter ] ) ) {
                        array_unique( $filters[ $remainingFilter ] );
                    }
                }
            }
        }

        /* We now have 2 arrays:
        ** - filtered: the ids of the wines that belong to filterValue
        ** - filters: the filters that remain in the filtered wines
        **
        ** Let's build the template
        */

        // Now, let's create the template

        return $this->templateFilters( $filters, $remainingFilters ) . $this->wines( $filters );
    }

    /**
     * The {{ lefipe:example }} tag
     *
     * @return string|array
     */
    public function example()
    {
        //
    }
}
