<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirants', function (Blueprint $table) {
            $table->id();
            $table->text('address');
            $table->string('flyer');
            $table->unsignedBigInteger('constituency_id');
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('position_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('aspirants');
    }
};
