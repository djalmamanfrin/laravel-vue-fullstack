<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('change_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('updated_at')->nullable()->useCurrentOnUpdate();
            $table->softDeletes();
        });

        DB::table('change_types')->insert([
            ['name' => 'Board', 'slug' => 'board'],
            ['name' => 'Image', 'slug' => 'image'],
            ['name' => 'Collection', 'slug' => 'collection'],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('change_types');
    }
};
