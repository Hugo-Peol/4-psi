<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Psicólogo dono
            $table->string('name');
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->decimal('session_value', 10, 2)->nullable();  // Valor por sessão
            $table->integer('package_sessions')->nullable();       // Pacote de sessões
            $table->integer('sessions_done')->default(0);          // Sessões realizadas
            $table->text('notes')->nullable();                     // Observações internas
            $table->boolean('active')->default(true);
            $table->string('cpf', 14)->nullable();
            $table->date('birth_date')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Índices para performance
            $table->index('user_id');
            $table->index(['user_id', 'active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
