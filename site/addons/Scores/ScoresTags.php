<?php

namespace Statamic\Addons\Scores;

use Statamic\API\Entry;
use Statamic\Extend\Tags;

class ScoresTags extends Tags
{
    
    private function starsToScore( $stars )
    {
        $score = 0;
        $stars == 1 ? $score = 85 : 0;
        $stars == 2 ? $score = 90 : 0;
        $stars == 3 ? $score = 95 : 0;
        return $score;
    }

    private function getScoresFromSlug( $slug )
    {
        $variants = Entry::whereSlug( $slug, 'wines')->get('variants');

        // sort them descending to have the youngest vintage as first entry
        usort($variants, function ( $item1, $item2 ) {
            return $item2['millesime'] <=> $item1['millesime'];
        });

        // return if we don't have a score
        if( !array_key_exists('scores', $variants[0] ) )
        {
            return false;
        }

        $scores = $variants[0]['scores'];
    }
    /**
     * The {{ scores:count slug }} tag
     *
     * @return string|array
     */

     public function aggregateString( $rating, $count )
     {
        $aggregateString = "\"aggregateRating\":{";
        $typeString = "\"@type\": \"AggregateRating\",";
        $ratingValueString = "\"ratingValue\": \"{$rating}\",";
        $bestRatingString = '"bestRating": "100",';
        $reviewCountString = "\"reviewCount\": \"{$count}\"},";

        return $aggregateString . $typeString . $ratingValueString . $bestRatingString . $reviewCountString;
     }

    /**
     * The {{ scores:aggregate slug }} tag
     *
     * @return string|array
     */
    public function aggregate()
    {
        
        $slug = $this->getParam('slug');

        $variants = Entry::whereSlug( $slug, 'wines')->get('variants');

        // sort them descending to have the youngest vintage as first entry
        usort($variants, function ( $item1, $item2 ) {
            return $item2['millesime'] <=> $item1['millesime'];
        });

        // return if we don't have a score
        if( !array_key_exists('scores', $variants[0] ) )
        {
            return "";
        }

        $scores = $variants[0]['scores'];

        $total_score = 0;
        foreach( $scores as $score )
        {
            $total_score += $score['maxscore'] == '100' ? $score['score'] : 0 ;
            $total_score += $score['maxscore'] == '20' ? $score['score'] * 2.5 : 0;
            $total_score += $score['maxscore'] == '3' ? this.starsToScore(3) : 0;
        }

        return $this->aggregateString ( $total_score / count( $scores ), count( $scores ) );
    }

    /**
     * The {{ scores }} tag
     *
     * @return string|array
     */

    public function index()
    {

    }

}
