SET FOREIGN_KEY_CHECKS=0;

INSERT INTO edomp.category (id, label, created) VALUES (1, '1. Rovnice', '2019-06-16 10:12:16');
INSERT INTO edomp.category (id, label, created) VALUES (2, '2. Posloupnosti', '2019-06-16 10:12:25');

INSERT INTO edomp.difficulty (id, created, label) VALUES (1, '2019-02-17 10:29:19', 'Lehká');
INSERT INTO edomp.difficulty (id, created, label) VALUES (2, '2019-02-17 10:29:19', 'Střední');
INSERT INTO edomp.difficulty (id, created, label) VALUES (3, '2019-02-17 10:29:19', 'Těžká');

INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (28, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (29, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (30, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (31, 2);
INSERT INTO edomp.group_category_rel (group_id, category_id) VALUES (32, 2);

INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (1, 1, null, '2019-05-02 10:56:53', 'Administrators');
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (28, 16, 68, '2019-06-20 13:30:39', '1.A');
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (29, 16, 68, '2019-06-20 13:30:49', '2.B');
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (30, 16, 68, '2019-06-20 13:30:53', '2.A');
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (31, 17, 68, '2019-06-20 13:31:09', 'Odpolední skupina');
INSERT INTO edomp.`group` (id, super_group_id, created_by_id, created, label) VALUES (32, 16, 68, '2019-06-20 13:32:19', 'Učitelé');

INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (1, 'Zvolte podmínky výsledku', '2019-04-27 11:42:36', 'Podmínka výsledku', 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (2, 'Zvolte podmínky diskriminantu', '2019-04-27 11:42:58', 'Podmínka diskriminantu', 0);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (3, null, '2019-07-28 07:46:22', 'Podmínka aritmetické posloupnosti (typ)', 1);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (4, null, '2019-07-28 11:53:58', 'Podmínka geometrické posloupnosti (typ)', 1);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (5, null, '2019-08-16 16:36:31', 'Podmínka lineární rovnice (typ)', 1);
INSERT INTO edomp.problem_condition_type (id, prompt, created, label, is_validation) VALUES (6, null, '2019-08-16 20:11:38', 'Podmínka kvadratické rovnice (typ)', 1);

INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (1, null, 1, 0, '2019-04-27 11:49:59', 'Bez omezení');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (2, 5, 1, 1, '2019-04-27 11:49:59', 'Kladný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (3, 6, 1, 2, '2019-04-27 11:49:59', 'Nulový');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (4, 7, 1, 3, '2019-04-27 11:49:59', 'Záporný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (5, null, 2, 0, '2019-04-27 11:49:59', 'Bez omezení');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (6, 5, 2, 1, '2019-04-27 11:49:59', 'Kladný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (7, 6, 2, 2, '2019-04-27 11:49:59', 'Nulový');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (8, 7, 2, 3, '2019-04-27 11:49:59', 'Záporný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (9, 9, 2, 4, '2019-04-27 11:49:59', 'Celočíselný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (10, 8, 2, 5, '2019-04-27 11:49:59', 'Kladný a odmocnitelný');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (11, 3, 3, 0, '2019-07-28 07:46:49', 'Aritmetická posloupnost (typ)');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (12, 4, 4, 0, '2019-07-28 07:46:55', 'Geometrická posloupnost (typ)');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (13, 1, 5, 0, '2019-08-16 16:37:06', 'Lineární rovnice (typ)');
INSERT INTO edomp.problem_condition (id, validation_function_id, problem_condition_type_id, accessor, created, label) VALUES (14, 2, 6, 0, '2019-08-16 20:15:16', 'Kvadratická rovnice (typ)');

INSERT INTO edomp.problem_type (id, is_generatable, created, label, key_label) VALUES (1, 1, '2019-04-05 19:19:49', 'Lineární rovnice', 'LinearEquation');
INSERT INTO edomp.problem_type (id, is_generatable, created, label, key_label) VALUES (2, 1, '2019-04-05 19:19:49', 'Kvadratická rovnice', 'QuadraticEquation');
INSERT INTO edomp.problem_type (id, is_generatable, created, label, key_label) VALUES (3, 1, '2019-04-05 19:19:49', 'Aritmetická posloupnost', 'ArithmeticSequence');
INSERT INTO edomp.problem_type (id, is_generatable, created, label, key_label) VALUES (4, 1, '2019-04-05 19:19:49', 'Geometická posloupnost', 'GeometricSequence');

INSERT INTO edomp.role (id, `key`, created, label) VALUES (1, 'admin', '2019-05-01 20:39:35', 'Administrátor');
INSERT INTO edomp.role (id, `key`, created, label) VALUES (2, 'teacher', '2019-05-01 20:39:51', 'Učitel');
INSERT INTO edomp.role (id, `key`, created, label) VALUES (3, 'student', '2019-05-01 20:39:51', 'Student');

INSERT INTO edomp.sub_category (id, category_id, created, label) VALUES (1, 1, '2019-06-16 10:13:01', '1.1. Lineární rovnice');
INSERT INTO edomp.sub_category (id, category_id, created, label) VALUES (2, 1, '2019-06-16 10:14:52', '1.2. Kvadratické rovnice');
INSERT INTO edomp.sub_category (id, category_id, created, label) VALUES (3, 2, '2019-06-16 10:15:10', '2.1. Aritmetické posloupnosti');
INSERT INTO edomp.sub_category (id, category_id, created, label) VALUES (10, 2, '2019-06-20 13:05:03', '2.2. Geometrické posloupnosti');

INSERT INTO edomp.super_group_category_rel (super_group_id, category_id) VALUES (16, 2);
INSERT INTO edomp.super_group_category_rel (super_group_id, category_id) VALUES (17, 2);

INSERT INTO edomp.super_group (id, created_by_id, created, label) VALUES (1, null, '2019-05-02 10:46:13', 'Administrators');
INSERT INTO edomp.super_group (id, created_by_id, created, label) VALUES (16, 68, '2019-06-20 13:30:19', 'Střední škola');
INSERT INTO edomp.super_group (id, created_by_id, created, label) VALUES (17, 68, '2019-06-20 13:30:27', 'Externisté');

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

INSERT INTO edomp.validation_function (id, created, label) VALUES (1, '2019-08-02 15:10:20', 'linearEquationType');
INSERT INTO edomp.validation_function (id, created, label) VALUES (2, '2019-08-02 15:10:20', 'quadraticEquationType');
INSERT INTO edomp.validation_function (id, created, label) VALUES (3, '2019-08-02 15:10:20', 'arithmeticSequenceType');
INSERT INTO edomp.validation_function (id, created, label) VALUES (4, '2019-08-02 15:10:20', 'geometricSequenceType');
INSERT INTO edomp.validation_function (id, created, label) VALUES (5, '2019-08-02 15:10:20', 'positive');
INSERT INTO edomp.validation_function (id, created, label) VALUES (6, '2019-08-02 15:10:20', 'zero');
INSERT INTO edomp.validation_function (id, created, label) VALUES (7, '2019-08-02 15:10:20', 'negative');
INSERT INTO edomp.validation_function (id, created, label) VALUES (8, '2019-08-16 16:37:26', 'positiveSquare');
INSERT INTO edomp.validation_function (id, created, label) VALUES (9, '2019-08-16 20:15:48', 'integer');

INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (68, null, 1, 'admin', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 1, 'Petr', 'Pondělík', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (69, 68, 2, 'jkohneke0@nba.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Joyce', 'Kohneke', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (70, 68, 2, 'mhazzard1@wiley.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Madelon', 'Hazzard', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (71, 68, 2, 'ddrable2@barnesandnoble.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Doralynn', 'Drable', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (72, 68, 3, 'mstammler3@so-net.ne.jp', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Mia', 'Stammler', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (73, 68, 3, 'hjann4@devhub.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Horatius', 'Jann', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (74, 68, 3, 'srosser5@tuttocitta.it', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sara', 'Rosser', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (75, 68, 3, 'wweems6@scientificamerican.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Welbie', 'Weems', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (76, 68, 3, 'gpullar7@reference.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Garrett', 'Pullar', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (77, 68, 3, 'awenman8@ucoz.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Aguie', 'Wenman', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (78, 68, 3, 'nantonignetti9@springer.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Noreen', 'Antonignetti', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (79, 68, 3, 'hgrimstera@facebook.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Hildagarde', 'Grimster', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (80, 68, 3, 'lcavellb@pagesperso-orange.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Lezley', 'Cavell', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (81, 68, 3, 'woldacrec@sciencedaily.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Winonah', 'Oldacre', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (82, 68, 3, 'bgreserd@bloglovin.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Bear', 'Greser', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (83, 68, 3, 'moldrede@miibeian.gov.cn', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Margalit', 'Oldred', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (84, 68, 3, 'amaragaf@webnode.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Angeli', 'Maraga', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (85, 68, 3, 'ppeetermanng@fema.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Page', 'Peetermann', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (86, 68, 3, 'sbucketth@smugmug.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Serene', 'Buckett', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (87, 68, 3, 'adeadmani@tamu.edu', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Arielle', 'Deadman', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (88, 68, 3, 'gbockinj@cdc.gov', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Giulio', 'Bockin', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (89, 68, 3, 'goxburyk@icq.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Georgy', 'Oxbury', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (90, 68, 3, 'abernlinl@parallels.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Annice', 'Bernlin', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (91, 68, 3, 'mcapronm@blogger.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Marylinda', 'Capron', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (92, 68, 3, 'gtarbattn@livejournal.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Gibby', 'Tarbatt', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (93, 68, 3, 'lnoviko@istockphoto.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Linette', 'Novik', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (94, 68, 3, 'scorderp@nytimes.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sandye', 'Corder', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (95, 68, 3, 'nminesq@uol.com.br', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Nettie', 'Mines', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (96, 68, 3, 'scorbettr@wufoo.com', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Sherill', 'Corbett', '2019-06-20 11:21:18');
INSERT INTO edomp.user (id, created_by_id, role_id, username, password, is_admin, first_name, last_name, created) VALUES (97, 68, 3, 'bgrunguers@google.fr', '$2y$10$E7uP70dMpG56mwA/b62VeOwR7bheongU0Wa7lP7S7kozNCsOyqIeO', 0, 'Benjie', 'Grunguer', '2019-06-20 11:21:18');

INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (1, 1);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (2, 2);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (3, 3);
INSERT INTO edomp.problem_tp_problem_condition_tp_rel (problem_type_id, problem_condition_type_id) VALUES (4, 3);

SET FOREIGN_KEY_CHECKS=1;
