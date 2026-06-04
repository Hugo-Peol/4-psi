-- Init SQL para criação do banco e configurações iniciais
-- Executado automaticamente no primeiro `docker compose up`

CREATE DATABASE IF NOT EXISTS `4psi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Garantir que o usuário tem privilégios completos
GRANT ALL PRIVILEGES ON `4psi`.* TO '4psi_user'@'%';
FLUSH PRIVILEGES;

-- Timezone padrão Brasil
SET GLOBAL time_zone = '-03:00';
