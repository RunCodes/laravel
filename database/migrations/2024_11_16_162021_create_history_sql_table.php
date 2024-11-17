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
        Schema::create('history_sql', function (Blueprint $table) {
            $table->increments('id'); // 自动递增的主键，已经定义了主键
            $table->integer('user_id')->nullable()->comment('用户id');
            $table->text('sql')->nullable();
            $table->text('error')->nullable();
            $table->timestamp('created_at')->nullable();
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('history_sql');
    }
};
