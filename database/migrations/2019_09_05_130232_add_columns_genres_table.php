<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsGenresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->integer('status')->after('genre_name');
        });
    }

    public function down()
    {
        Schema::table('genres', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
