<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetasearchTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('metasearch', function (Blueprint $table) {
            $table->id();
            $table->text('meta_text');
            $table->text('raw_text');
            $table->morphs('searchable');
            $table->timestamps();

            $table->unique(['searchable_id', 'searchable_type'], 'mp_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('metasearch');
    }
}
