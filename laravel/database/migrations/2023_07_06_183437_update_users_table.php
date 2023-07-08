<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;


class UpdateUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->timestamp('verification_token_expires_at')->nullable()->after('verificationToken')->change();
        });

        // Set a default value for existing rows in the table
        DB::table('users')->update(['email_verified_at' => now()]);
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('verification_token_expires_at');
        });
    }
}
