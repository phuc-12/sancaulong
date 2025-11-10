<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->string('account_no')->nullable()->after('address');      // Số tài khoản
            $table->string('account_bank')->nullable()->after('account_no'); // Mã ngân hàng (VD: VCB, MBB, ACB)
            $table->string('account_name')->nullable()->after('account_bank'); // Tên chủ tài khoản
        });
    }

    public function down()
    {
        Schema::table('facilities', function (Blueprint $table) {
            $table->dropColumn(['account_no', 'account_bank', 'account_name']);
        });
    }

};
