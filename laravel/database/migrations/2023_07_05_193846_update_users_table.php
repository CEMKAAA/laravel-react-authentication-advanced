<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {

        Schema::table('users', function (Blueprint $table)
        {

            $table->string('state')->nullable()->default('')->change();
            $table->string('country')->nullable()->default('')->change();
            $table->string('pincode')->nullable()->default('')->change();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table)
        {

            $table->dropColumn('state');
            $table->dropColumn('country');
            $table->dropColumn('pincode');

        });

    }
};
