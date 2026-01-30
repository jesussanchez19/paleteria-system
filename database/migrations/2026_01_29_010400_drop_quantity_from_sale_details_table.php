<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('sale_details', 'quantity')) {
                $table->dropColumn('quantity');
            }
        });
    }

    public function down()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->integer('quantity')->nullable();
        });
    }
};
