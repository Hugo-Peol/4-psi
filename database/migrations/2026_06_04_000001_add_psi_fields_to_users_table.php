<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('username')->unique()->nullable()->after('name');
            $table->string('crp', 20)->nullable()->after('username');
            $table->string('phone', 20)->nullable()->after('crp');
            $table->text('bio')->nullable()->after('phone');
            $table->string('city', 100)->nullable()->after('bio');
            $table->string('state', 2)->nullable()->after('city');
            $table->string('approach', 100)->nullable()->after('state'); // Abordagem clínica
            $table->string('avatar')->nullable()->after('approach');
            $table->string('session_value', 20)->nullable()->after('avatar'); // Valor por sessão
            $table->boolean('is_admin')->default(false)->after('session_value');
            $table->boolean('profile_public')->default(true)->after('is_admin');
            $table->string('whatsapp', 20)->nullable()->after('profile_public');
            $table->string('instagram', 100)->nullable()->after('whatsapp');
            $table->string('specialty')->nullable()->after('instagram'); // Especialidade
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'username', 'crp', 'phone', 'bio', 'city', 'state',
                'approach', 'avatar', 'session_value', 'is_admin',
                'profile_public', 'whatsapp', 'instagram', 'specialty'
            ]);
        });
    }
};
