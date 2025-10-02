<?php

use Hyperf\Database\Schema\Schema;
use Hyperf\Database\Schema\Blueprint;
use Hyperf\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('accounts_withdraws', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_id')->index();
            $table->string('method');
            $table->decimal('amount', 10, 2);
            $table->boolean('scheduled')->default(false);
            $table->dateTime('scheduled_for')->nullable();
            $table->enum('status', ['pending', 'done', 'denied'])->default('pending');
            $table->boolean('done')->default(false);
            $table->boolean('error')->default(false);
            $table->string('error_reason')->nullable();     
            $table->dateTime('done_at')->nullable();
            $table->dateTime('denied_at')->nullable();
            $table->timestamps();
            
            $table->index(['scheduled', 'done', 'error', 'scheduled_for'], 'idx_scheduled_withdraws');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_withdraws');
    }
};
