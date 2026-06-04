#!/bin/bash
# ============================================================
# Script de setup do Laravel 11 dentro do container
# Executar: docker compose exec app bash /var/www/docker/scripts/install-laravel.sh
# ============================================================

set -e

echo "🚀 Iniciando instalação do Laravel 11..."

cd /var/www

# 1. Instalar Laravel 11
echo "📦 Instalando Laravel 11 via Composer..."
composer create-project laravel/laravel:^11.0 /tmp/laravel --no-interaction --prefer-dist
cp -r /tmp/laravel/. /var/www/
rm -rf /tmp/laravel

# 2. Copiar .env
echo "⚙️ Configurando .env..."
cp /var/www/.env.example /var/www/.env

# 3. Gerar APP_KEY
echo "🔑 Gerando APP_KEY..."
php artisan key:generate

# 4. Instalar Breeze (auth scaffolding - Blade)
echo "🔐 Instalando Laravel Breeze..."
composer require laravel/breeze --dev --no-interaction
php artisan breeze:install blade --no-interaction

# 5. Instalar Pacotes adicionais
echo "📦 Instalando pacotes extras..."
composer require barryvdh/laravel-dompdf --no-interaction   # PDF
composer require intervention/image:^3.0 --no-interaction   # Imagens

# 6. Instalar dependências Node
echo "🎨 Instalando dependências Node.js..."
npm install

# 7. Build do Tailwind/Vite
echo "🏗️ Compilando assets..."
npm run build

# 8. Permissões de storage
echo "📁 Ajustando permissões..."
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo ""
echo "✅ Laravel 11 instalado com sucesso!"
echo "   App disponível em: http://localhost:8080"
