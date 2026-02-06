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
        Schema::create('operator_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bus_operator_id')->constrained()->onDelete('cascade');
            $table->enum('document_type', ['business_license', 'business_permit', 'tax_id', 'other']);
            $table->string('file_path');
            $table->string('original_filename');
            $table->unsignedInteger('file_size')->nullable(); // in bytes
            $table->string('mime_type', 100)->nullable();
            $table->boolean('is_verified')->default(false);
            $table->foreignId('verified_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('verified_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('bus_operator_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('operator_documents');
    }
};
