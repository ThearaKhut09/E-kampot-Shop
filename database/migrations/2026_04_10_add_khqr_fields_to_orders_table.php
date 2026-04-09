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
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'qr_string')) {
                $table->text('qr_string')->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('orders', 'md5_hash')) {
                $table->string('md5_hash', 64)->nullable()->after('payment_id');
            }
            if (!Schema::hasColumn('orders', 'payment_expires_at')) {
                $table->timestamp('payment_expires_at')->nullable()->after('payment_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = [];
            if (Schema::hasColumn('orders', 'qr_string')) $columns[] = 'qr_string';
            if (Schema::hasColumn('orders', 'md5_hash')) $columns[] = 'md5_hash';
            if (Schema::hasColumn('orders', 'payment_expires_at')) $columns[] = 'payment_expires_at';
            if (!empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
