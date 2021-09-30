<?php

namespace CloudMyn\MetaSearch\Models;

use CloudMyn\MetaSearch\Models\MetaSearch;

class SearchResult
{
    public int $metasearch_id;

    public string $input, $meta_text, $raw_text;

    public string $created_at, $updated_at;

    public int $searchable_id;
    public string $searchable_type;

    public array $matches = [];

    public function __construct($metasearch, string $input, array $matches)
    {
        $this->metasearch_id    =   $metasearch->id;
        $this->input            =   $input;
        $this->matches          =   $matches;
        $this->meta_text        =   $metasearch->meta_text;
        $this->raw_text         =   $metasearch->raw_text;
        $this->created_at       =   $metasearch->created_at;
        $this->updated_at       =   $metasearch->updated_at;
        $this->searchable_id    =   $metasearch->searchable_id;
        $this->searchable_type  =   $metasearch->searchable_type;
    }

    /**
     *  @return CloudMyn\MetaSearch\Models\MetaSearch;
     */
    public function metasearch(): MetaSearch
    {
        return MetaSearch::find($this->metasearchId);
    }

    /**
     *  @return \Illuminate\Database\Eloquent\Model
     */
    public function searchable()
    {
        return "\\" . $this->searchable_type::find($this->metasearchId);
    }
}
