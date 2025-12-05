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
        Schema::create('chat_histories', function (Blueprint $table) {
            // 1. id: BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY
            $table->id();

            // 2. user_id: BIGINT UNSIGNED, Allow NULL
            // Lưu ý: Nếu có liên kết khóa ngoại với bảng users, bạn có thể thêm ->constrained()
            $table->unsignedBigInteger('user_id')->nullable();

            // 3. message: TEXT, Not NULL
            $table->text('message');

            // 4. reply: LONGTEXT, Allow NULL
            $table->longText('reply')->nullable();

            // 5. intent: VARCHAR(255), Allow NULL
            $table->string('intent', 255)->nullable();

            // 6. entities: JSON, Allow NULL
            $table->json('entities')->nullable();

            // 7. session_key: VARCHAR(255), Allow NULL
            $table->string('session_key', 255)->nullable();

            // 8. ip: VARCHAR(45), Allow NULL
            $table->string('ip', 45)->nullable();

            // 9. user_agent: VARCHAR(255), Allow NULL
            $table->string('user_agent', 255)->nullable();

            // 10 & 11. created_at, updated_at: TIMESTAMP
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chat_histories');
    }
};