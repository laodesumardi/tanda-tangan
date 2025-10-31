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
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('document_id')->constrained()->onDelete('cascade');
            $table->string('signer_name');
            $table->string('signer_email')->nullable();
            $table->string('signer_position')->nullable();
            $table->text('signature_data'); // Base64 encoded signature image
            $table->string('ip_address')->nullable();
            $table->timestamp('signed_at');
            $table->text('metadata')->nullable(); // JSON for additional data
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('signatures');
    }
};
