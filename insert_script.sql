INSERT INTO `condition_type` (`id`, `accessor`, `label`, `created`) VALUES
(1, 1, 'Podmínka výsledku', '2019-04-27 11:42:36'),
(2, 2, 'Podmínka diskriminantu', '2019-04-27 11:42:58');

INSERT INTO `condition` (`id`, `condition_type_id`, `accessor`, `label`, `created`) VALUES
(1, 1, 0, 'Bez omezení', '2019-04-27 11:49:59'),
(2, 1, 1, 'Kladný', '2019-04-27 11:49:59'),
(3, 1, 2, 'Nulový', '2019-04-27 11:49:59'),
(4, 1, 3, 'Záporný', '2019-04-27 11:49:59'),
(5, 2, 0, 'Bez omezení', '2019-04-27 11:49:59'),
(6, 2, 1, 'Kladný', '2019-04-27 11:49:59'),
(7, 2, 2, 'Nulový', '2019-04-27 11:49:59'),
(8, 2, 3, 'Záporný', '2019-04-27 11:49:59'),
(9, 2, 4, 'Celočíselný', '2019-04-27 11:49:59'),
(10, 2, 5, 'Kladný a odmocnitelný', '2019-04-27 11:49:59');

INSERT INTO `difficulty` (`id`, `label`, `created`) VALUES
(1, 'Lehká', '2019-02-17 10:29:19'),
(2, 'Střední', '2019-02-17 10:29:19'),
(3, 'Těžká', '2019-02-17 10:29:19');

INSERT INTO `category` (`id`, `label`, `created`) VALUES
(1, '1. Rovnice', '2019-04-27 14:20:50'),
(2, '2. Posloupnosti', '2019-04-27 14:21:09'),
(3, '3. Logika', '2019-04-27 14:21:18');

INSERT INTO `sub_category` (`id`, `category_id`, `label`, `created`) VALUES
(1, 1, '1.1. Lineární rovnice', '2019-04-27 14:21:31'),
(2, 1, '1.2. Kvadratické rovnice', '2019-04-27 14:21:42'),
(3, 2, '2.1. Aritmetické posloupnosti', '2019-04-27 14:21:54'),
(4, 2, '2.2. Geometrické posloupnosti', '2019-04-27 14:22:07'),
(5, 3, '3.1. Výroková logika', '2019-04-27 14:22:17');

INSERT INTO `problem_type` (`id`, `label`, `created`, `accessor`, `is_generatable`) VALUES
(1, 'Lineární rovnice', '2019-04-05 19:19:49', 1, 1),
(2, 'Kvadratická rovnice', '2019-04-05 19:19:49', 2, 1),
(3, 'Aritmetická posloupnost', '2019-04-05 19:19:49', 3, 1),
(4, 'Geometická posloupnost', '2019-04-05 19:19:49', 5, 1),
(5, 'Logika', '2019-04-10 16:12:10', 6, 0);