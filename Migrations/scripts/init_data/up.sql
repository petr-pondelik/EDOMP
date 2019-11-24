SET FOREIGN_KEY_CHECKS=0;

INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (1, '2019-02-17 10:29:19', 'Lehká', 0);
INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (2, '2019-02-17 10:29:19', 'Střední', 0);
INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (3, '2019-02-17 10:29:19', 'Těžká', 0);

INSERT INTO edomp.super_group (id, created_by_id, created, label, teacher_level_secured) VALUES (1, null, '2019-05-02 10:46:13', 'Administrators', 1);

INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (1, 1, null, '2019-05-02 10:56:53', 'Administrators', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (2, 1, 3, '2019-06-20 13:32:19', 'Učitelé', 1);

INSERT INTO edomp.role (id, `key`, created, label) VALUES (1, 'admin', '2019-05-01 20:39:35', 'Administrátor');
INSERT INTO edomp.role (id, `key`, created, label) VALUES (2, 'teacher', '2019-05-01 20:39:51', 'Učitel');
INSERT INTO edomp.role (id, `key`, created, label) VALUES (3, 'student', '2019-05-01 20:39:51', 'Student');

INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name, created, teacher_level_secured) VALUES (1, null, 1, 'admin@email.com', 'admin', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 1, 'Petr', 'Pondělík', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (2, 1, 2, 'jkohneke0@nba.com', 'jkohneke0@nba.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Joyce', 'Kohneke', '2019-06-20 11:21:18', 1);

INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (1, 1);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (2, 2);

INSERT INTO edomp.problem_type (id, created, label, key_label, teacher_level_secured) VALUES (1, '2019-04-05 19:19:49', 'Lineární rovnice', 'LinearEquation', 0);
INSERT INTO edomp.problem_type (id, created, label, key_label, teacher_level_secured) VALUES (2, '2019-04-05 19:19:49', 'Kvadratická rovnice', 'QuadraticEquation', 0);
INSERT INTO edomp.problem_type (id, created, label, key_label, teacher_level_secured) VALUES (3, '2019-04-05 19:19:49', 'Aritmetická posloupnost', 'ArithmeticSequence', 0);
INSERT INTO edomp.problem_type (id, created, label, key_label, teacher_level_secured) VALUES (4, '2019-04-05 19:19:49', 'Geometická posloupnost', 'GeometricSequence', 0);

INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (1, 'Zvolte podmínky výsledku', '2019-04-27 11:42:36', 'Podmínka výsledku', 0, 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (2, 'Zvolte podmínky diskriminantu', '2019-04-27 11:42:58', 'Podmínka diskriminantu', 0, 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (3, null, '2019-07-28 07:46:22', 'Podmínka aritmetické posloupnosti (typ)', 1, 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (4, null, '2019-07-28 11:53:58', 'Podmínka geometrické posloupnosti (typ)', 1, 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (5, null, '2019-08-16 16:36:31', 'Podmínka lineární rovnice (typ)', 1, 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation, teacher_level_secured) VALUES (6, null, '2019-08-16 20:11:38', 'Podmínka kvadratické rovnice (typ)', 1, 0);

INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (1, 1, 0, '2019-04-27 11:49:59', 'Bez omezení', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (2, 1, 1, '2019-04-27 11:49:59', 'Kladný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (3, 1, 2, '2019-04-27 11:49:59', 'Nulový', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (4, 1, 3, '2019-04-27 11:49:59', 'Záporný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (5, 2, 0, '2019-04-27 11:49:59', 'Bez omezení', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (6, 2, 1, '2019-04-27 11:49:59', 'Kladný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (7, 2, 2, '2019-04-27 11:49:59', 'Nulový', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (8, 2, 3, '2019-04-27 11:49:59', 'Záporný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (9, 2, 4, '2019-04-27 11:49:59', 'Celočíselný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (10, 2, 5, '2019-04-27 11:49:59', 'Kladný a odmocnitelný', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (11, 3, 0, '2019-07-28 07:46:49', 'Aritmetická posloupnost (typ)', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (12, 4, 0, '2019-07-28 07:46:55', 'Geometrická posloupnost (typ)', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (13, 5, 0, '2019-08-16 16:37:06', 'Lineární rovnice (typ)', 0);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, teacher_level_secured) VALUES (14, 6, 0, '2019-08-16 20:15:16', 'Kvadratická rovnice (typ)', 0);

INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (1, 1);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (2, 2);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (3, 3);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (4, 3);

SET FOREIGN_KEY_CHECKS=1;