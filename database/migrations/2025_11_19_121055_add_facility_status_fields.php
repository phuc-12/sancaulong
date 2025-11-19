<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('facilities', function (Blueprint $table) {
            // Sử dụng boolean thay vì tinyInteger cho rõ ràng hơn
            $table->boolean('is_active')->default(false)->after('status')->comment('Cơ sở đã được kích hoạt');
            $table->boolean('need_reapprove')->default(true)->after('is_active')->comment('Cần admin phê duyệt');

            // Sử dụng enum để giới hạn giá trị
            $table->enum('pending_request_type', ['activate', 'sensitive_update'])
                ->nullable()
                ->after('need_reapprove')
                ->comment('Loại yêu cầu đang chờ duyệt');
        });
    }

    public function down()
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['is_active', 'need_reapprove', 'pending_request_type']);
        });
    }
};
