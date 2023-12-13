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
        Schema::create('note', function (Blueprint $table) {
            $table->id();
            $table->integer('id_user')->index();
            $table->string('nome',36)->index();
            $table->datetime('datanasc');
            $table->string('ente',1);
			$table->text('note');
            $table->timestamps();
			$table->index(['nome', 'datanasc']);
			$table->index(['nome', 'datanasc', 'ente']);
        });    
	}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('note');
    }
};
