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
        //usamos el table de usuarios para poder tener que cada usuario con una foreging key  tenga su softdelte
        Schema::table('users', function (Blueprint $table) {
        $table->foreignId("role_id")->nullable()->constrained("roles")->restrictOnDelete();
        $table->softDeletes();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //lo contrario aqui lo matamos
        Schema::table('users', function (Blueprint $table) {
        $table->dropConstrainedForeignId("role_id");
        $table->dropSoftDeletes();
        });
    }
};
