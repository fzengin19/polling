<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUuidToPollsTable extends Migration
{
    public function up()
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->string('uuid', 36)->unique()->after('id');
        });
    }

    public function down()
    {
        Schema::table('polls', function (Blueprint $table) {
            $table->dropColumn('uuid');
        });
    }
}
