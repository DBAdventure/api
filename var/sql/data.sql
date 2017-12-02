--
-- PostgreSQL database dump
--
SET statement_timeout = 0;
SET lock_timeout = 0;
SET client_encoding = 'UTF8';
SET standard_conforming_strings = on;
SET check_function_bodies = false;
SET client_min_messages = warning;

SET search_path = public, pg_catalog;



--
-- Data for Name: dragon_ball; Type: TABLE DATA; Schema: public
--

INSERT INTO dragon_ball VALUES (1, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (2, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (3, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (4, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (5, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (6, null, null, null, null, true);
INSERT INTO dragon_ball VALUES (7, null, null, null, null, true);

--
-- Data for Name: event_type; Type: TABLE DATA; Schema: public
--

INSERT INTO event_type VALUES (1, 'player');
INSERT INTO event_type VALUES (2, 'hq');
INSERT INTO event_type VALUES (3, 'bank');


--
-- Data for Name: race; Type: TABLE DATA; Schema: public
--

INSERT INTO race VALUES (1, 'human');
INSERT INTO race VALUES (2, 'human-saiyajin');
INSERT INTO race VALUES (3, 'namekian');
INSERT INTO race VALUES (4, 'saiyajin');
INSERT INTO race VALUES (5, 'alien');
INSERT INTO race VALUES (6, 'cyborg');
INSERT INTO race VALUES (7, 'majin');
INSERT INTO race VALUES (8, 'dragon');
INSERT INTO race VALUES (9, 'predator');
INSERT INTO race VALUES (10, 'ghost');
INSERT INTO race VALUES (11, 'insect');
INSERT INTO race VALUES (12, 'reptilian');
INSERT INTO race VALUES (13, 'demon');
INSERT INTO race VALUES (14, 'hydra');
INSERT INTO race VALUES (15, 'human-soldier');

--
-- Data for Name: object; Type: TABLE DATA; Schema: public
--

INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (1, 'Carte', 200, 'other/map.png', 0, '{}', '{}', 0, true, 'La carte de l''île Permettez-vous de regarder votre position, tous les bâtiments et tous vos amis.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (10, 'Nuage magique n°7', 55, 'other/cloud.png', 1, '{"teleport": "w"}', '{}', 4, true, 'Ce beau nuage vous téléportera à l''Ouest West de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (22, 'Boussole', 80, 'vision/compass.png', 0.3, '{"vision": 2}', '{}', 5, true, 'Une petite boussole pour vous aider à vous orienter.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (21, 'Lunettes du Mâitre des Grues', 130, 'vision/shen.png', 0.3, '{"vision": 4}', '{}', 5, true, 'Les lunettes du maitre des Grues. Elles protègent bien du soleil permettant ainsi de bien voir au loin.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (12, 'Détecteur Saïyen', 140, 'vision/detector.png', 0.4, '{"analysis": 4}', '{}', 5, true, 'Assez sophistiqué, cet appareil permet d''analyser vos cibles.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (47, 'Lunettes de Tortue Géniale', 190, 'vision/roshi.png', 0, '{"vision":"5"}', '{}', 5, true, 'Lunettes similaires à celles du maitre des Grues mais sont moins sombre offrant donc de meilleures capacités visuelles.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (19, 'Lunettes du roi Kaïo', 460, 'vision/glasses.png', 0.6, '{"analysis": 4, "vision": 4}', '{}', 5, true, 'Lunettes du Roi Kaïo, elles sont polyvalentes comparées à celles de la Terre.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (16, 'Antennes du roi Kaïo', 850, 'vision/antenna-kaio.png', 2, '{"analysis": 8}', '{}', 5, true, 'Lourdes, mais incroyablement efficaces, ces antennes ont permis à Maitre Kaïo de repérer l''aura d''ennemis tels que Freezer de l''autre bout de l''univers.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (15, 'Sabre brisé du Ninja Murasaki', 80, 'weapon/broken-murasaki.png', 2, '{"strength": 1, "accuracy": 3}', '{}', 6, true, 'Un sabre cassé apportant un minimum de puissance.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (24, 'Fine Lame de Tao Paï Paï', 130, 'weapon/tao.png', 2, '{"strength": 2, "accuracy": 1}', '{}', 6, true, 'Une lame puissante et précise.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (30, 'Bâton de Tortue Géniale', 230, 'weapon/roshi.png', 0, '{"intellect":"3"}', '{}', 6, true, 'Le bâton du célèbre maitre des arts martiaux Tortue Géniale. Cette arme est puissante, plutôt légère mais est composée de bois uniquement, ce qui la rend donc facilement cassable.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (29, 'Ocarina de Tapion', 390, 'weapon/tapion-ocarina.png', 0, '{"intellect":"5","max_ki":"2"}', '{}', 6, true, 'Un instrument mystérieux qui rendit célèbre le héros Tapion en lui permettant d''enfermer dans son propre corps la partie supérieure du monstre Hildegarde.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (14, 'Sabre de Yajirobé', 400, 'weapon/yajirobe.png', 4, '{"strength": 4, "accuracy": 3}', '{}', 6, true, 'Sabre long et précis. Yajirobé coupa la queue du singe géant Végéta avec cette arme.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (25, 'Épée de Sangohan', 580, 'weapon/sangohan.png', 3.5, '{"strength": 4}', '{}', 6, true, 'L''épée qui accompagna Sangohan durant l''entrainement infernal que ce dernier ait du surmonter. C''est Piccolo, son entraineur devenu son plus grand ami par la suite qui lui fit don de cette arme.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (26, 'Bouclier en bois', 300, 'shield/wood.png', 0, '{"resistance": 2}', '{}', 7, true, 'Un simple bouclier en bois, il protège peu mais il est très léger.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (18, 'Bouclier Viking', 650, 'shield/viking.png', 5, '{"resistance": 4}', '{}', 7, true, 'Magnifique bouclier, résistance moyenne.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (23, 'Bouclier en fer', 3000, 'shield/iron.png', 9, '{"resistance": 20}', '{}', 7, true, 'Ce bouclier est lourd, résistant mais il est maudit.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (17, 'Bouclier de Link', 6500, 'shield/link.png', 10, '{"resistance": 20}', '{}', 7, true, 'Le bouclier de Link offre la meilleure résistance aux attaques physiques. Réduit légèrement les dégâts magiques.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (28, 'Bouclier Miroir Ultime', 6500, 'shield/ultimate-mirror.png', 0, '{"resistance": 40}', '{}', 7, true, 'L''ultime bouclier protège et renvoie les attaques magiques. Son design est magnifique. Il faut une certaine résistance pour pouvoir porter cet immense bouclier.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (31, 'Collier de Bido', 250, 'amulet/bido.png', 0, '{"analysis":"2","intellect":"2","skill":"2","max_ki":"5"}', '{}', 8, true, 'Collier de Bido');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (32, 'Amullette du Roi Vegeta', 400, 'amulet/king-vegeta.png', 0, '{"accuracy":"4","resistance":"3","intellect":"4","max_ki":"5","vision":"1"}', '{}', 8, true, 'L''amulette du légendaire Roi Végéta.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (70, 'Collier de Ryu Sheron', 600, 'amulet/ryu.png', 0, '{"intellect":"7","max_ki":"7"}', '{}', 8, true, 'Le collier du légendaire Ryu Shenron');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (71, 'Amulette de Broly', 850, 'amulet/broly.png', 0, '{"strength":"7","intellect":"7","max_ki":"5","vision":"-15"}', '{"vision":"20"}', 8, true, 'Amulette du légendaire guerrier de l''espace. Améliore toute votre puissance');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (36, 'Chapeau de bébé San Gohan', 100, 'head/baby-gohan.png', 0, '{"analysis":"1","intellect":"1","skill":"1"}', '{}', 9, true, 'Ce chapeau a été le premier qu''ont porté de nombreux magiciens très talentueux. On raconte que la boule à quatre étoiles accrochée sur le dessus renferme de mystérieux pouvoirs.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (37, 'Bandana de Pan', 200, 'head/bandana-pan.png', 0, '{"strength":"1","agility":"1","accuracy":"1","resistance":"1"}', '{}', 9, true, 'Ce bandana a été tissé avec du fil très fin et très souple ce qui permet une bonne liberté de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (92, 'Casquette du général Blue', 200, 'head/blue.png', 0, '{"strength":"2","accuracy":"2"}', '{}', 9, true, 'Elle appartenait au célèbre général de l''armée du Ruban Rouge. Cette casquette est le dernière chose que nous avons retrouvé après qu''il fut tué par Taopaïpaï.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (93, 'Chapeau du maitre des Grues', 270, 'head/tsuru.png', 0, '{"accuracy":"3","resistance":"2","analysis":"1","intellect":"1"}', '{}', 9, true, 'Le chapeau du maitre des Grues est assez polyvalent');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (85, 'Casque de Gyumao', 400, 'head/helmet-gyumao.png', 0, '{"resistance":"5","skill":"4"}', '{}', 9, true, 'Les cornes de ce chapeau appartenaient au plus féroce taureau de l''île. Ce dernier fut vaincu par le père de Chichi. Gyumao  symbolisa cette victoire en mettant ces cornes très résistantes sur le chapeau. Pour se le procurer, il faut être aussi dur comme l''était cet impitoyable Taureau que seul Gyumao réussit à dompter.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (84, 'Bandana de Bojack', 500, 'head/bandana-bojack.png', 0, '{"agility":"4","accuracy":"4","resistance":"3","intellect":"2"}', '{}', 9, true, 'Autrefois, ce bandana appartenait à un très grand voleur mais ce dernier fut tué par un mercenaire redoutable répondant au nom de Bojack.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (94, 'Casque de C-19', 600, 'head/c19.png', 0, '{"resistance":"7"}', '{}', 9, true, 'Casque solide malgré qu''il s''est avéré inutile pour protéger C-19 lorsque ce dernier fut détruit par le Big Bang Attack de Végéta');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (88, 'Turban de Paikûhan', 750, 'head/turban-paikuhan.png', 0, '{"resistance":"4","analysis":"2","skill":"4"}', '{}', 9, true, 'Ce chapeau est une arme à double tranchant : il peut aussi bien vous prendre la vie que de vous la sauver.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (95, 'Casquette de Krilin', 830, 'head/krillin.png', 0, '{"accuracy":"4","analysis":"3","vision":"4"}', '{}', 9, true, 'Cette casquette est très utile pour ne pas être ébloui par l''adversaire.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (34, 'Nuage magique', 100, 'other/cloud.png', 9, '{"teleport": "hq"}', '{}', 0, true, 'Ce nuage peut être utilisé à tout moment, vous permet de vous téléporter à votre QG pour 10 points de mouvement.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (33, 'Radar', 0, 'other/radar.png', 0, '{}', '{}', 2, true, 'Créé par Bulmal, il permet de retrouver les Dragon balls.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (35, 'Lanterne', 150, 'other/lantern.png', 0, '{}', '{}', 2, true, 'Cette lanterne, dont la flamme ne s''étend jamais permet de voir normalement. Votre vision ne diminue pas la nuit.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (20, 'Poire', 10, 'other/pear.png', 0.6, '{"health_percent": 25}', '{}', 4, true, 'Belle poire, restaure 25 pour cent de votre vie.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (13, 'Potion de fatigue', 15, 'other/potionoffatigue.png', 0.1, '{"fatigue": 50}', '{}', 4, true, 'Réduit votre fatigue de 50.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (3, 'Potion de vie', 20, 'other/potionoflife.png', 0.1, '{"health_percent": 50}', '{}', 4, true, 'Restaure 50% de votre maximum de santé.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (2, 'Senzu', 45, 'other/senzu.png', 0.05, '{"health_percent": 100, "fatigue_percent": 100, "ki_percent": 100}', '{}', 4, true, 'Restaure toute ta vie, Ki et élimine la fatigue.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (11, 'Nuage magique n°8', 55, 'other/cloud.png', 1, '{"teleport": "nw"}', '{}', 4, true, 'Ce beau nuage vous téléportera au Nord-Ouest de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (6, 'Nuage magique n°3', 55, 'other/cloud.png', 1, '{"teleport": "e"}', '{}', 4, true, 'Ce beau nuage vous téléportera à l''Est de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (5, 'Nuage magique n°2', 55, 'other/cloud.png', 1, '{"teleport": "ne"}', '{}', 4, true, 'Ce beau nuage vous téléportera au Nord-Est de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (4, 'Nuage magique n°1', 55, 'other/cloud.png', 1, '{"teleport": "n"}', '{}', 4, true, 'Ce beau nuage vous téléportera au nord de l''île pour 10 points de mouvement.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (7, 'Nuage magique n°4', 55, 'other/cloud.png', 1, '{"teleport": "se"}', '{}', 4, true, 'Ce beau nuage vous téléportera au Sud-Est de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (8, 'Nuage magique n°5', 55, 'other/cloud.png', 1, '{"teleport": "s"}', '{}', 4, true, 'Ce beau nuage vous téléportera au Sud de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (9, 'Nuage magique n°6', 55, 'other/cloud.png', 1, '{"teleport": "sw"}', '{}', 4, true, 'Ce beau nuage vous téléportera au Sud-Ouest de l''île pour 10 points de mouvement');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (27, 'Baies sauvages', 5, 'other/berries.png', 0, '{"health":"30"}', '{}', 4, true, 'Petites baies trouvées sur le terrain. Rend 30 points de vie');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (56, 'Petite potion de Ki', 50, 'other/potionki10.png', 0, '{"ki":"10"}', '{}', 4, true, 'Redonne 10 de Ki.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (57, 'Moyenne potion de Ki', 80, 'other/potionki20.png', 0, '{"ki":"20"}', '{}', 4, true, 'Redonne 20 de Ki.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (58, 'Grosse potion de Ki', 175, 'other/potionki50.png', 0, '{"ki":"50"}', '{}', 4, true, 'Redonne 50 de Ki.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (72, 'Détecteur dernière génération', 220, 'vision/detector-last.png', 0, '{"analysis":"7"}', '{}', 5, true, 'Détecteur de technologie supérieure. Il fut utilisé par Freezer et ses hommes dont le célèbre Commando Ginue.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (63, 'Épée Tapion', 650, 'weapon/tapion-sword.png', 0, '{"strength":"7","accuracy":"6"}', '{}', 6, true, 'Un guerrier mystérieux découpa en deux Hildegarde avant d''enfermer les deux parties du monstre à l''intérieur de Tapion et son petit frère.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (60, 'Bâton magique de San Goku', 770, 'weapon/pole.png', 0, '{"strength":"8","accuracy":"5","resistance":"3"}', '{}', 6, true, 'Le célèbre bâton magique de San Goku doté d''une résistance exceptionnelle. Cette arme sera en conséquence tout aussi efficace en attaque qu''en défense.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (61, 'Épée du Gokua', 830, 'weapon/gokua.png', 0, '{"strength":"9","accuracy":"9"}', '{}', 6, true, 'L''épée du mercenaire de l''espace Gokua. Sa longueur accentue sa précision.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (64, 'Bâton du Tout Puissant', 950, 'weapon/god.png', 0, '{"intellect":"9","max_ki":"5"}', '{}', 6, true, 'Arme procurant une grande puissance magique.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (62, 'Épée Z Sword', 6500, 'weapon/z.png', 0, '{}', '{}', 6, true, 'Épée très lourde contenant l''âme d''un Dieu. Malgré son poids, elle s''avère très facile d''accès.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (59, 'Bouclier rouillé', 900, 'shield/rusty.png', 0, '{}', '{}', 7, true, 'Malgré son usure, la résistance qu''il offre est remarquable.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (79, 'Bouclier médical', 1300, 'shield/medical.png', 0, '{}', '{}', 7, true, 'Bouclier particulier de la même famille que le bouclier en bois amélioré mais étant orienté soutien. Il sera très pratique pour les jeunes guerrier blessés devant s''attribuer d''urgence des soins');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (65, 'Bouclier en bois amélioré', 1500, 'shield/wood2.png', 0, '{}', '{}', 7, true, 'Toujours aussi léger, ce bouclier permet une résistance très efficace.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (66, 'Boucles de Gokua', 300, 'amulet/gokua.png', 0, '{"agility":"5"}', '{}', 8, true, 'Les boucles du célèbre mercenaire de l''espace Gokua, réputé pour sa rapidité d''attaque.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (83, 'Casque de Great Saiyamen', 1000, 'head/great.png', 0, '{}', '{}', 9, true, 'Ce casque a été conçut par des scientifiques qui ont récoltés et combinés les matériaux les plus résistants de l''île. Une fois le casque porté, il réduit considérablement les dégâts magiques et physiques.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (96, 'Potalas de Shin', 1000, 'head/potalas.png', 0, '{"agility":"8","accuracy":"8","max_ki":"8"}', '{}', 9, true, 'Les plus nostalgiques se rappelleront leur première apparition sur le monde de Dbadventure il y a bien des années...');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (80, 'Bandeau de Broly', 1000, 'head/broly.png', 0, '{}', '{}', 9, true, 'Ce bandeau a été conçut dans le but de brider la puissance magique mais il permet en contre partie à son porteur de rendre plus élevé sa réserve d''énergie. Le bandeau de Broly est généralement utilisé pour les sorts de soutien.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (89, 'Turban de Paikûhan défensif', 1200, 'head/turban-paikuhan.png', 0, '{}', '{}', 9, true, 'Ce chapeau est assez similaire au turban offensif mais il permet une plus longue durée de vie');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (87, 'Turban de Piccolo', 1500, 'head/piccolo.png', 0, '{"resistance":"2","analysis":"2","vision":"2"}', '{}', 9, true, 'Ce turban est idéal pour bien observer l''adversaire comme le faisait Piccolo du haut du Palais des Dieux. Attention à son poids pouvant handicaper le porteur.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (39, 'Aura Super Saiyen Végéta', 2600, 'head/aura-vegeta.png', 0, '{}', '{}', 9, true, 'Comme pour l''aura San Goku, celle ci est plus destinée aux guerriers. Elle confère un bonus en vitalité non négligeable.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (38, 'Aura Super Saiyen goku', 2600, 'head/aura-goku.png', 0, '{}', '{}', 9, true, 'Grâce aux recherches de nos chercheurs de l''île, nous avons pu reproduire avec quelques mèches de cheveux, la chevelure du célèbre Super Saïyen San Goku !');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (81, 'Potalas des guerriers', 9000, 'head/potalas.png', 0, '{}', '{}', 9, true, 'objects.head.potalas.warrior.description');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (82, 'Potalas des mages', 9000, 'head/potalas.png', 0, '{}', '{}', 9, true, 'Potalas dont le potentiel est immense, de nombreux mages légendaires ont passé des années à s''entrainer avant de pouvoir se les procurer.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (86, 'Turban de Popo', 99999, 'head/turban-popo.png', 0, '{}', '{}', 9, true, 'Il paraitrait que les pouvoirs de M Popo proviendrait de ce somptueux émeraude de son turban.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (40, 'Tennis de Bulma', 50, 'shoes/bulma.png', 0, '{"resistance":"1"}', '{}', 10, true, 'Des paires de chaussures classiques. Elles ne procurent rien d''exceptionnel si ce n''est un minimum de confort');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (41, 'Espadrilles de Piccolo', 120, 'shoes/piccolo.png', 0, '{"agility":"1","resistance":"1"}', '{}', 10, true, 'Bottes souples et résistantes.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (42, 'Bottes de Sangohan', 230, 'shoes/sangohan.png', 0, '{"agility":"3","resistance":"1"}', '{}', 10, true, 'Des petites bottes plus souples que celles de Piccolo.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (43, 'Bottes de C-18', 300, 'shoes/c18.png', 0, '{"agility":"3","resistance":"3"}', '{}', 10, true, 'Les bottes du cyborg C-18. Elles sont résistantes comme nous pouvons le constater lors du combat entre C-18 et Végata, elles ont très bien tenu le choc.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (44, 'Bottes de Trunks', 410, 'shoes/trunks.png', 0, '{"agility":"5","resistance":"2"}', '{}', 10, true, 'Les bottes du fils de Végéta. Elles sont plus souples mais peu résistantes.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (78, 'Bottes des Guerriers de l''Espace', 520, 'shoes/warrior.png', 0, '{"agility":"6","resistance":"3"}', '{}', 10, true, 'Ces bottes furent utilisés par les saïyens. Elles sont extrêmement souples et confortables.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (46, 'Bottes de Végéta', 590, 'shoes/vegeta.png', 0, '{"agility":"7","resistance":"5"}', '{"agility":"25"}', 10, true, 'Les bottes du roi des saïyens Végéta. Elles bénéficient d''améliorations afin de répondre mieux aux attentes de cette illustre personne.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (77, 'Bottes de Sangoku', 670, 'shoes/sangoku.png', 0, '{"agility":"8","resistance":"8"}', '{"agility":"35"}', 10, true, 'Ces bottes sont très lourdes conférant ainsi une résistance plus importante. Elles appartiennent à Sangoku, qui s''en servi lors de son entrainement chez Kaïo.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (76, 'Bottes de Super C-17', 700, 'shoes/superc17.png', 0, '{"agility":"10","resistance":"5"}', '{"agility":"50"}', 10, true, 'Les bottes du plus puissant cyborg de tous les temps, Super C-17. Elles sont composées d''une matière à base de cuir évoluée.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (45, 'Bottes de Broly', 900, 'shoes/broly.png', 0, '{"agility":"10","resistance":"10"}', '{"agility":"60"}', 10, true, 'Les bottes en or du légendaire guerrier de l''espace. Elles sont lourdes et nécessitent d''être très agile pour les maitriser.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (48, 'Tenue de Videl', 50, 'chest/videl.png', 0, '{"resistance":"1"}', '{}', 11, true, 'Des vêtements terriens très simples.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (49, 'Tenue de Chichi', 90, 'chest/chichi.png', 0, '{"resistance":"1","intellect":"1"}', '{}', 11, true, 'Très fine, apporte un léger soutien magique.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (51, 'Tenue de Chaozu', 125, 'chest/chaozu.png', 0, '{"resistance":"2","intellect":"2"}', '{}', 11, true, 'Très légère, la tenue de Chaozu est un peu plus efficace que celle de Chichi');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (73, 'Uniforme Namek', 200, 'chest/namekian.png', 0, '{"resistance":"2","skill":"3"}', '{}', 11, true, 'Uniforme léger et divin. Elle permettra d''améliorer vos soins. Excellent rapport qualité/prix.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (53, 'Armure Saiyen première génération', 350, 'chest/sayajin.png', 0, '{"agility":"2","resistance":"4"}', '{}', 11, true, 'Tenue de combat très souple améliorant agilité et résistance.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (52, 'Tenue de Dendé', 400, 'chest/dende.png', 0, '{"intellect":"5"}', '{}', 11, true, 'La robe magique du Dieu de la terre. Renforce votre puissance magique.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (67, 'Tenue du Maitre des Grues', 550, 'chest/tsuru.png', 0, '{"resistance":"4","intellect":"4"}', '{}', 11, true, 'Tenue souple et résistante.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (90, 'Tenue de Goku Première Génération', 600, 'chest/goku-generation1.png', 0, '{"agility":"6"}', '{}', 11, true, 'La première Tenue de Goku. Elle est plus confortable que la tenue saïyen première génération mais moins résistante');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (68, 'Tenue de C-18', 600, 'chest/c18.png', 0, '{"resistance":"6"}', '{}', 11, true, 'Tenue de C-18. Elle confère essentiellement un bonus de défense.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (91, 'Tenue de Goku Deuxième Génération', 800, 'chest/goku-generation2.png', 0, '{"agility":"7","resistance":"2"}', '{}', 11, true, 'Encore plus performante que son prédécesseur, elle est idéale pour combattre dans de bonnes conditions.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (50, 'Tenue du Tout Puissant', 5000, 'chest/god.png', 0, '{}', '{}', 11, true, 'Tenue de l''ancien Dieu de la Terre. Elle conviendra parfaitement aux mages. Il faut travailler dur pour la mériter.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (69, 'Tenue Roi Kaio', 15000, 'chest/kai.png', 0, '{}', '{}', 11, true, 'La tenue ultime pour tout mage. Appartenant au roi de la galaxie nord, elle fera de vous le plus puissant mage de l''île ; mais il faut prouver votre mérite de la porter...');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (74, 'Tenue Piccolo améliorée', 15000, 'chest/piccolo.png', 0, '{}', '{}', 11, true, 'Amélioration de la tenue Piccolo, elle apporte en plus un bon soutien défensif afin de mieux résister contre l''ennemi et donc de mieux venir en aide à vos compagnons.');
INSERT INTO object (id, name, price, image, weight, bonus, requirements, type, enabled, description) VALUES (75, 'Tenue Piccolo améliorée + cape', 99999, 'chest/piccolo-cape.png', 0, '{"agility":"-2","resistance":"3","intellect":"1","skill":"4"}', '{"agility":"5"}', 11, true, 'Excellente tenue pour un soigneur. Vêtue d''une cape céleste la rendant un peu lourde, elle vous permettra de mieux soigner et bien résister à l''adversaire');



--
-- Data for Name: rank; Type: TABLE DATA; Schema: public
--


INSERT INTO rank VALUES (1, 1, 1, 'Plume');
INSERT INTO rank VALUES (2, 1, 2, 'Oolon');
INSERT INTO rank VALUES (3, 1, 3, 'Bulma');
INSERT INTO rank VALUES (4, 1, 4, 'Nam');
INSERT INTO rank VALUES (5, 1, 5, 'Tortue Géniale');
INSERT INTO rank VALUES (6, 1, 6, 'Chaozu');
INSERT INTO rank VALUES (7, 1, 7, 'Yamcha');
INSERT INTO rank VALUES (8, 1, 8, 'Tenshinhan');
INSERT INTO rank VALUES (9, 1, 9, 'Videl');
INSERT INTO rank VALUES (10, 1, 10, 'Krilin');
INSERT INTO rank VALUES (11, 1, 11, 'Hercule');
INSERT INTO rank VALUES (12, 2, 1, 'Bébé Trunks');
INSERT INTO rank VALUES (13, 2, 2, 'Gohan enfant');
INSERT INTO rank VALUES (14, 2, 3, 'Gohan Super Saïyen');
INSERT INTO rank VALUES (15, 2, 4, 'Trunks du Futur');
INSERT INTO rank VALUES (16, 2, 5, 'Trunks du Futur Super Saïyen');
INSERT INTO rank VALUES (17, 2, 6, 'Gotrunks');
INSERT INTO rank VALUES (18, 2, 7, 'Great Saïyamen');
INSERT INTO rank VALUES (19, 2, 8, 'Gotrunks Super Saïyen');
INSERT INTO rank VALUES (20, 2, 9, 'Gohan enfant Super Saïyen 2');
INSERT INTO rank VALUES (21, 2, 10, 'Gotrunks super saïyen 3');
INSERT INTO rank VALUES (22, 2, 11, 'Gohan adulte (Kaioshin)');
INSERT INTO rank VALUES (23, 3, 1, 'Villageois Namek');
INSERT INTO rank VALUES (24, 3, 2, 'Chef Namek');
INSERT INTO rank VALUES (25, 3, 3, 'Démon Piccolo (vieux)');
INSERT INTO rank VALUES (26, 3, 4, 'Démon Piccolo (jeune)');
INSERT INTO rank VALUES (27, 3, 5, 'Tout puissant');
INSERT INTO rank VALUES (28, 3, 6, 'Piccolo Jr');
INSERT INTO rank VALUES (29, 3, 7, 'Le Père');
INSERT INTO rank VALUES (30, 3, 8, 'Nail');
INSERT INTO rank VALUES (31, 3, 9, 'Piccolo');
INSERT INTO rank VALUES (32, 3, 10, 'Dieuccolo');
INSERT INTO rank VALUES (33, 3, 11, 'Dendé Dieu de la Terre');
INSERT INTO rank VALUES (34, 4, 1, 'Bébé Saïyen');
INSERT INTO rank VALUES (35, 4, 2, 'Goku Adolescent');
INSERT INTO rank VALUES (36, 4, 3, 'Goku Kaioken');
INSERT INTO rank VALUES (37, 4, 4, 'Végéta Gorille Géant');
INSERT INTO rank VALUES (38, 4, 5, 'Goku Super Saïyen');
INSERT INTO rank VALUES (39, 4, 6, 'Goku Super Saïyen 2');
INSERT INTO rank VALUES (40, 4, 7, 'Majin Végéta');
INSERT INTO rank VALUES (41, 4, 8, 'Goku Super Saïyen 3');
INSERT INTO rank VALUES (42, 4, 9, 'Végéto');
INSERT INTO rank VALUES (43, 4, 10, 'Super Végéto');
INSERT INTO rank VALUES (44, 4, 11, 'Broly Super Saïyen Légendaire');
INSERT INTO rank VALUES (45, 5, 1, 'Apol');
INSERT INTO rank VALUES (46, 5, 2, 'Kiwi');
INSERT INTO rank VALUES (47, 5, 3, 'Guldo');
INSERT INTO rank VALUES (48, 5, 4, 'Jeece');
INSERT INTO rank VALUES (49, 5, 5, 'Barta');
INSERT INTO rank VALUES (50, 5, 6, 'Recoom');
INSERT INTO rank VALUES (51, 5, 7, 'Capitaine Ginue');
INSERT INTO rank VALUES (52, 5, 8, 'Cooler');
INSERT INTO rank VALUES (122, 5, 9, 'Freezer 3ème forme');
INSERT INTO rank VALUES (123, 5, 10, 'Métal Cooler');
INSERT INTO rank VALUES (124, 5, 11, 'Freezer Puissance Max');
INSERT INTO rank VALUES (125, 6, 1, 'Cyborg');
INSERT INTO rank VALUES (126, 6, 2, 'Sergent métallique');
INSERT INTO rank VALUES (127, 6, 3, 'C-20');
INSERT INTO rank VALUES (128, 6, 4, 'C-19');
INSERT INTO rank VALUES (129, 6, 5, 'C-13');
INSERT INTO rank VALUES (130, 6, 6, 'C-17');
INSERT INTO rank VALUES (131, 6, 7, 'C-18');
INSERT INTO rank VALUES (132, 6, 8, 'C-16');
INSERT INTO rank VALUES (133, 6, 9, 'Cell');
INSERT INTO rank VALUES (134, 6, 10, 'Cell 2ème forme');
INSERT INTO rank VALUES (135, 6, 11, 'Perfect cell');
INSERT INTO rank VALUES (136, 7, 1, 'Pui Pui');
INSERT INTO rank VALUES (137, 7, 2, 'Yamu');
INSERT INTO rank VALUES (138, 7, 3, 'Sporovitch');
INSERT INTO rank VALUES (139, 7, 4, 'Yakon');
INSERT INTO rank VALUES (140, 7, 5, 'Babidi');
INSERT INTO rank VALUES (141, 7, 6, 'Dabra');
INSERT INTO rank VALUES (142, 7, 7, 'Boubou');
INSERT INTO rank VALUES (143, 7, 8, 'Majin Bou + Piccolo');
INSERT INTO rank VALUES (144, 7, 9, 'Majin Bou + Gotrunks');
INSERT INTO rank VALUES (145, 7, 10, 'Majin Bou + Gohan');
INSERT INTO rank VALUES (146, 7, 11, 'Bou');
INSERT INTO rank VALUES (147, 8, 1, 'Guilan');
INSERT INTO rank VALUES (148, 8, 2, 'Black Shéron');
INSERT INTO rank VALUES (149, 8, 3, 'Rayan Shéron');
INSERT INTO rank VALUES (150, 8, 4, 'Uu Shéron');
INSERT INTO rank VALUES (151, 8, 5, 'Ryu Shéron');
INSERT INTO rank VALUES (152, 8, 6, 'Chii Shéron');
INSERT INTO rank VALUES (153, 8, 7, 'Suu Shéron');
INSERT INTO rank VALUES (154, 8, 8, 'San Shéron');
INSERT INTO rank VALUES (155, 8, 9, 'Li Shéron');
INSERT INTO rank VALUES (156, 8, 10, 'Li Shéron(+7 db)');
INSERT INTO rank VALUES (157, 8, 11, 'Dragon sacré');
INSERT INTO rank VALUES (158, 9, 1, 'predator');
INSERT INTO rank VALUES (159, 10, 1, 'ghost');
INSERT INTO rank VALUES (160, 11, 1, 'insect');
INSERT INTO rank VALUES (161, 12, 1, 'reptilian');
INSERT INTO rank VALUES (162, 13, 1, 'demon');
INSERT INTO rank VALUES (163, 14, 1, 'hydra');
INSERT INTO rank VALUES (164, 15, 1, 'human-soldier');


--
-- Data for Name: side; Type: TABLE DATA; Schema: public
--

INSERT INTO side VALUES (1, 'good');
INSERT INTO side VALUES (2, 'bad');
INSERT INTO side VALUES (3, 'npc');


--
-- Data for Name: building; Type: TABLE DATA; Schema: public
--

INSERT INTO building VALUES (1, 3, 'Temple magique nord ouest', 'magic.png', 49, 31, 13, true);
INSERT INTO building VALUES (2, 3, 'Chez Bebert', 'restaurant.png', 49, 32, 4, true);
INSERT INTO building VALUES (3, 3, 'Magasin d''armes du Nord Ouest', 'weapon.png', 52, 34, 6, true);
INSERT INTO building VALUES (4, 3, 'Laboratoire de vision et d''analyse', 'vision.png', 51, 34, 5, true);
INSERT INTO building VALUES (5, 3, 'Magasin de tenues du Nord Ouest', 'clothing.png', 52, 29, 11, true);
INSERT INTO building VALUES (6, 3, 'Magasin divers du Nord Ouest', 'miscellaneous.png', 54, 32, 2, true);
INSERT INTO building VALUES (7, 3, 'Magasin d''accessoires Sud Est', 'amulet.png', 149, 90, 8, true);
INSERT INTO building VALUES (8, 3, 'Magasin d''accessoires Nord Ouest', 'amulet.png', 23, 65, 8, true);
INSERT INTO building VALUES (9, 3, 'Temple magique Nord Est', 'magic.png', 97, 23, 13, true);
INSERT INTO building VALUES (10, 2, 'Téléporteur Sud', 'teleport.png', 10, 15, 1, true);
INSERT INTO building VALUES (11, 3, 'Magasin divers Nord Est', 'miscellaneous.png', 108, 18, 2, true);
INSERT INTO building VALUES (12, 3, 'Magasin de tenues Nord Est', 'clothing.png', 103, 18, 11, true);
INSERT INTO building VALUES (13, 3, 'Magasin d''armes du Nord Est', 'weapon.png', 113, 18, 6, true);
INSERT INTO building VALUES (14, 3, 'Laboratoire de vision et d''analyse du Nord', 'vision.png', 97, 27, 5, true);
INSERT INTO building VALUES (15, 3, 'Cible à éliminer', 'face.png', 78, 71, 3, true);
INSERT INTO building VALUES (16, 3, 'Magasin de tenues centre ouest', 'clothing.png', 51, 77, 11, true);
INSERT INTO building VALUES (17, 3, 'Magasin divers centre ouest', 'miscellaneous.png', 42, 75, 2, true);
INSERT INTO building VALUES (18, 3, 'Laboratoire de vision et d''analyse du Centre Ouest', 'vision.png', 54, 77, 5, true);
INSERT INTO building VALUES (19, 3, 'Temple magique centre ouest', 'magic.png', 48, 76, 13, true);
INSERT INTO building VALUES (20, 3, 'Magasin d''armes du Centre Ouest', 'weapon.png', 45, 75, 6, true);
INSERT INTO building VALUES (21, 3, 'Chez Bebert', 'restaurant.png', 39, 76, 4, true);
INSERT INTO building VALUES (22, 3, 'Banque Centrale Sud', 'bank.png', 77, 99, 12, true);
INSERT INTO building VALUES (23, 3, 'Chez Doudou', 'restaurant.png', 105, 125, 4, true);
INSERT INTO building VALUES (24, 3, 'Laboratoire de vision et d''analyse du Sud Est', 'vision.png', 113, 117, 5, true);
INSERT INTO building VALUES (25, 3, 'Temple magique Sud Est', 'magic.png', 111, 119, 13, true);
INSERT INTO building VALUES (26, 3, 'Magasin divers du Sud Est', 'miscellaneous.png', 109, 121, 2, true);
INSERT INTO building VALUES (27, 3, 'Magasin d''armes du Sud Est', 'weapon.png', 115, 115, 6, true);
INSERT INTO building VALUES (28, 3, 'Magasin de tenues du Sud Est', 'armor.png', 107, 123, 11, true);
INSERT INTO building VALUES (29, 3, 'Banque Centrale Nord', 'bank.png', 77, 46, 12, true);
INSERT INTO building VALUES (30, 3, 'Chez Robert', 'restaurant.png', 70, 19, 4, true);
INSERT INTO building VALUES (31, 2, 'Téléporteur Ouest', 'teleport.png', 5, 10, 1, true);
INSERT INTO building VALUES (32, 2, 'Téléporteur Est', 'teleport.png', 15, 10, 1, true);
INSERT INTO building VALUES (33, 2, 'Téléporteur Nord', 'teleport.png', 10, 5, 1, true);
INSERT INTO building VALUES (34, 1, 'Téléporteur Nord', 'teleport.png', 10, 5, 1, true);
INSERT INTO building VALUES (35, 1, 'Téléporteur Ouest', 'teleport.png', 5, 10, 1, true);
INSERT INTO building VALUES (36, 1, 'Téléporteur Est', 'teleport.png', 15, 10, 1, true);
INSERT INTO building VALUES (37, 1, 'Téléporteur Sud', 'teleport.png', 10, 15, 1, true);
INSERT INTO building VALUES (38, 1, 'Banque des Enfers', 'bank.png', 12, 10, 12, true);
INSERT INTO building VALUES (39, 1, 'Restaurant des Enfers', 'restaurant.png', 8, 10, 4, true);
INSERT INTO building VALUES (40, 2, 'Bank du Paradis', 'bank.png', 12, 10, 12, true);
INSERT INTO building VALUES (41, 2, 'Restaurant du Paradis', 'restaurant.png', 8, 10, 4, true);
INSERT INTO building VALUES (42, 3, 'Magasin des coiffes Nord Est', 'head.png', 116, 22, 9, true);
INSERT INTO building VALUES (43, 3, 'shop.shoes.southeast', 'shoes.png', 119, 111, 10, true);
INSERT INTO building VALUES (44, 3, 'Magasin des coiffes du Sud Ouest', 'head.png', 117, 113, 9, true);
INSERT INTO building VALUES (45, 3, 'Magasin de bottes central', 'shoes.png', 34, 76, 10, true);
INSERT INTO building VALUES (46, 3, 'Magasin de bottes du Nord Est', 'shoes.png', 54, 31, 10, true);
INSERT INTO building VALUES (47, 3, 'Magasin de bottes du Sud Ouest', 'shoes.png', 99, 20, 10, true);
INSERT INTO building VALUES (48, 3, 'Magasin des coiffes Sud Est', 'head.png', 30, 75, 9, true);
INSERT INTO building VALUES (49, 3, 'Magasin des coiffes Nord Ouest', 'head.png', 51, 29, 9, true);
INSERT INTO building VALUES (50, 3, 'Antre de l''ours', 'cave.png', 69, 19, 13, true);
INSERT INTO building VALUES (51, 4, 'Sortie de l''antre', 'cave.png', 3, 6, 14, true);
INSERT INTO building VALUES (52, 6, 'Temple magique nord ouest', 'magic.png', 5, 4, 13, true);
INSERT INTO building VALUES (53, 6, 'Chez Bebert', 'restaurant.png', 14, 11, 4, true);
INSERT INTO building VALUES (54, 6, 'Magasin d''armes du Nord Ouest', 'weapon.png', 12, 2, 6, true);
INSERT INTO building VALUES (55, 6, 'Laboratoire de vision et d''analyse', 'vision.png', 28, 4, 5, true);
INSERT INTO building VALUES (56, 6, 'Magasin de tenues du Nord Ouest', 'clothing.png', 6, 7, 11, true);
INSERT INTO building VALUES (57, 6, 'Magasin divers du Nord Ouest', 'miscellaneous.png', 6, 8, 2, true);
INSERT INTO building VALUES (58, 6, 'Magasin d''accessoires Sud Est', 'amulet.png', 149, 90, 8, true);
INSERT INTO building VALUES (59, 6, 'Magasin d''accessoires Nord Ouest', 'amulet.png', 20, 9, 8, true);
INSERT INTO building VALUES (60, 6, 'Temple magique Nord Est', 'magic.png', 47, 5, 13, true);
INSERT INTO building VALUES (61, 6, 'Magasin divers Nord Est', 'miscellaneous.png', 40, 11, 2, true);
INSERT INTO building VALUES (62, 6, 'Magasin de tenues Nord Est', 'clothing.png', 41, 11, 11, true);
INSERT INTO building VALUES (63, 6, 'Magasin d''armes du Nord Est', 'weapon.png', 40, 18, 6, true);
INSERT INTO building VALUES (64, 6, 'Cible à éliminer', 'face.png', 27, 25, 3, true);
INSERT INTO building VALUES (65, 6, 'Magasin de tenues centre ouest', 'clothing.png', 13, 28, 11, true);
INSERT INTO building VALUES (66, 6, 'Magasin divers centre ouest', 'miscellaneous.png', 5, 27, 2, true);
INSERT INTO building VALUES (67, 6, 'Magasin des bottes du Nord Ouest', 'vision.png', 11, 24, 5, true);
INSERT INTO building VALUES (68, 6, 'Temple magique centre ouest', 'magic.png', 20, 24, 13, true);
INSERT INTO building VALUES (69, 6, 'Magasin d''armes du Centre Ouest', 'weapon.png', 32, 23, 6, true);
INSERT INTO building VALUES (70, 6, 'Chez Bebert', 'restaurant.png', 32, 24, 4, true);
INSERT INTO building VALUES (71, 6, 'Banque Centrale Sud', 'bank.png', 38, 43, 12, true);
INSERT INTO building VALUES (72, 6, 'Banque Centrale Nord', 'bank.png', 23, 5, 12, true);
INSERT INTO building VALUES (73, 6, 'Chez Doudou', 'restaurant.png', 24, 41, 4, true);
INSERT INTO building VALUES (74, 6, 'Magasin de bottes du Sud Est', 'vision.png', 44, 33, 5, true);
INSERT INTO building VALUES (75, 6, 'Magasin de bottes du Sud Ouest', 'shoes.png', 9, 40, 10, true);
INSERT INTO building VALUES (76, 6, 'Magasin des coiffes du Sud Ouest', 'head.png', 11, 36, 9, true);



--
-- Name: building_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('building_id_seq', 77, true);


--
-- Data for Name: map_object_type; Type: TABLE DATA; Schema: public
--

INSERT INTO map_object_type VALUES (1, 'zeni', 'zeni.png');
INSERT INTO map_object_type VALUES (3, 'chest', 'chest.png');
INSERT INTO map_object_type VALUES (2, 'bush', 'bush.png');
INSERT INTO map_object_type VALUES (4, 'capsule.blue', 'capsule-blue.png');
INSERT INTO map_object_type VALUES (5, 'capsule.red', 'capsule-red.png');
INSERT INTO map_object_type VALUES (6, 'capsule.orange', 'capsule-orange.png');
INSERT INTO map_object_type VALUES (7, 'capsule.black', 'capsule-black.png');
INSERT INTO map_object_type VALUES (8, 'capsule.green', 'capsule-green.png');
INSERT INTO map_object_type VALUES (9, 'sign', 'sign.png');


--
-- Name: player_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('player_id_seq', 1, false);


--
-- Name: race_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('race_id_seq', 16, true);


--
-- Name: rank_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('rank_id_seq', 165, true);


--
-- Name: side_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('side_id_seq', 4, true);


--
-- Name: map_object_type_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('map_object_type_id_seq', 9, true);


--
-- Name: event_type_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('event_type_id_seq', 3, true);

--
-- Name: event_type_id_seq; Type: SEQUENCE SET; Schema: public
--

SELECT pg_catalog.setval('object_id_seq', 97, true);

--
-- PostgreSQL database dump complete
--
