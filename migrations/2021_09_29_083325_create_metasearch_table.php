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
            $table->mediumText('mp_text');
            $table->morphs('model');
            $table->timestamps();

            $table->unique(['model_id', 'model_type'], 'mp_unique');
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
