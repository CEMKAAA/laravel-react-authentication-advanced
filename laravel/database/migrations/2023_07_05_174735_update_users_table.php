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

        // Check if the column exists before modifying it
        if (Schema::hasColumn('users', 'city')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('city')->nullable()->default('')->change();
            });
        }

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table)
        {

            $table->dropColumn('city');

        });

    }
};
