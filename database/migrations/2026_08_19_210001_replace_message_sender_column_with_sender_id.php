<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->unsignedBigInteger('sender_id')->nullable()->after('dialogue_id');
        });

        $senderIds = DB::table('message_senders')->pluck('id', 'slug');

        foreach ($senderIds as $slug => $senderId) {
            DB::table('messages')
                ->where('sender', $slug)
                ->update(['sender_id' => $senderId]);
        }

        DB::table('messages')
            ->whereNull('sender_id')
            ->update(['sender_id' => $senderIds['client']]);

        Schema::table('messages', function (Blueprint $table) {
            $table->dropColumn('sender');
        });

        Schema::table('messages', function (Blueprint $table) {
            $table->foreign('sender_id')->references('id')->on('message_senders');
        });
    }

    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender')->after('dialogue_id');
        });

        $senderSlugs = DB::table('message_senders')->pluck('slug', 'id');

        foreach ($senderSlugs as $senderId => $slug) {
            DB::table('messages')
                ->where('sender_id', $senderId)
                ->update(['sender' => $slug]);
        }

        Schema::table('messages', function (Blueprint $table) {
            $table->dropForeign(['sender_id']);
            $table->dropColumn('sender_id');
        });
    }
};
