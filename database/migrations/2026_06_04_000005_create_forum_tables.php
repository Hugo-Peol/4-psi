<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('forum_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->text('body');                              // Corpo anonimizado
            $table->enum('category', [
                'casos_clinicos',
                'materiais',
                'indicacoes',
                'duvidas',
                'geral'
            ])->default('geral');
            $table->boolean('anonymous')->default(true);       // Postar como anônimo
            $table->string('anonymous_label')->nullable();     // Ex: "Psi Anônimo #42"
            $table->boolean('pinned')->default(false);
            $table->integer('views')->default(0);
            $table->integer('replies_count')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index(['category', 'created_at']);
            $table->index('user_id');
        });

        Schema::create('forum_replies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('forum_post_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('body');
            $table->boolean('anonymous')->default(true);
            $table->string('anonymous_label')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('forum_post_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('forum_replies');
        Schema::dropIfExists('forum_posts');
    }
};
