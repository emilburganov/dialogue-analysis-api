<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('messages')->delete();
        DB::table('dialogues')->delete();

        Schema::table('dialogues', function (Blueprint $table) {
            $table->dropColumn(['manager_name', 'client_name']);
        });

        Schema::table('dialogues', function (Blueprint $table) {
            $table->foreignId('manager_id')->after('id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('client_id')->after('manager_id')->constrained('users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('dialogues', function (Blueprint $table) {
            $table->dropConstrainedForeignId('manager_id');
            $table->dropConstrainedForeignId('client_id');
        });

        Schema::table('dialogues', function (Blueprint $table) {
            $table->string('manager_name')->after('id');
            $table->string('client_name')->after('manager_name');
        });
    }
};
