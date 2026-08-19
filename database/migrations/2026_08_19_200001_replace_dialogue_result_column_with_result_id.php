<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('dialogues', function (Blueprint $table) {
            $table->unsignedBigInteger('result_id')->nullable()->after('client_id');
        });

        $resultIds = DB::table('dialogue_results')->pluck('id', 'slug');

        foreach ($resultIds as $slug => $resultId) {
            DB::table('dialogues')
                ->where('result', $slug)
                ->update(['result_id' => $resultId]);
        }

        DB::table('dialogues')
            ->whereNull('result_id')
            ->update(['result_id' => $resultIds['not_bought']]);

        Schema::table('dialogues', function (Blueprint $table) {
            $table->dropColumn('result');
        });

        Schema::table('dialogues', function (Blueprint $table) {
            $table->foreign('result_id')->references('id')->on('dialogue_results');
        });
    }

    public function down(): void
    {
        Schema::table('dialogues', function (Blueprint $table) {
            $table->string('result')->default('not_bought')->after('client_id');
        });

        $resultSlugs = DB::table('dialogue_results')->pluck('slug', 'id');

        foreach ($resultSlugs as $resultId => $slug) {
            DB::table('dialogues')
                ->where('result_id', $resultId)
                ->update(['result' => $slug]);
        }

        Schema::table('dialogues', function (Blueprint $table) {
            $table->dropForeign(['result_id']);
            $table->dropColumn('result_id');
        });
    }
};
