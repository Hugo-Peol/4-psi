<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BillingCycle extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'patient_id', 'amount', 'due_date', 'paid_at',
        'status', 'payment_link', 'pix_code', 'description',
        'session_count', 'notified', 'notified_at', 'external_id',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'due_date'     => 'date',
        'paid_at'      => 'datetime',
        'notified_at'  => 'datetime',
        'notified'     => 'boolean',
        'session_count'=> 'integer',
    ];

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function receipt()
    {
        return $this->hasOne(Receipt::class);
    }

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeLate($query)
    {
        return $query->where('status', 'late');
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                     ->where('due_date', '<', now()->toDateString());
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public function markAsPaid(): void
    {
        $this->update(['status' => 'paid', 'paid_at' => now()]);
    }

    public function statusBadge(): array
    {
        return match ($this->status) {
            'paid'      => ['label' => 'Pago',      'class' => 'badge-paid'],
            'late'      => ['label' => 'Atrasado',  'class' => 'badge-late'],
            'pending'   => ['label' => 'Pendente',  'class' => 'badge-pending'],
            'cancelled' => ['label' => 'Cancelado', 'class' => 'badge-cancelled'],
            default     => ['label' => 'Desconhecido', 'class' => ''],
        };
    }

    public function whatsappChargeMessage(): string
    {
        $patient = $this->patient->name;
        $value   = 'R$ ' . number_format($this->amount, 2, ',', '.');
        $due     = $this->due_date->format('d/m/Y');
        return "Olá, {$patient}! 😊 Passando para lembrar que sua consulta no valor de *{$value}* vence em *{$due}*. Qualquer dúvida, é só me chamar! 🙏";
    }
}
