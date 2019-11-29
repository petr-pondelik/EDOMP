SET FOREIGN_KEY_CHECKS=0;

INSERT INTO edomp.theme (id, label, created, created_by_id, teacher_level_secured) VALUES (1, '1. Rovnice', '2019-06-16 10:12:16', 2, 1);
INSERT INTO edomp.theme (id, label, created, created_by_id, teacher_level_secured) VALUES (2, '2. Posloupnosti', '2019-06-16 10:12:25', 2, 1);

INSERT INTO edomp.sub_theme (id, created_by_id, theme_id, created, label, teacher_level_secured) VALUES (1, 2, 1, '2019-06-16 10:13:01', '1.1. Lineární rovnice', 1);
INSERT INTO edomp.sub_theme (id, created_by_id, theme_id, created, label, teacher_level_secured) VALUES (2, 2, 1, '2019-06-16 10:14:52', '1.2. Kvadratické rovnice', 1);
INSERT INTO edomp.sub_theme (id, created_by_id, theme_id, created, label, teacher_level_secured) VALUES (3, 2, 2, '2019-06-16 10:15:10', '2.1. Aritmetické posloupnosti', 1);
INSERT INTO edomp.sub_theme (id, created_by_id, theme_id, created, label, teacher_level_secured) VALUES (4, 2, 2, '2019-06-20 13:05:03', '2.2. Geometrické posloupnosti', 1);

INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (1, '2019-02-17 10:29:19', 'Lehká', 0);
INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (2, '2019-02-17 10:29:19', 'Střední', 0);
INSERT INTO edomp.difficulty (id, created, label, teacher_level_secured) VALUES (3, '2019-02-17 10:29:19', 'Těžká', 0);

INSERT INTO edomp.super_group (id, created_by_id, created, label, teacher_level_secured) VALUES (1, null, '2019-05-02 10:46:13', 'Administrators', 1);
INSERT INTO edomp.super_group (id, created_by_id, created, label, teacher_level_secured) VALUES (2, null, '2019-05-02 10:46:13', 'Učitelé', 1);
INSERT INTO edomp.super_group (id, created_by_id, created, label, teacher_level_secured) VALUES (3, 2, '2019-06-20 13:30:19', 'Střední škola', 1);
INSERT INTO edomp.super_group (id, created_by_id, created, label, teacher_level_secured) VALUES (4, 3, '2019-06-20 13:30:27', 'Externisté', 1);

INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (1, 1, null, '2019-05-02 10:56:53', 'Administrators', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (2, 2, null, '2019-06-20 13:32:19', 'Učitelé', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (3, 3, 2, '2019-06-20 13:30:39', '1.A', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (4, 3, 2, '2019-06-20 13:30:49', '2.B', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (5, 3, 2, '2019-06-20 13:30:53', '2.A', 1);
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label, teacher_level_secured) VALUES (6, 4, 3, '2019-06-20 13:31:09', 'Odpolední skupina', 1);

INSERT INTO edomp.role (id, `key`, created, label, teacher_level_secured) VALUES (1, 'admin', '2019-05-01 20:39:35', 'Administrátor', 0);
INSERT INTO edomp.role (id, `key`, created, label, teacher_level_secured) VALUES (2, 'teacher', '2019-05-01 20:39:51', 'Učitel', 0);
INSERT INTO edomp.role (id, `key`, created, label, teacher_level_secured) VALUES (3, 'student', '2019-05-01 20:39:51', 'Student', 0);

INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (1, null, 1, 'admin@email.com', 'admin', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 1, 'Petr', 'Pondělík', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (2, 1, 2, 'jkohneke0@nba.com', 'jkohneke0@nba.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Joyce', 'Kohneke', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (3, 1, 2, 'mhazzard1@wiley.com', 'mhazzard1@wiley.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Madelon', 'Hazzard', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (4, 2, 3, 'mstammler3@so-net.ne.jp', 'mstammler3@so-net.ne.jp', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Mia', 'Stammler', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (5, 2, 3, 'hjann4@devhub.com', 'hjann4@devhub.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Horatius', 'Jann', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (6, 2, 3, 'srosser5@tuttocitta.it', 'srosser5@tuttocitta.it', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sara', 'Rosser', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (7, 2, 3, 'wweems6@scientificamerican.com', 'wweems6@scientificamerican.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Welbie', 'Weems', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (8, 2, 3, 'gpullar7@reference.com', 'gpullar7@reference.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Garrett', 'Pullar', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (9, 2, 3, 'awenman8@ucoz.com', 'awenman8@ucoz.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Aguie', 'Wenman', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (10, 2, 3, 'nantonignetti9@springer.com', 'nantonignetti9@springer.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Noreen', 'Antonignetti', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (11, 2, 3, 'hgrimstera@facebook.com', 'hgrimstera@facebook.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Hildagarde', 'Grimster', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (12, 2, 3, 'lcavellb@pagesperso-orange.fr', 'lcavellb@pagesperso-orange.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Lezley', 'Cavell', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (13, 2, 3, 'woldacrec@sciencedaily.com', 'woldacrec@sciencedaily.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Winonah', 'Oldacre', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (14, 2, 3, 'bgreserd@bloglovin.com', 'bgreserd@bloglovin.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Bear', 'Greser', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (15, 2, 3, 'moldrede@miibeian.gov.cn', 'moldrede@miibeian.gov.cn', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Margalit', 'Oldred', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (16, 2, 3, 'amaragaf@webnode.com', 'amaragaf@webnode.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Angeli', 'Maraga', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (17, 2, 3, 'ppeetermanng@fema.gov', 'ppeetermanng@fema.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Page', 'Peetermann', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (18, 2, 3, 'sbucketth@smugmug.com', 'sbucketth@smugmug.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Serene', 'Buckett', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (19, 2, 3, 'adeadmani@tamu.edu', 'adeadmani@tamu.edu', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Arielle', 'Deadman', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (20, 2, 3, 'gbockinj@cdc.gov', 'gbockinj@cdc.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Giulio', 'Bockin', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (21, 3, 3, 'goxburyk@icq.com', 'goxburyk@icq.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Georgy', 'Oxbury', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (22, 3, 3, 'abernlinl@parallels.com', 'abernlinl@parallels.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Annice', 'Bernlin', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (23, 3, 3, 'mcapronm@blogger.com', 'mcapronm@blogger.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Marylinda', 'Capron', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (24, 3, 3, 'gtarbattn@livejournal.com', 'gtarbattn@livejournal.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Gibby', 'Tarbatt', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (25, 3, 3, 'lnoviko@istockphoto.com', 'lnoviko@istockphoto.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Linette', 'Novik', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (26, 3, 3, 'scorderp@nytimes.com', 'scorderp@nytimes.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sandye', 'Corder', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (27, 3, 3, 'nminesq@uol.com.br', 'nminesq@uol.com.br', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Nettie', 'Mines', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (28, 3, 3, 'scorbettr@wufoo.com', 'scorbettr@wufoo.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sherill', 'Corbett', '2019-06-20 11:21:18', 1);
INSERT INTO edomp.user (id, created_by_id, role_id, email, username, password, is_admin, first_name, last_name,created, teacher_level_secured) VALUES (29, 3, 3, 'bgrunguers@google.fr', 'bgrunguers@google.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Benjie', 'Grunguer', '2019-06-20 11:21:18', 1);

# Insert Users relations to the Groups
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (1, 1);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (2, 2);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (3, 2);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (4, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (5, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (6, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (7, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (8, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (9, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (10, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (11, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (12, 3);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (13, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (14, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (15, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (16, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (17, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (18, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (19, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (20, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (21, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (22, 4);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (23, 5);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (24, 5);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (25, 5);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (26, 5);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (27, 5);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (28, 6);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (29, 6);

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

# Insert logos
INSERT INTO edomp.logo (id, created_by_id, path, extension, extension_tmp, created, teacher_level_secured, label) VALUES (1, 2, '/data_public/logos/1/file.jpg', '.jpg', '.jpg', '2019-11-26 22:22:48', 1, 'Testing logo 1');
INSERT INTO edomp.logo (id, created_by_id, path, extension, extension_tmp, created, teacher_level_secured, label) VALUES (2, 1, '/data_public/logos/2/file.jpg', '.jpg', '.jpg', '2019-11-26 22:22:48', 1, 'Testing logo 2');

# Insert ProblemTemplate
INSERT INTO edomp.problem (id, problem_type_id, difficulty_id, sub_theme_id, created_by_id, body, text_before, text_after, success_rate, is_template, is_generated, student_visible, created, teacher_level_secured, discr) VALUES (1, 1, 1, 1, 2, '$$ x + <par min="-5" max="15"/> = 4 $$', '', '', null, 1, 0, 1, '2019-11-26 22:26:08', 1, 'lineareqtemplate');
INSERT INTO edomp.problem_template (id, matches) VALUES (1, '"[{\\"p0\\":-5},{\\"p0\\":-4},{\\"p0\\":-3},{\\"p0\\":-2},{\\"p0\\":-1},{\\"p0\\":0},{\\"p0\\":1},{\\"p0\\":2},{\\"p0\\":3}]"');
INSERT INTO edomp.linear_equation_template (id, variable) VALUES (1, 'x');

# Insert Test
INSERT INTO edomp.test (id, logo_id, created_by_id, introduction_text, school_year, test_number, variants_cnt, problems_per_variant, term, is_closed, created, teacher_level_secured) VALUES (1, 1, 2, '', '2018/19', 1, 2, 1, '1. pol.', 1, '2019-11-26 22:27:06', 1);

# Insert Test variants
INSERT INTO edomp.test_variant (id, test_id, created, teacher_level_secured, label) VALUES (1, 1, '2019-11-26 22:27:06', 0, 'A');
INSERT INTO edomp.test_variant (id, test_id, created, teacher_level_secured, label) VALUES (2, 1, '2019-11-26 22:27:07', 0, 'B');

# Insert ProblemFinal - TestVariant associations
INSERT INTO edomp.problem_final_test_variant_association (id, problem_final_id, problem_template_id, test_variant_id, next_page, success_rate, created, teacher_level_secured) VALUES (1, 2, null, 1, 0, null, '2019-11-26 22:27:06', 0);
INSERT INTO edomp.problem_final_test_variant_association (id, problem_final_id, problem_template_id, test_variant_id, next_page, success_rate, created, teacher_level_secured) VALUES (2, 3, null, 2, 0, null, '2019-11-26 22:27:07', 0);

# Insert ProblemFinal
INSERT INTO edomp.problem (id, problem_type_id, difficulty_id, sub_theme_id, created_by_id, body, text_before, text_after, success_rate, is_template, is_generated, student_visible, created, teacher_level_secured, discr) VALUES (2, 1, 1, 1, 2, '$$ x + 3 = 4 $$', '', '', null, 0, 1, 1, '2019-11-26 22:27:06', 1, 'problemfinal');
INSERT INTO edomp.problem (id, problem_type_id, difficulty_id, sub_theme_id, created_by_id, body, text_before, text_after, success_rate, is_template, is_generated, student_visible, created, teacher_level_secured, discr) VALUES (3, 1, 1, 1, 2, '$$ x + 3 = 4 $$', '', '', null, 0, 1, 1, '2019-11-26 22:27:07', 1, 'problemfinal');
INSERT INTO edomp.problem_final (id, problem_template_id, matches_index, result) VALUES (2, 1, 8, null);
INSERT INTO edomp.problem_final (id, problem_template_id, matches_index, result) VALUES (3, 1, 8, null);

# Insert Groups relations to the Tests
INSERT INTO edomp.test_group_rel (test_id, group_id) VALUES (1, 4);
INSERT INTO edomp.test_group_rel (test_id, group_id) VALUES (1, 5);

# Insert Test Filters
INSERT INTO edomp.filter (id, test_id, selected_filters, selected_problems, seq, created, teacher_level_secured) VALUES (1, 1, '{"isGenerated":false,"createdBy":2,"isTemplate":1,"problemType":[],"difficulty":[],"subTheme":[],"conditionType":{"1":[],"2":[],"3":[],"4":[],"5":[],"6":[]}}', '[]', 0, '2019-11-26 22:27:06', 0);

SET FOREIGN_KEY_CHECKS=1;