<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->enum('role', ['standard', 'manager', 'admin', 'super'])
                ->default('standard')
                ->after('remember_token');
            $table->boolean('must_change_password')
                ->default(false)
                ->after('role');
        });

        $rootEmail = env('BUSINESS_ADMIN_EMAIL', 'root@localhost');

        DB::table('users')->insertOrIgnore([
            'name' => 'Admin',
            'email' => $rootEmail,
            'password' => Hash::make('Admin'),
            'role' => 'super',
            'must_change_password' => true,
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('users')->where('email', env('BUSINESS_ADMIN_EMAIL', 'root@localhost'))->delete();

        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn(['role', 'must_change_password']);
        });
    }
};
