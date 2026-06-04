<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('billing_cycle_id')->nullable()->constrained()->onDelete('set null');
            $table->string('cid_code', 20)->nullable();       // Código CID-10
            $table->date('session_date');                      // Data da sessão
            $table->decimal('amount', 10, 2);                 // Valor do recibo
            $table->integer('session_count')->default(1);     // Nº de sessões no recibo
            $table->string('health_plan')->nullable();         // Convênio (SulAmérica, Bradesco)
            $table->text('observations')->nullable();          // Observações clínicas
            $table->string('pdf_path')->nullable();            // Caminho do PDF gerado
            $table->string('receipt_number')->unique();        // Número sequencial do recibo
            $table->timestamps();

            $table->index(['user_id', 'session_date']);
            $table->index('patient_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
