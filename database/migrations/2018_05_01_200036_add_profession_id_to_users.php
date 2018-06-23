<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProfessionIdToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */

    // Esta migracion la puedo eliminar. La dejo como ejemplo
    // ya no sirve porque he llevado estos campos a la tabla userProfile
    //  public function up()
    // {
    //     Schema::table('users', function (Blueprint $table) {
    //         $table->unsignedInteger('profession_id')->nullable();
    //         $table->foreign('profession_id')->references('id')->on('professions');
    //     });
    // }

    // /**
    //  * Reverse the migrations.
    //  *
    //  * @return void
    //  */
    // public function down()
    // {
    //     Schema::table('users', function (Blueprint $table) {
    //         $table->dropForeign(['profession_id']);
    //         $table->dropColumn('profession_id');
    //     });
    // }
}
