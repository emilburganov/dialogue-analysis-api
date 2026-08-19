<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('role_id')->nullable()->after('password');
        });

        $roleIds = DB::table('roles')->pluck('id', 'slug');

        foreach ($roleIds as $slug => $roleId) {
            DB::table('users')
                ->where('role', $slug)
                ->update(['role_id' => $roleId]);
        }

        DB::table('users')
            ->whereNull('role_id')
            ->update(['role_id' => $roleIds['client']]);

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreign('role_id')->references('id')->on('roles');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('role')->default('client')->after('password');
        });

        $roleSlugs = DB::table('roles')->pluck('slug', 'id');

        foreach ($roleSlugs as $roleId => $slug) {
            DB::table('users')
                ->where('role_id', $roleId)
                ->update(['role' => $slug]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['role_id']);
            $table->dropColumn('role_id');
        });
    }
};
