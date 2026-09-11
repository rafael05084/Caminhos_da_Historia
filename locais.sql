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


/* Livro: Uruguaiana na linguagem plástica e histórica (Carlos Fonttes e Daniel Fanti) */
(
  'Ponte Internacional',
  'Ponte rodoferroviária que liga Uruguaiana (Brasil) a Paso de los Libres (Argentina) sobre o Rio Uruguai.',
  'ANTONIO MARY ULRICH, Cônsul do Brasil em Paso de Los Libres, na Argentina, juntamente com EUSTÁQUIO ORMAZABAL, lideram o movimento para a construção da primeira ponte internacional, entre o Brasil e a Argentina, Urich,quando discursava durante a aprovação do projeto,teve morte instantânea,não chegando a ver seu sonho realizado. Em 28/12/1942, iniciam-se as obras de construção da ponte, tendo sido lançada sua pedra fundamental pelos Presidentes Getúlio Vargas do Brasil e Augustín Justo da Argentina em 1938. Foi entregue ao trânsito de veículos em outubro de 1945. Sua inauguração foi efetuada em 21/05/1947, com a presença dos dois mandatários do Brasil e Argentina, Juan Domingos Perón e Eurico Gaspar Dutra, passando a denominar-se " PONTE INTERNACIONAL AUGUSTIN JUSTO E GETULIO VARGAS". Em 27 de Setembro de 1995, o Governo Municipal, considerou oficialmente como símbolo de Uruguaiana',
  'Patrimônio Histórico',
  -29.74310000, -57.09310000,
  'Século XX (1942-1947)',
  'Com 1.419 metros de extensão, tornou Uruguaiana um dos maiores portos secos do país.',
  'Símbolo da integração entre Brasil e Argentina e um dos principais marcos históricos e econômicos da cidade.'
),


/* Livro: Uruguaiana na linguagem plástica e histórica (Carlos Fonttes e Daniel Fanti) */
(
  'Praça do Barão',
  'Antiga  "Praça da Matriz" ou "Praça da Rendição"',
  'A atual Praça Barão do Rio Branco era originalmente apenas um descampado conhecido como "Praça da Matriz" com a fundação da cidade. Em 12 de abril de 1870, a Câmara Municipal a renomeou para "Praça da Rendição" em homenagem à rendição das forças comandadas pelo Tenente-Coronel Antonio de La Cruz Estigarribia em 18 de setembro de 1865, durante a Guerra do Paraguai. Mais tarde, em 7 de setembro de 1942, às vésperas do centenário do município (1943), o Interventor Municipal José Maria Piquet alterou o nome para "Praça Barão do Rio Branco" para não ferir a suscetibilidade de autoridades paraguaias presentes. O local abriga a estátua do Barão do Rio Branco, erguida em 13 de maio de 1914, e foi oficialmente tombado pelo Patrimônio Histórico do Município pela Lei nº 1.877, em 17 de junho de 1987. ',
  'Patrimônio Histórico',
  -29.75550000, -57.08780000,
  'Século XIX',
  'A Praça Barão do Rio Branco possui um valor inestimável para a história de Uruguaiana por ser o marco zero de seu planejamento urbano e o cenário de um dos acontecimentos mais marcantes do Brasil Imperial. Surgida a partir da própria fundação do município como o espaço central em frente à Igreja Matriz, a praça foi o palco exato em que ocorreu a rendição das tropas paraguaias comandadas pelo Tenente-Coronel Antonio de La Cruz Estigarribia em 1865, episódio decisivo na Guerra do Paraguai que contou com a presença do imperador Dom Pedro II. O local consolidou-se ao longo do tempo como o coração cultural e de memória da cidade, abrigando a estátua do diplomata Barão do Rio Branco e sendo oficialmente reconhecido como Patrimônio Histórico do Município.',
  'A praça foi demarcada originalmente em 1843 pelo engenheiro e agrimensor alemão Schuster. Anos mais tarde, em 1871, o Conselho Municipal tomou a iniciativa oficial de ordenar o plantio de diversas mudas de árvores no local para que a população de Uruguaiana pudesse ter um espaço arborizado com sombra e refúgio nos dias de calor intenso.'
),


(
  'CTG Sinuelo do Pago',
  'Centro de Tradições Gaúchas localizado no bairro Santo Inácio, dedicado à preservação da cultura gaúcha.',
  'Um dos CTGs mais tradicionais de Uruguaiana, promove rodeios, festivais e atividades ligadas ao movimento tradicionalista gaúcho ao longo do ano.',
  'Patrimônio Histórico',
  -29.74600000, -57.02900000,
  'Século XX',
  'Recebe eventos tradicionalistas de grande público, como rodeios e festivais artísticos.',
  'Um dos principais espaços de preservação e difusão da cultura gaúcha na região da fronteira oeste.'
),

/* Livro: Uruguaiana - Atalaia da pátria. (Urbano Lago Villela) */
(
  'Rua Dr. Maia',
  'Antiga Rua 28',
  'A atual Rua Dr. Maia é uma das vias estruturais e mais valorizadas do Centro de Uruguaiana, tendo deixado completamente para trás o antigo estigma de "zona de meretrício" da velha Rua 28. Hoje, ela combina residências de alto padrão, comércio local e forte apelo histórico e cultural.',
  'Patrimônio Histórico',
  -29.75980000, -57.08800000,
  'Século XIX',
  'O nome remete ao médico Vicente José da Maia de grande renome nesta zona, havendo por longos anos exercido a presidência do Clube Comercial de Uruguaiana. Nasceu em 24 de Abril de 1901 e faleceu nesta cidade em 22 de agosto de 1948.',
  'Registro da malha urbana original de Uruguaiana e de sua ocupação histórica.'
),

/* Livro: Sociedade Beneficiente União filhos do trabalho - História do primeiro clube social negro de Uruguaiana. (Augusto Juvenal Correa Fidelis) */
(
  'Sociedade Beneficente União Filhos do Trabalho (SBU)',
  'Primeiro clube social negro de Uruguaiana.',
  'Fundada em 1925, a Sociedade Beneficente União Filhos do Trabalho foi o primeiro clube social negro de Uruguaiana, criado em resposta à exclusão do pós-Abolição. Operando com base no mutualismo, garantia suporte comunitário e auxílio financeiro aos associados, além de ser o berço do carnaval local. Embora sua sede física tenha sido demolida, permanece como um valioso patrimônio imaterial da cidade.',
  'Patrimônio Histórico',
  -29.75800000, -57.09550000,
  'Século XX',
  'Prazos e Normas no "Livro Preto": Exigia rigoroso cumprimento das mensalidades para garantir os benefícios de auxílio e funeral.\n\nResistência e Bipartição: Ocupou o papel de centro cultural da cidade diante da segregação nos clubes de elite.\n\nSignificado do Nome: Expressava o orgulho operário e a união pela dignidade do trabalho pós-escravidão.\n\nPatrimônio Sem Parede: Preservado na memória histórica e na literatura acadêmica mesmo após a demolição do prédio.\n\nSemente do Carnaval: Os blocos organizados no clube serviram de base para as primeiras escolas de samba de Uruguaiana.',
  'Pioneirismo na Fronteira: Primeiro espaço de gestão e autonomia da comunidade negra local.\n\nRede Mutualista: Atuou como seguridade social comunitária diante da ausência de apoio do Estado.\n\nMatriz Cultural: Base formadora da identidade cultural e da tradição carnavalesca de Uruguaiana.'
);