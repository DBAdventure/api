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

INSERT INTO "object" VALUES(1, 'map', 200, 'other/map.png', 0, '{}', '{}',0, true);
INSERT INTO "object" VALUES(2, 'senzu', 45, 'other/senzu.png', 0.05, '{"health_percent": 100, "fatigue_percent": 100, "ki_percent": 100}', '{}', 4, true);
INSERT INTO "object" VALUES(3, 'potion.life', 20, 'other/potionoflife.png', 0.1, '{"health_percent": 50}', '{}', 4, true);
INSERT INTO "object" VALUES(4, 'teleport.cloud.1', 55, 'other/cloud.png', 1, '{"teleport": "n"}', '{}', 4, true);
INSERT INTO "object" VALUES(5, 'teleport.cloud.2', 55, 'other/cloud.png', 1, '{"teleport": "ne"}', '{}', 4, true);
INSERT INTO "object" VALUES(6, 'teleport.cloud.3', 55, 'other/cloud.png', 1, '{"teleport": "e"}', '{}', 4, true);
INSERT INTO "object" VALUES(7, 'teleport.cloud.4', 55, 'other/cloud.png', 1, '{"teleport": "se"}', '{}', 4, true);
INSERT INTO "object" VALUES(8, 'teleport.cloud.5', 55, 'other/cloud.png', 1, '{"teleport": "s"}', '{}', 4, true);
INSERT INTO "object" VALUES(9, 'teleport.cloud.6', 55, 'other/cloud.png', 1, '{"teleport": "sw"}', '{}', 4, true);
INSERT INTO "object" VALUES(10, 'teleport.cloud.7', 55, 'other/cloud.png', 1, '{"teleport": "w"}', '{}', 4, true);
INSERT INTO "object" VALUES(11, 'teleport.cloud.8', 55, 'other/cloud.png', 1, '{"teleport": "nw"}', '{}', 4, true);
INSERT INTO "object" VALUES(12, 'vision.detector.sayajin', 140, 'vision/detector.png', 0.4, '{"analysis": 4}', '{}', 5, true);
INSERT INTO "object" VALUES(13, 'potion.fatigue', 15, 'other/potionoffatigue.png', 0.1, '{"fatigue": 50}', '{}', 4, true);
INSERT INTO "object" VALUES(14, 'weapon.yajirobe', 400, 'weapon/yajirobe.png', 4, '{"strength": 4, "accuracy": 3}', '{}', 6, true);
INSERT INTO "object" VALUES(15, 'weapon.murasaki', 80, 'weapon/broken-murasaki.png', 2, '{"strength": 1, "accuracy": 3}', '{}', 6, true);
INSERT INTO "object" VALUES(16, 'vision.king.kai', 850, 'vision/antenna-kaio.png', 2, '{"analysis": 8}', '{}', 5, true);
INSERT INTO "object" VALUES(17, 'shield.link', 6500, 'shield/link.png', 10, '{"resistance": 20}', '{}', 7, true);
INSERT INTO "object" VALUES(18, 'shield.viking', 650, 'shield/viking.png', 5, '{"resistance": 4}', '{}', 7, true);
INSERT INTO "object" VALUES(19, 'vision.glasses', 460, 'vision/glasses.png', 0.6, '{"analysis": 4, "vision": 4}', '{}', 5, true);
INSERT INTO "object" VALUES(20, 'pear', 10, 'other/pear.png', 0.6, '{"health_percent": 25}', '{}', 4, true);
INSERT INTO "object" VALUES(21, 'vision.shen', 130, 'vision/shen.png', 0.3, '{"vision": 4}', '{}', 5, true);
INSERT INTO "object" VALUES(22, 'vision.compass', 80, 'vision/compass.png', 0.3, '{"vision": 2}', '{}', 5, true);
INSERT INTO "object" VALUES(23, 'shield.iron', 3000, 'shield/iron.png', 9, '{"resistance": 20}', '{}', 7, true);
INSERT INTO "object" VALUES(24, 'weapon.tao', 130, 'weapon/tao.png', 2, '{"strength": 2, "accuracy": 1}', '{}', 6, true);
INSERT INTO "object" VALUES(25, 'weapon.sangohan', 580, 'weapon/sangohan.png', 3.5, '{"strength": 4}', '{}', 6, true);
INSERT INTO "object" VALUES(26, 'shield.wood', 300, 'shield/wood.png', 0, '{"resistance": 2}', '{}', 7, true);
INSERT INTO "object" VALUES(27, 'berries', 5, 'other/berries.png', 0, '{"health": 30}', '{}', 4, true);
INSERT INTO "object" VALUES(28, 'shield.mirror', 6500, 'shield/ultimate-mirror.png', 0, '{"resistance": 40}', '{}', 7, true);
INSERT INTO "object" VALUES(29, 'weapon.ocarina', 390, 'weapon/tapion-ocarina.png', 0, '{"intellect":"5","max_ki":"2"}', '{}', 6, true);
INSERT INTO "object" VALUES(30, 'weapon.roshi', 230, 'weapon/roshi.png', 0, '{"intellect":"3"}', '{}', 6, true);
INSERT INTO "object" VALUES(31, 'amulet.bido', 250, 'amulet/bido.png', 0, '{"analysis":"2","intellect":"2","skill":"2","max_ki":"5"}', '{}', 8, true);
INSERT INTO "object" VALUES(32, 'amulet.king.vegeta', 400, 'amulet/king-vegeta.png', 0, '{"accuracy":"4","resistance":"3","intellect":"4","max_ki":"5","vision":"1"}', '{}', 8, true);
INSERT INTO "object" VALUES(33, 'radar', 0, 'other/radar.png', 0, '{}', '{}', 2, true);
INSERT INTO "object" VALUES(34, 'teleport.hq', 100, 'other/cloud.png', 9, '{"teleport": "hq"}', '{}', 0, true);
INSERT INTO "object" VALUES(35, 'lantern', 150, 'other/lantern.png', 0, '{}', '{}', 2, true);
INSERT INTO "object" VALUES(36, 'head.gohan.baby', 100, 'head/baby-gohan.png', 0, '{"analysis":"1","intellect":"1","skill":"1"}', '{}', 9, true);
INSERT INTO "object" VALUES(37, 'head.bandana.pan', 200, 'head/bandana-pan.png', 0, '{"strength":"1","agility":"1","accuracy":"1","resistance":"1"}', '{}', 9, true);
INSERT INTO "object" VALUES(38, 'head.aura.goku', 2600, 'head/aura-goku.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(39, 'head.aura.vegeta', 2600, 'head/aura-vegeta.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(40, 'shoes.bulma', 50, 'shoes/bulma.png', 0, '{"resistance":"1"}', '{}', 10, true);
INSERT INTO "object" VALUES(41, 'shoes.piccolo', 120, 'shoes/piccolo.png', 0, '{"agility":"1","resistance":"1"}', '{}', 10, true);
INSERT INTO "object" VALUES(42, 'shoes.gohan', 230, 'shoes/sangohan.png', 0, '{"agility":"3","resistance":"1"}', '{}', 10, true);
INSERT INTO "object" VALUES(43, 'shoes.c18', 300, 'shoes/c18.png', 0, '{"agility":"3","resistance":"3"}', '{}', 10, true);
INSERT INTO "object" VALUES(44, 'shoes.trunks', 410, 'shoes/trunks.png', 0, '{"agility":"5","resistance":"2"}', '{}', 10, true);
INSERT INTO "object" VALUES(45, 'shoes.broly', 900, 'shoes/broly.png', 0, '{"agility":"10","resistance":"10"}', '{"agility":"60"}', 10, true);
INSERT INTO "object" VALUES(46, 'shoes.vegeta', 590, 'shoes/vegeta.png', 0, '{"agility":"7","resistance":"5"}', '{"agility":"25"}', 10, true);
INSERT INTO "object" VALUES(47, 'vision.roshi', 190, 'vision/roshi.png', 0, '{"vision":"5"}', '{}', 5, true);
INSERT INTO "object" VALUES(48, 'chest.videl', 50, 'chest/videl.png', 0, '{"resistance":"1"}', '{}', 11, true);
INSERT INTO "object" VALUES(49, 'chest.chichi', 90, 'chest/chichi.png', 0, '{"resistance":"1","intellect":"1"}', '{}', 11, true);
INSERT INTO "object" VALUES(50, 'chest.god', 5000, 'chest/god.png', 0, '{}', '{}', 11, true);
INSERT INTO "object" VALUES(51, 'chest.chaozu', 125, 'chest/chaozu.png', 0, '{"resistance":"2","intellect":"2"}', '{}', 11, true);
INSERT INTO "object" VALUES(52, 'chest.dende', 400, 'chest/dende.png', 0, '{"intellect":"5"}', '{}', 11, true);
INSERT INTO "object" VALUES(53, 'chest.sayajin', 350, 'chest/sayajin.png', 0, '{"agility":"2","resistance":"4"}', '{}', 11, true);
INSERT INTO "object" VALUES(56, 'potion.ki.little', 50, 'other/potionki10.png', 0, '{"ki":"10"}', '{}', 4, true);
INSERT INTO "object" VALUES(57, 'potion.ki.medium', 80, 'other/potionki20.png', 0, '{"ki":"20"}', '{}', 4, true);
INSERT INTO "object" VALUES(58, 'potion.ki.big', 175, 'other/potionki50.png', 0, '{"ki":"50"}', '{}', 4, true);
INSERT INTO "object" VALUES(59, 'shield.rusty', 900, 'shield/rusty.png', 0, '{}', '{}', 7, true);
INSERT INTO "object" VALUES(60, 'weapon.power.pole', 770, 'weapon/pole.png', 0, '{"strength":"8","accuracy":"5","resistance":"3"}', '{}', 6, true);
INSERT INTO "object" VALUES(61, 'weapon.gokua', 830, 'weapon/gokua.png', 0, '{"strength":"9","accuracy":"9"}', '{}', 6, true);
INSERT INTO "object" VALUES(62, 'weapon.z', 6500, 'weapon/z.png', 0, '{}', '{}', 6, true);
INSERT INTO "object" VALUES(63, 'weapon.tapion', 650, 'weapon/tapion-sword.png', 0, '{"strength":"7","accuracy":"6"}', '{}', 6, true);
INSERT INTO "object" VALUES(64, 'weapon.god', 950, 'weapon/god.png', 0, '{"intellect":"9","max_ki":"5"}', '{}', 6, true);
INSERT INTO "object" VALUES(65, 'shield.wood2', 1500, 'shield/wood2.png', 0, '{}', '{}', 7, true);
INSERT INTO "object" VALUES(66, 'amulet.gokua', 300, 'amulet/gokua.png', 0, '{"agility":"5"}', '{}', 8, true);
INSERT INTO "object" VALUES(67, 'chest.tsuru', 550, 'chest/tsuru.png', 0, '{"resistance":"4","intellect":"4"}', '{}', 11, true);
INSERT INTO "object" VALUES(68, 'chest.c18', 600, 'chest/c18.png', 0, '{"resistance":"6"}', '{}', 11, true);
INSERT INTO "object" VALUES(69, 'chest.kai', 15000, 'chest/kai.png', 0, '{}', '{}', 11, true);
INSERT INTO "object" VALUES(70, 'amulet.ryu', 600, 'amulet/ryu.png', 0, '{"intellect":"7","max_ki":"7"}', '{}', 8, true);
INSERT INTO "object" VALUES(71, 'amulet.broly', 850, 'amulet/broly.png', 0, '{"strength":"7","intellect":"7","max_ki":"5","vision":"-15"}', '{"vision":"20"}', 8, true);
INSERT INTO "object" VALUES(72, 'vision.detector.last', 220, 'vision/detector-last.png', 0, '{"analysis":"7"}', '{}', 5, true);
INSERT INTO "object" VALUES(73, 'chest.namekian', 200, 'chest/namekian.png', 0, '{"resistance":"2","skill":"3"}', '{}', 11, true);
INSERT INTO "object" VALUES(74, 'chest.piccolo', 15000, 'chest/piccolo.png', 0, '{}', '{}', 11, true);
INSERT INTO "object" VALUES(75, 'chest.piccolo.cape', 99999, 'chest/piccolo-cape.png', 0, '{"agility":"-2","resistance":"3","intellect":"1","skill":"4"}', '{"agility":"5"}', 11, true);
INSERT INTO "object" VALUES(76, 'shoes.superc17', 700, 'shoes/superc17.png', 0, '{"agility":"10","resistance":"5"}', '{"agility":"50"}', 10, true);
INSERT INTO "object" VALUES(77, 'shoes.goku', 670, 'shoes/sangoku.png', 0, '{"agility":"8","resistance":"8"}', '{"agility":"35"}', 10, true);
INSERT INTO "object" VALUES(78, 'shoes.warrior', 520, 'shoes/warrior.png', 0, '{"agility":"6","resistance":"3"}', '{}', 10, true);
INSERT INTO "object" VALUES(79, 'shield.medical', 1300, 'shield/medical.png', 0, '{}', '{}', 7, true);
INSERT INTO "object" VALUES(80, 'head.broly', 1000, 'head/broly.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(81, 'head.potalas.warrior', 9000, 'head/potalas.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(82, 'head.potalas.magus', 9000, 'head/potalas.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(83, 'head.helmet.great', 1000, 'head/great.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(84, 'head.bandana.bojack', 500, 'head/bandana-bojack.png', 0, '{"agility":"4","accuracy":"4","resistance":"3","intellect":"2"}', '{}', 9, true);
INSERT INTO "object" VALUES(85, 'head.helmet.gyumao', 400, 'head/helmet-gyumao.png', 0, '{"resistance":"5","skill":"4"}', '{}', 9, true);
INSERT INTO "object" VALUES(86, 'head.turban.popo', 99999, 'head/turban-popo.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(87, 'head.turban.piccolo', 1500, 'head/piccolo.png', 0, '{"resistance":"2","analysis":"2","vision":"2"}', '{}', 9, true);
INSERT INTO "object" VALUES(88, 'head.turban.paikuhan', 750, 'head/turban-paikuhan.png', 0, '{"resistance":"4","analysis":"2","skill":"4"}', '{}', 9, true);
INSERT INTO "object" VALUES(89, 'head.turban.paikuhan.defense', 1200, 'head/turban-paikuhan.png', 0, '{}', '{}', 9, true);
INSERT INTO "object" VALUES(90, 'chest.goku.generation.1', 600, 'chest/goku-generation1.png', 0, '{"agility":"6"}', '{}', 11, true);
INSERT INTO "object" VALUES(91, 'chest.goku.generation.2', 800, 'chest/goku-generation2.png', 0, '{"agility":"7","resistance":"2"}', '{}', 11, true);
INSERT INTO "object" VALUES(92, 'head.blue', 200, 'head/blue.png', 0, '{"strength":"2","accuracy":"2"}', '{}', 9, true);
INSERT INTO "object" VALUES(93, 'head.tsuru', 270, 'head/tsuru.png',  0, '{"accuracy":"3","resistance":"2","analysis":"1","intellect":"1"}', '{}', 9, true);
INSERT INTO "object" VALUES(94, 'head.helmet.c19', 600, 'head/c19.png', 0, '{"resistance":"7"}', '{}', 9, true);
INSERT INTO "object" VALUES(95, 'head.krillin', 830, 'head/krillin.png', 0, '{"accuracy":"4","analysis":"3","vision":"4"}', '{}', 9, true);
INSERT INTO "object" VALUES(96, 'head.potalas.shin', 1000, 'head/potalas.png', 0, '{"agility":"8","accuracy":"8","max_ki":"8"}', '{}', 9, true);

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

INSERT INTO building VALUES (1, 3, 'magic.temple.northwest', 'buildings/magic.png', 49, 31, 13, true);
INSERT INTO building VALUES (2, 3, 'restaurant.bebert', 'buildings/restaurant.png', 49, 32, 4, true);
INSERT INTO building VALUES (3, 3, 'shop.weapon.northwest', 'buildings/weapon.png', 52, 34, 6, true);
INSERT INTO building VALUES (4, 3, 'shop.vision.laboratory', 'buildings/vision.png', 51, 34, 5, true);
INSERT INTO building VALUES (5, 3, 'shop.cloth.northwest', 'buildings/clothing.png', 52, 29, 11, true);
INSERT INTO building VALUES (6, 3, 'shop.miscellaneous.northwest', 'buildings/miscellaneous.png', 54, 32, 2, true);
INSERT INTO building VALUES (7, 3, 'shop.amulet.southeast', 'buildings/amulet.png', 149, 90, 8, true);
INSERT INTO building VALUES (8, 3, 'shop.amulet.northwest', 'buildings/amulet.png', 23, 65, 8, true);
INSERT INTO building VALUES (9, 3, 'magic.temple.northeast', 'buildings/magic.png', 97, 23, 13, true);
INSERT INTO building VALUES (10, 2, 'teleport.heaven.south', 'buildings/teleport.png', 10, 15, 1, true);
INSERT INTO building VALUES (11, 3, 'shop.miscellaneous.northeast', 'buildings/miscellaneous.png', 108, 18, 2, true);
INSERT INTO building VALUES (12, 3, 'shop.cloth.northeast', 'buildings/clothing.png', 103, 18, 11, true);
INSERT INTO building VALUES (13, 3, 'shop.weapon.northeast', 'buildings/weapon.png', 113, 18, 6, true);
INSERT INTO building VALUES (14, 3, 'shop.vision.north', 'buildings/vision.png', 97, 27, 5, true);
INSERT INTO building VALUES (15, 3, 'wanted.face', 'buildings/face.png', 78, 71, 3, true);
INSERT INTO building VALUES (16, 3, 'shop.cloth.centerwest', 'buildings/clothing.png', 51, 77, 11, true);
INSERT INTO building VALUES (17, 3, 'shop.miscellaneous.centerwest', 'buildings/miscellaneous.png', 42, 75, 2, true);
INSERT INTO building VALUES (18, 3, 'shop.vision.centerwest', 'buildings/vision.png', 54, 77, 5, true);
INSERT INTO building VALUES (19, 3, 'magic.temple.centerwest', 'buildings/magic.png', 48, 76, 13, true);
INSERT INTO building VALUES (20, 3, 'shop.weapon.centerwest', 'buildings/weapon.png', 45, 75, 6, true);
INSERT INTO building VALUES (21, 3, 'restaurant.roger', 'buildings/restaurant.png', 39, 76, 4, true);
INSERT INTO building VALUES (22, 3, 'bank.central.south', 'buildings/bank.png', 77, 99, 12, true);
INSERT INTO building VALUES (23, 3, 'restaurant.doudou', 'buildings/restaurant.png', 105, 125, 4, true);
INSERT INTO building VALUES (24, 3, 'shop.vision.southeast', 'buildings/vision.png', 113, 117, 5, true);
INSERT INTO building VALUES (25, 3, 'magic.temple.southeast', 'buildings/magic.png', 111, 119, 13, true);
INSERT INTO building VALUES (26, 3, 'shop.miscellaneous.southeast', 'buildings/miscellaneous.png', 109, 121, 2, true);
INSERT INTO building VALUES (27, 3, 'shop.weapon.southeast', 'buildings/weapon.png', 115, 115, 6, true);
INSERT INTO building VALUES (28, 3, 'shop.cloth.southeast', 'buildings/armor.png', 107, 123, 11, true);
INSERT INTO building VALUES (29, 3, 'bank.central.north', 'buildings/bank.png', 77, 46, 12, true);
INSERT INTO building VALUES (30, 3, 'restaurant.robert', 'buildings/restaurant.png', 70, 19, 4, true);
INSERT INTO building VALUES (31, 2, 'teleport.heaven.west', 'buildings/teleport.png', 5, 10, 1, true);
INSERT INTO building VALUES (32, 2, 'teleport.heaven.east', 'buildings/teleport.png', 15, 10, 1, true);
INSERT INTO building VALUES (33, 2, 'teleport.heaven.north', 'buildings/teleport.png', 10, 5, 1, true);
INSERT INTO building VALUES (34, 1, 'teleport.hell.north', 'buildings/teleport.png', 10, 5, 1, true);
INSERT INTO building VALUES (35, 1, 'teleport.hell.west', 'buildings/teleport.png', 5, 10, 1, true);
INSERT INTO building VALUES (36, 1, 'teleport.hell.east', 'buildings/teleport.png', 15, 10, 1, true);
INSERT INTO building VALUES (37, 1, 'teleport.hell.south', 'buildings/teleport.png', 10, 15, 1, true);
INSERT INTO building VALUES (38, 1, 'bank.hell', 'buildings/bank.png', 12, 10, 12, true);
INSERT INTO building VALUES (39, 1, 'restaurant.hell', 'buildings/restaurant.png', 8, 10, 4, true);
INSERT INTO building VALUES (40, 2, 'bank.heaven', 'buildings/bank.png', 12, 10, 12, true);
INSERT INTO building VALUES (41, 2, 'restaurant.heaven', 'buildings/restaurant.png', 8, 10, 4, true);
INSERT INTO building VALUES (42, 3, 'shop.head.northeast', 'buildings/head.png', 116, 22, 9, true);
INSERT INTO building VALUES (43, 3, 'shop.shoes.southeast', 'buildings/shoes.png', 119, 111, 10, true);
INSERT INTO building VALUES (44, 3, 'shop.head.southeast', 'buildings/head.png', 117, 113, 9, true);
INSERT INTO building VALUES (45, 3, 'shop.shoes.center', 'buildings/shoes.png', 34, 76, 10, true);
INSERT INTO building VALUES (46, 3, 'shop.shoes.northwest', 'buildings/shoes.png', 54, 31, 10, true);
INSERT INTO building VALUES (47, 3, 'shop.shoes.southwest', 'buildings/shoes.png', 99, 20, 10, true);
INSERT INTO building VALUES (48, 3, 'shop.head.southwest', 'buildings/head.png', 30, 75, 9, true);
INSERT INTO building VALUES (49, 3, 'shop.head.northwest', 'buildings/head.png', 51, 29, 9, true);
INSERT INTO building VALUES (50, 3, 'bear.enter', 'buildings/cave.png', 69, 19, 13, true);
INSERT INTO building VALUES (51, 4, 'bear.exit', 'buildings/cave.png', 3, 6, 14, true);
INSERT INTO building VALUES (52, 6, 'magic.temple.northwest', 'buildings/magic.png', 5, 4, 13, true);
INSERT INTO building VALUES (53, 6, 'restaurant.bebert', 'buildings/restaurant.png', 14, 11, 4, true);
INSERT INTO building VALUES (54, 6, 'shop.weapon.northwest', 'buildings/weapon.png', 12, 2, 6, true);
INSERT INTO building VALUES (55, 6, 'shop.vision.laboratory', 'buildings/vision.png', 28, 4, 5, true);
INSERT INTO building VALUES (56, 6, 'shop.cloth.northwest', 'buildings/clothing.png', 6, 7, 11, true);
INSERT INTO building VALUES (57, 6, 'shop.miscellaneous.northwest', 'buildings/miscellaneous.png', 6, 8, 2, true);
INSERT INTO building VALUES (58, 6, 'shop.amulet.southeast', 'buildings/amulet.png', 149, 90, 8, true);
INSERT INTO building VALUES (59, 6, 'shop.amulet.northwest', 'buildings/amulet.png', 20, 9, 8, true);
INSERT INTO building VALUES (60, 6, 'magic.temple.northeast', 'buildings/magic.png', 47, 5, 13, true);
INSERT INTO building VALUES (61, 6, 'shop.miscellaneous.northeast', 'buildings/miscellaneous.png', 40, 11, 2, true);
INSERT INTO building VALUES (62, 6, 'shop.cloth.northeast', 'buildings/clothing.png', 41, 11, true);
INSERT INTO building VALUES (63, 6, 'shop.weapon.northeast', 'buildings/weapon.png', 40, 18, 6, true);
INSERT INTO building VALUES (64, 6, 'wanted.face', 'buildings/face.png', 27, 25, 3, true);
INSERT INTO building VALUES (65, 6, 'shop.cloth.centerwest', 'buildings/clothing.png', 13, 28, 11, true);
INSERT INTO building VALUES (66, 6, 'shop.miscellaneous.centerwest', 'buildings/miscellaneous.png', 5, 27, 2, true);
INSERT INTO building VALUES (67, 6, 'shop.vision.centerwest', 'buildings/vision.png', 11, 24, 5, true);
INSERT INTO building VALUES (68, 6, 'magic.temple.centerwest', 'buildings/magic.png', 20, 24, 13, true);
INSERT INTO building VALUES (69, 6, 'shop.weapon.centerwest', 'buildings/weapon.png', 32, 23, 6, true);
INSERT INTO building VALUES (70, 6, 'restaurant.roger', 'buildings/restaurant.png', 32, 24, 4, true);
INSERT INTO building VALUES (71, 6, 'bank.central.south', 'buildings/bank.png', 38, 43, 12, true);
INSERT INTO building VALUES (72, 6, 'bank.central.north', 'buildings/bank.png', 23, 5, 12, true);
INSERT INTO building VALUES (73, 6, 'restaurant.doudou', 'buildings/restaurant.png', 24, 41, 4, true);
INSERT INTO building VALUES (74, 6, 'shop.vision.southeast', 'buildings/vision.png', 44, 33, 5, true);
INSERT INTO building VALUES (75, 6, 'shop.shoes.southwest', 'buildings/shoes.png', 9, 40, 10, true);
INSERT INTO building VALUES (76, 6, 'shop.head.southwest', 'buildings/head.png', 11, 36, 9, true);



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
