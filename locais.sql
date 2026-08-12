-- =============================================
-- Caminhos da História - Uruguaiana
-- Tabela `locais` (mapa histórico interativo)
-- Executar no mesmo banco: loginsimples3
-- =============================================

DROP TABLE IF EXISTS `locais`;
CREATE TABLE IF NOT EXISTS `locais` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `descricao` text NOT NULL,
  `historia` text,
  `categoria` varchar(100) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `imagem` varchar(255),
  `periodo_historico` varchar(100),
  `curiosidades` text,
  `importancia` text,
  `data_cadastro` timestamp DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- Locais iniciais (coordenadas aproximadas — ajuste fino pelo painel admin)
INSERT INTO `locais`
(`nome`, `descricao`, `historia`, `categoria`, `latitude`, `longitude`, `periodo_historico`, `curiosidades`, `importancia`)
VALUES
(
  'Ponte Internacional',
  'Ponte rodoferroviária que liga Uruguaiana (Brasil) a Paso de los Libres (Argentina) sobre o Rio Uruguai.',
  'Oficialmente Ponte Internacional Getúlio Vargas - Agustín P. Justo, foi construída a partir de 1942 e aberta ao tráfego em 12 de outubro de 1945, sendo inaugurada oficialmente em 1947. Na época, foi considerada uma das maiores obras de engenharia da América Latina.',
  'Integração Regional',
  -29.74310000, -57.09310000,
  'Século XX (1942-1947)',
  'Com 1.419 metros de extensão, tornou Uruguaiana um dos maiores portos secos do país.',
  'Símbolo da integração entre Brasil e Argentina e um dos principais marcos históricos e econômicos da cidade.'
),
(
  'Praça do Barão',
  'Praça central de Uruguaiana, ponto de encontro tradicional da comunidade no coração do Centro histórico.',
  'Uma das praças mais antigas da cidade, testemunha da formação e do crescimento urbano de Uruguaiana desde o século XIX.',
  'Patrimônio Histórico',
  -29.75550000, -57.08780000,
  'Século XIX',
  'Está cercada por importantes edifícios históricos do Centro da cidade.',
  'Referência de convivência social e ponto de partida para conhecer o patrimônio histórico do Centro.'
),
(
  'CTG Sinuelo do Pago',
  'Centro de Tradições Gaúchas localizado no bairro Santo Inácio, dedicado à preservação da cultura gaúcha.',
  'Um dos CTGs mais tradicionais de Uruguaiana, promove rodeios, festivais e atividades ligadas ao movimento tradicionalista gaúcho ao longo do ano.',
  'Tradição Gaúcha',
  -29.74600000, -57.02900000,
  'Século XX',
  'Recebe eventos tradicionalistas de grande público, como rodeios e festivais artísticos.',
  'Um dos principais espaços de preservação e difusão da cultura gaúcha na região da fronteira oeste.'
),
(
  'Antiga Rua 28',
  'Antiga via histórica localizada na região oeste do Centro de Uruguaiana, próxima ao rio.',
  'Um dos logradouros mais antigos da cidade, ligado à formação dos primeiros núcleos urbanos de Uruguaiana.',
  'Patrimônio Histórico',
  -29.76400000, -57.09400000,
  'Século XIX',
  'O nome remete à antiga numeração/denominação das ruas no traçado original da cidade.',
  'Registro da malha urbana original de Uruguaiana e de sua ocupação histórica.'
),
(
  'Clube Social Negro de Uruguaiana',
  'Espaço histórico de organização e resistência da comunidade negra uruguaianense.',
  'Associação criada pela comunidade afro-descendente da cidade como espaço de sociabilidade, cultura e enfrentamento ao racismo em um período de forte segregação social.',
  'Cultura',
  -29.75800000, -57.09550000,
  'Século XX',
  'Localização exata ainda em confirmação junto a fontes e moradores da comunidade.',
  'Patrimônio histórico e cultural fundamental para a memória afro-brasileira de Uruguaiana.'
),
(
  'Atual Mesquita',
  'Edificação religiosa localizada próxima ao Centro, hoje voltada à comunidade muçulmana da cidade.',
  'O prédio integra o conjunto de patrimônios religiosos de Uruguaiana, refletindo a diversidade cultural e religiosa formada na região de fronteira.',
  'Patrimônio Religioso',
  -29.75150000, -57.08450000,
  'Século XX',
  'Reflete a presença de diferentes comunidades e tradições religiosas na formação histórica da cidade.',
  'Representa a pluralidade religiosa e cultural de Uruguaiana.'
);
