SET FOREIGN_KEY_CHECKS = 0;

INSERT INTO edomp.category (id, label, created) VALUES (1, '1. Rovnice', '2019-06-16 10:12:16');
INSERT INTO edomp.category (id, label, created) VALUES (2, '2. Posloupnosti', '2019-06-16 10:12:25');

INSERT INTO edomp.difficulty (id, label, created) VALUES (1, 'Lehká', '2019-02-17 10:29:19');
INSERT INTO edomp.difficulty (id, label, created) VALUES (2, 'Střední', '2019-02-17 10:29:19');
INSERT INTO edomp.difficulty (id, label, created) VALUES (3, 'Těžká', '2019-02-17 10:29:19');

INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (1, 1, 'Administrators', '2019-05-02 10:56:53', null);
INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (28, 16, '1.A', '2019-06-20 13:30:39', 68);
INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (29, 16, '2.B', '2019-06-20 13:30:49', 68);
INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (30, 16, '2.A', '2019-06-20 13:30:53', 68);
INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (31, 17, 'Odpolední skupina', '2019-06-20 13:31:09', 68);
INSERT INTO edomp.`group` (id, super_group_id, label, created, created_by_id) VALUES (32, 16, 'Učitelé', '2019-06-20 13:32:19', 68);

INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (28, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (29, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (30, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (31, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (32, 2);

INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (1, 1, 0, '2019-04-27 11:49:59', 'Bez omezení', null);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (2, 1, 1, '2019-04-27 11:49:59', 'Kladný', 'positive');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (3, 1, 2, '2019-04-27 11:49:59', 'Nulový', 'zero');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (4, 1, 3, '2019-04-27 11:49:59', 'Záporný', 'negative');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (5, 2, 0, '2019-04-27 11:49:59', 'Bez omezení', null);
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (6, 2, 1, '2019-04-27 11:49:59', 'Kladný', 'positive');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (7, 2, 2, '2019-04-27 11:49:59', 'Nulový', 'zero');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (8, 2, 3, '2019-04-27 11:49:59', 'Záporný', 'negative');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (9, 2, 4, '2019-04-27 11:49:59', 'Celočíselný', 'integer');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (10, 2, 5, '2019-04-27 11:49:59', 'Kladný a odmocnitelný', 'positiveSquare');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (11, 3, 0, '2019-07-28 07:46:49', 'Existuje', 'differenceExists');
INSERT INTO edomp.problem_condition (id, problem_condition_type_id, accessor, created, label, validation_function) VALUES (12, 4, 0, '2019-07-28 07:46:55', 'Existuje', 'quotientExists');

INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (1, 'Zvolte podmínky výsledku', '2019-04-27 11:42:36', 'Podmínka výsledku', 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (2, 'Zvolte podmínky diskriminantu', '2019-04-27 11:42:58', 'Podmínka diskriminantu', 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (3, null, '2019-07-28 07:46:22', 'Podmínka diference', 1);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (4, null, '2019-07-28 11:53:58', 'Podmínka kvocientu', 1);

INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (1, 1);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (2, 2);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (3, 3);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (4, 3);


INSERT INTO edomp.problem_type (id, label, is_generatable, created) VALUES (1, 'Lineární rovnice', 1, '2019-04-05 19:19:49');
INSERT INTO edomp.problem_type (id, label, is_generatable, created) VALUES (2, 'Kvadratická rovnice', 1, '2019-04-05 19:19:49');
INSERT INTO edomp.problem_type (id, label, is_generatable, created) VALUES (3, 'Aritmetická posloupnost', 1, '2019-04-05 19:19:49');
INSERT INTO edomp.problem_type (id, label, is_generatable, created) VALUES (4, 'Geometická posloupnost', 1, '2019-04-05 19:19:49');
INSERT INTO edomp.problem_type (id, label, is_generatable, created) VALUES (11, 'Funkce', 0, '2019-06-20 12:47:56');


INSERT INTO edomp.role (id, label, created, `key`) VALUES (1, 'Administrátor', '2019-05-01 20:39:35', 'admin');
INSERT INTO edomp.role (id, label, created, `key`) VALUES (2, 'Učitel', '2019-05-01 20:39:51', 'teacher');
INSERT INTO edomp.role (id, label, created, `key`) VALUES (3, 'Student', '2019-05-01 20:39:51', 'student');

INSERT INTO edomp.sub_category (id, category_id, label, created) VALUES (1, 1, '1.1. Lineární rovnice', '2019-06-16 10:13:01');
INSERT INTO edomp.sub_category (id, category_id, label, created) VALUES (2, 1, '1.2. Kvadratické rovnice', '2019-06-16 10:14:52');
INSERT INTO edomp.sub_category (id, category_id, label, created) VALUES (3, 2, '2.1. Aritmetické posloupnosti', '2019-06-16 10:15:10');
INSERT INTO edomp.sub_category (id, category_id, label, created) VALUES (10, 2, '2.2. Geometrické posloupnosti', '2019-06-20 13:05:03');

INSERT INTO edomp.super_group (id, label, created, created_by_id) VALUES (1, 'Administrators', '2019-05-02 10:46:13', null);
INSERT INTO edomp.super_group (id, label, created, created_by_id) VALUES (16, 'Střední škola', '2019-06-20 13:30:19', 68);
INSERT INTO edomp.super_group (id, label, created, created_by_id) VALUES (17, 'Externisté', '2019-06-20 13:30:27', 68);

INSERT INTO edomp.super_group_category_rel (super_group_id, category_id) VALUES (16, 2);
INSERT INTO edomp.super_group_category_rel (super_group_id, category_id) VALUES (17, 2);

INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (68, 'admin', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 1, 1, null, 'Petr', 'Pondělík');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (69, 'jkohneke0@nba.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 2, 68, 'Joyce', 'Kohneke');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (70, 'mhazzard1@wiley.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 2, 68, 'Madelon', 'Hazzard');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (71, 'ddrable2@barnesandnoble.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 2, 68, 'Doralynn', 'Drable');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (72, 'mstammler3@so-net.ne.jp', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Mia', 'Stammler');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (73, 'hjann4@devhub.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Horatius', 'Jann');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (74, 'srosser5@tuttocitta.it', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Sara', 'Rosser');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (75, 'wweems6@scientificamerican.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Welbie', 'Weems');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (76, 'gpullar7@reference.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Garrett', 'Pullar');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (77, 'awenman8@ucoz.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Aguie', 'Wenman');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (78, 'nantonignetti9@springer.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Noreen', 'Antonignetti');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (79, 'hgrimstera@facebook.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Hildagarde', 'Grimster');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (80, 'lcavellb@pagesperso-orange.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Lezley', 'Cavell');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (81, 'woldacrec@sciencedaily.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Winonah', 'Oldacre');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (82, 'bgreserd@bloglovin.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Bear', 'Greser');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (83, 'moldrede@miibeian.gov.cn', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Margalit', 'Oldred');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (84, 'amaragaf@webnode.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Angeli', 'Maraga');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (85, 'ppeetermanng@fema.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Page', 'Peetermann');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (86, 'sbucketth@smugmug.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Serene', 'Buckett');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (87, 'adeadmani@tamu.edu', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Arielle', 'Deadman');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (88, 'gbockinj@cdc.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Giulio', 'Bockin');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (89, 'goxburyk@icq.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Georgy', 'Oxbury');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (90, 'abernlinl@parallels.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Annice', 'Bernlin');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (91, 'mcapronm@blogger.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Marylinda', 'Capron');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (92, 'gtarbattn@livejournal.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Gibby', 'Tarbatt');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (93, 'lnoviko@istockphoto.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Linette', 'Novik');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (94, 'scorderp@nytimes.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Sandye', 'Corder');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (95, 'nminesq@uol.com.br', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Nettie', 'Mines');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (96, 'scorbettr@wufoo.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Sherill', 'Corbett');
INSERT INTO edomp.user (id, username, password, created, is_admin, role_id, created_by_id, first_name, last_name) VALUES (97, 'bgrunguers@google.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', '2019-06-20 11:21:18', 0, 3, 68, 'Benjie', 'Grunguer');
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (69, 32);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (70, 32);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (71, 32);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (72, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (73, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (74, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (75, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (76, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (77, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (78, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (79, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (80, 28);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (81, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (82, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (83, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (84, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (85, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (86, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (87, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (88, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (89, 29);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (90, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (91, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (92, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (93, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (94, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (95, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (96, 30);
INSERT INTO edomp.user_group_rel (user_id, group_id) VALUES (97, 30);

SET FOREIGN_KEY_CHECKS = 1;
