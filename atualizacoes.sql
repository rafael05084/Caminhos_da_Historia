-- =============================================
-- Caminhos da História - Uruguaiana
-- Migração: causos + linha do tempo (imagens)
-- Executar no mesmo banco: loginsimples3
-- (rodar depois de locais.sql já ter sido executado)
-- =============================================

-- ---------- Causos enviados pela comunidade ----------
DROP TABLE IF EXISTS `causos`;
CREATE TABLE IF NOT EXISTS `causos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `local_id` int NOT NULL,
  `usuario_id` int NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `texto` text NOT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'pendente', -- pendente | aprovado | rejeitado
  `data_cadastro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `local_id` (`local_id`),
  KEY `usuario_id` (`usuario_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------- Imagens por período (linha do tempo do modal) ----------
DROP TABLE IF EXISTS `locais_imagens`;
CREATE TABLE IF NOT EXISTS `locais_imagens` (
  `id` int NOT NULL AUTO_INCREMENT,
  `local_id` int NOT NULL,
  `periodo` varchar(100) NOT NULL,
  `imagem` varchar(255) NOT NULL,
  `legenda` varchar(255),
  `ordem` int DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `local_id` (`local_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- ---------- Nova categoria "Patrimônios Imateriais" ----------
-- (a categoria não precisa de tabela própria, já que `locais.categoria` é texto livre;
--  basta selecioná-la no formulário do admin-locais.php)
