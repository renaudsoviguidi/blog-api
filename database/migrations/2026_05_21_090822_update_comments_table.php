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
        //
        Schema::table('comments', function (Blueprint $table) {
            $table->string('guest_name')->nullable()->after('user_id');
            $table->string('guest_email')->nullable()->after('guest_name');
            $table->string('status')->default('pending')->after('ref');
            $table->text('rejection_reason')->nullable()->after('status');
            $table->ipAddress('ip_address')->nullable()->after('rejection_reason');
            $table->text('user_agent')->nullable()->after('ip_address');
            $table->unsignedInteger('likes_count')->default(0)->after('user_agent');
            $table->softDeletes();
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::table('comments', function (Blueprint $table) {
            $table->dropColumn([
                'guest_name',
                'guest_email',
                'status',
                'rejection_reason',
                'ip_address',
                'user_agent',
                'likes_count',
                'deleted_at',
            ]);
            $table->boolean('is_approved')->default(false);
        });
    }
};
