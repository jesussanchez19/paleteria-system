<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            if (Schema::hasColumn('sale_details', 'price')) {
                $table->dropColumn('price');
            }
        });
    }

    public function down()
    {
        Schema::table('sale_details', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable();
        });
    }
};
