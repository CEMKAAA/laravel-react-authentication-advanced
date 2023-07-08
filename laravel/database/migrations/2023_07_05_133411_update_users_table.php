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
        Schema::table('users', function ($table) {

            $table->enum('is_verified', ['No', 'Yes'])->after('remember_token');
            $table->string('verificationToken')->nullable()->after('remember_token');
            $table->date('email_verified_at')->nullable()->after('remember_token');
            $table->date('verification_token_expires_at')->nullable()->after('remember_token');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::table('users', function (Blueprint $table)
        {

            $table->dropColumn('is_verified_at');
            $table->dropColumn('verificationToken');
            $table->dropColumn('email_verified_at');
            $table->dropColumn('verification_token_expires_at');

        });

    }
};
