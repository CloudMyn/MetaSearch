<?php

namespace CloudMyn\MetaSearch\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\DB;

/**
 *  @method static \CloudMyn\MetaSearch\MetaSearch|null search(string $text)
 */
class MetaSearch extends Model
{
    protected $guarded  =   [];

    protected $table    =   "metasearch";

    protected $appends  =   ['suggestions'];

    /**
     *  Costume attribute for 'suggestions'
     *
     *  @return array
     */
    public function getSuggestionsAttribute(): array
    {
        return [];
    }

    /**
     *  Relational method
     *
     *  @return Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function searchable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     *  Cari objek berdasarkan text yang diberikan
     *
     *  @param  mixed   $builder
     *  @param  string  $text
     *
     *  @return mixed
     */
    public function scopeSearch(Builder $builder, string $searchable, string $text)
    {
        $start_exec =   microtime(true);

        $enable_suggestion = config('metasearch.enable_suggestion', false);

        $text       =   trim($text);
        $keyword    =   soundex($text);

        $search_results =   [];
        $suggestions    =   [];

        $total_data = 0;

        $where_query = [
            ['meta_text', 'LIKE', "%{$keyword}%"],
            ['searchable_type', '=', $searchable]
        ];

        DB::table($this->table)->where($where_query)->orderBy('id', "DESC")
            ->chunk(
                120,
                function (\Illuminate\Support\Collection $data)
                use ($text, $keyword, &$search_results, &$suggestions, &$total_data) {

                    foreach ($data as $metasearch) {

                        $total_data++;

                        $meta_text  =   trim($metasearch->meta_text);
                        $raw_taxt   =   trim($metasearch->raw_text);

                        $meta_text_arr  =   explode(" ", $meta_text);
                        $raw_text_arr   =   explode(" ", $raw_taxt);

                        if (!is_array($meta_text_arr) && !is_array($raw_text_arr)) {

                            $metasearch->suggestions = [
                                $meta_text_arr  =>  $raw_text_arr,
                            ];

                            continue;
                        }

                        // $matches = $this->serialize($meta_text_arr, $keyword, $raw_text_arr);

                        $search_results[] = new SearchResult($metasearch, $text, []);

                        $suggestions = array_merge($suggestions, []);
                    }

                    // ...
                },
            );


        $end_exec = microtime(true);

        return  [
            'suggestions'       => array_values(array_unique($suggestions)),
            'execution_time'    => round(($end_exec - $start_exec) * 100) . "ms",
            'data'              => $search_results,
            'dt'    =>  $total_data,
        ];
    }

    public function getSuggestions()
    {
    }

    protected function serialize(array $array, $keyword, array $raw_text_arr): array
    {
        $index      =   0;
        $hasMatch   =   "";

        $suggestions    =   [];

        foreach ($array as $soundex) {

            // avoid duplicated valud
            if (strtolower($hasMatch) === strtolower($raw_text_arr[$index])) {
                $index++;
                continue;
            }

            // compare the soundex from databas with the keyword
            if ($soundex === $keyword) {
                $suggestions[]  =   ($hasMatch = strtolower($raw_text_arr[$index]));
            }

            $index++;
        }

        return  $suggestions;
    }

    // ...
}
