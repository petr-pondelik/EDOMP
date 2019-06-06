INSERT INTO `problem_condition_type` (`accessor`, `label`, `created`) VALUES
(1, 'Podmínka výsledku', '2019-04-27 11:42:36'),
(2, 'Podmínka diskriminantu', '2019-04-27 11:42:58');

INSERT INTO `problem_condition` (`condition_type_id`, `accessor`, `label`, `created`) VALUES
(1, 0, 'Bez omezení', '2019-04-27 11:49:59'),
(1, 1, 'Kladný', '2019-04-27 11:49:59'),
(1, 2, 'Nulový', '2019-04-27 11:49:59'),
(1, 3, 'Záporný', '2019-04-27 11:49:59'),
(2, 0, 'Bez omezení', '2019-04-27 11:49:59'),
(2, 1, 'Kladný', '2019-04-27 11:49:59'),
(2, 2, 'Nulový', '2019-04-27 11:49:59'),
(2, 3, 'Záporný', '2019-04-27 11:49:59'),
(2, 4, 'Celočíselný', '2019-04-27 11:49:59'),
(2, 5, 'Kladný a odmocnitelný', '2019-04-27 11:49:59');

INSERT INTO `difficulty` (`label`, `created`) VALUES
('Lehká', '2019-02-17 10:29:19'),
('Střední', '2019-02-17 10:29:19'),
('Těžká', '2019-02-17 10:29:19');

INSERT INTO `category` (`label`, `created`) VALUES
('1. Rovnice', '2019-04-27 14:20:50'),
('2. Posloupnosti', '2019-04-27 14:21:09'),

INSERT INTO `sub_category` (`category_id`, `label`, `created`) VALUES
(1, '1.1. Lineární rovnice', '2019-04-27 14:21:31'),
(1, '1.2. Kvadratické rovnice', '2019-04-27 14:21:42'),
(2, '2.1. Aritmetické posloupnosti', '2019-04-27 14:21:54'),
(2, '2.2. Geometrické posloupnosti', '2019-04-27 14:22:07'),

INSERT INTO `problem_type` (`label`, `created`, `accessor`, `is_generatable`) VALUES
('Lineární rovnice', '2019-04-05 19:19:49', 1, 1),
('Kvadratická rovnice', '2019-04-05 19:19:49', 2, 1),
('Aritmetická posloupnost', '2019-04-05 19:19:49', 3, 1),
('Geometická posloupnost', '2019-04-05 19:19:49', 5, 1),

INSERT INTO edomp_final.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (1, 1);
INSERT INTO edomp_final.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (2, 2);