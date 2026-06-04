<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('billing_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');    // Psicólogo
            $table->foreignId('patient_id')->constrained()->onDelete('cascade'); // Paciente
            $table->decimal('amount', 10, 2);                // Valor a cobrar
            $table->date('due_date');                         // Data de vencimento
            $table->timestamp('paid_at')->nullable();         // Data do pagamento
            $table->enum('status', ['pending', 'paid', 'late', 'cancelled'])->default('pending');
            $table->string('payment_link')->nullable();       // Link Asaas/PIX
            $table->string('pix_code')->nullable();           // Código PIX copia-e-cola
            $table->string('description')->nullable();        // Descrição da cobrança
            $table->integer('session_count')->default(1);     // Número de sessões
            $table->boolean('notified')->default(false);      // Cobrança notificada?
            $table->timestamp('notified_at')->nullable();
            $table->string('external_id')->nullable();        // ID na API Asaas
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index(['user_id', 'due_date']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('billing_cycles');
    }
};
