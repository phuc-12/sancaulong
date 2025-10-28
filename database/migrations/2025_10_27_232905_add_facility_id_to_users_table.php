<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    // ... trong file migration ...add_facility_id_to_users_table.php

public function up(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Bước 1: Chỉ thêm cột nếu nó chưa tồn tại
        if (!Schema::hasColumn('users', 'facility_id')) {
            $table->integer('facility_id') 
                  ->nullable()
                  ->after('updated_at'); // Đặt vị trí cột (tùy chọn)
        }
    });

    // Bước 2: Thêm khóa ngoại (có thể cần kiểm tra khóa ngoại đã tồn tại chưa)
    // Tạm thời có thể chạy luôn, nếu lỗi thì thêm kiểm tra khóa ngoại
    Schema::table('users', function (Blueprint $table) {
         // Kiểm tra xem cột có tồn tại không trước khi thêm khóa ngoại
         if (Schema::hasColumn('users', 'facility_id')) {
             $table->foreign('facility_id')
                   ->references('facility_id')
                   ->on('facilities')
                   ->onDelete('set null');
         }
    });
}

// Hàm down() cũng nên kiểm tra trước khi xóa
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        // Chỉ xóa nếu cột tồn tại
        if (Schema::hasColumn('users', 'facility_id')) {
             // Thử xóa khóa ngoại trước (có thể cần kiểm tra tên khóa ngoại)
             try {
                 $table->dropForeign(['facility_id']); 
             } catch (\Exception $e) {
                 // Bỏ qua lỗi nếu khóa ngoại không tồn tại
             }
             $table->dropColumn('facility_id'); 
        }
    });
}
};