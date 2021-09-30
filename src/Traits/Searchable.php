<?php

namespace CloudMyn\MetaSearch\Traits;

use CloudMyn\MetaSearch\Models\MetaSearch;
use Illuminate\Database\Eloquent\Relations\MorphOne;

trait Searchable
{

    /**
     *  Metode untuk menyimpan keyword metasearch
     *
     *  @return bool
     */
    public function saveMs(): bool
    {
        $metaSearch = new MetaSearch();

        $text   =   trim($this->getMSText());

        if (is_null($text) or $text === "")
            throw new \UnexpectedValueException('the return value of this method \'getMSText\' can\'t be emptied');

        $m_text = $this->translateChar($text);

        $metaSearch->meta_text  =   $m_text;
        // remove non alphabet
        $metaSearch->raw_text   =   preg_replace("/[^A-Za-z ]/", '', $text);;

        $metaSearch->searchable()->associate($this);

        return (bool) $metaSearch->save();
    }

    /**
     *  Metode untuk meng-update metasearch
     *
     *  @return bool
     */
    public function updateMs(): bool
    {
        try {

            $raw_text   =   trim($this->getMSText());

            $text   =   $this->translateChar($raw_text);

            $model = $this->metasearch;

            $model->meta_text   =   $text;
            $model->raw_text    =   $raw_text;

            return (bool) $model->save();

            // ...
        } catch (\Throwable $th) {

            return false;
        }
    }

    /**
     *  Metode untuk menghapus keyword metasearch
     *
     *  @return int|false
     */
    public function deleteMs()
    {
        return $this->metasearch()->delete();
    }

    /**
     *  Relatinal method
     *
     *  @return Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function metasearch(): MorphOne
    {
        return $this->morphOne(MetaSearch::class, 'searchable');
    }

    /**
     *  Metode untuk mengkonversi text biasa menjadi text tipe soundex
     *
     *  @param  string  $text
     *  @return string  soundex text
     */
    protected function translateChar(string $text): string
    {
        $_tx_arr    =   explode(" ", $text);

        if (!is_array($_tx_arr))
            return soundex($text);

        $text   =   "";

        foreach ($_tx_arr as $_txt) {
            $text .= " " . soundex($_txt);
        }

        return $text;
    }
}
