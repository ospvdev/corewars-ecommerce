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
        Schema::create('web_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('web_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('slug')->unique();
            $table->timestamps();
        });

        Schema::create('web_role_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('web_users')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('web_roles')->onDelete('cascade');
            $table->timestamps();
        });

        Schema::create('web_permission_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('role_id')->constrained('web_roles')->onDelete('cascade');
            $table->foreignId('permission_id')->constrained('web_permissions')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('web_roles');
        Schema::dropIfExists('web_permissions');
        Schema::dropIfExists('web_role_user');
        Schema::dropIfExists('web_permission_role');
    }
};