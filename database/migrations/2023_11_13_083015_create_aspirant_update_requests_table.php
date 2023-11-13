<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('aspirant_update_requests', function (Blueprint $table) {
            $table->id();
            $table->text('address');
            $table->string('flyer');
            $table->enum(
                'status',
                [
                    'Accepted',
                    'Declined'
                ]
            );
            $table->timestamp('status_applied_at');
            $table->unsignedBigInteger('aspirant_id');
            $table->unsignedBigInteger('constituency_id');
            $table->unsignedBigInteger('party_id');
            $table->unsignedBigInteger('position_id');
            $table->timestamps();
        });
    }
    
    public function down(): void
    {
        Schema::dropIfExists('aspirant_update_requests');
    }
};
