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
        Schema::table('products', function (Blueprint $table) {
            $table->string('image')->nullable()->after('status'); // Single main image
            $table->json('gallery')->nullable()->after('image'); // Gallery images
            $table->boolean('featured')->default(false)->after('is_featured'); // Featured flag for admin
            $table->string('meta_title')->nullable()->after('review_count'); // SEO meta title
            $table->text('meta_description')->nullable()->after('meta_title'); // SEO meta description
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['image', 'gallery', 'featured', 'meta_title', 'meta_description']);
        });
    }
};
