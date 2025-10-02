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
        Schema::create('accounts_withdraws_pix', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('account_withdraw_id');
            $table->string('type');
            $table->string('key');
            $table->timestamps();
            
            $table->index('account_withdraw_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accounts_withdraws_pix');
    }
};
