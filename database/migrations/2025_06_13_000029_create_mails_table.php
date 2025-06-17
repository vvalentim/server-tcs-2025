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
        Schema::create('mails', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('subject')->nullable();
            $table->string('sender');
            $table->string('recipient')->nullable();
            $table->longText('body')->charset('utf8mb4')->nullable();
            $table->enum('status', ['draft', 'sent', 'read'])->default('draft');
            $table->timestamp('sent_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mails');
    }
};
