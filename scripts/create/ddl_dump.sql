create table category
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table difficulty
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table logo
(
	id int auto_increment
		primary key,
	path varchar(255) null,
	extension varchar(255) null,
	extension_tmp varchar(255) not null,
	is_used tinyint(1) not null,
	label varchar(255) not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table problem_condition_problem_rel
(
	problem_final_id int not null,
	problem_condition_id int not null,
	primary key (problem_final_id, problem_condition_id)
)
collate=utf8_unicode_ci;

create index IDX_1430909773A5214B
	on problem_condition_problem_rel (problem_condition_id);

create index IDX_14309097DA3E196B
	on problem_condition_problem_rel (problem_final_id);

create table problem_condition_type
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table problem_condition
(
	id int auto_increment
		primary key,
	problem_condition_type_id int null,
	accessor int not null,
	label varchar(255) not null,
	created datetime not null,
	constraint FK_22A086E4FF45F437
		foreign key (problem_condition_type_id) references problem_condition_type (id)
)
collate=utf8_unicode_ci;

create index IDX_22A086E4FF45F437
	on problem_condition (problem_condition_type_id);

create table problem_type
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	is_generatable tinyint(1) null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table problem_tp_problem_condition_tp_rel
(
	problem_type_id int not null,
	problem_condition_type_id int not null,
	primary key (problem_type_id, problem_condition_type_id),
	constraint FK_55A5508236E4CE0
		foreign key (problem_type_id) references problem_type (id)
			on delete cascade,
	constraint FK_55A5508FF45F437
		foreign key (problem_condition_type_id) references problem_condition_type (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_55A5508236E4CE0
	on problem_tp_problem_condition_tp_rel (problem_type_id);

create index IDX_55A5508FF45F437
	on problem_tp_problem_condition_tp_rel (problem_condition_type_id);

create table role
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null,
	`key` varchar(255) not null
)
collate=utf8_unicode_ci;

create table sub_category
(
	id int auto_increment
		primary key,
	category_id int null,
	label varchar(255) not null,
	created datetime not null,
	constraint FK_BCE3F79812469DE2
		foreign key (category_id) references category (id)
)
collate=utf8_unicode_ci;

create table problem
(
	id int auto_increment
		primary key,
	problem_type_id int null,
	difficulty_id int null,
	sub_category_id int null,
	body longtext not null,
	text_before longtext null,
	text_after longtext null,
	success_rate double null,
	created datetime not null,
	discr varchar(255) not null,
	is_template tinyint(1) null,
	constraint FK_D7E7CCC8236E4CE0
		foreign key (problem_type_id) references problem_type (id),
	constraint FK_D7E7CCC8F7BFE87C
		foreign key (sub_category_id) references sub_category (id),
	constraint FK_D7E7CCC8FCFA9DAE
		foreign key (difficulty_id) references difficulty (id)
)
collate=utf8_unicode_ci;

create table arithmetic_seq_templ
(
	id int not null
		primary key,
	variable varchar(255) not null,
	first_n int not null,
	difference double null,
	constraint FK_3E471ABEBF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create table geometric_seq_templ
(
	id int not null
		primary key,
	variable varchar(255) not null,
	first_n int not null,
	quotient double null,
	constraint FK_DC9BD28CBF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create table linear_eq_templ
(
	id int not null
		primary key,
	variable varchar(255) not null,
	constraint FK_7C28422FBF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_D7E7CCC8236E4CE0
	on problem (problem_type_id);

create index IDX_D7E7CCC8F7BFE87C
	on problem (sub_category_id);

create index IDX_D7E7CCC8FCFA9DAE
	on problem (difficulty_id);

create table problem_condition_problem_template_rel
(
	problem_id int not null,
	problem_condition_id int not null,
	primary key (problem_id, problem_condition_id),
	constraint FK_5474336873A5214B
		foreign key (problem_condition_id) references problem_condition (id)
			on delete cascade,
	constraint FK_54743368A0DCED86
		foreign key (problem_id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_5474336873A5214B
	on problem_condition_problem_template_rel (problem_condition_id);

create index IDX_54743368A0DCED86
	on problem_condition_problem_template_rel (problem_id);

create table problem_template
(
	id int not null
		primary key,
	matches longtext null comment '(DC2Type:json)',
	constraint FK_5E8A1DC5BF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create table problem_final
(
	id int not null
		primary key,
	problem_template_id int null,
	result longtext null,
	is_generated tinyint(1) null,
	variable varchar(1) null,
	first_n int null,
	constraint FK_74DD736D32497E7
		foreign key (problem_template_id) references problem_template (id),
	constraint FK_74DD736DBF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_74DD736D32497E7
	on problem_final (problem_template_id);

create table quadratic_eq_templ
(
	id int not null
		primary key,
	variable varchar(1) not null,
	constraint FK_C98826F6BF396750
		foreign key (id) references problem (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_BCE3F79812469DE2
	on sub_category (category_id);

create table template_json_data
(
	id int auto_increment
		primary key,
	json_data longtext null comment '(DC2Type:json)',
	template_id int not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table term
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null
)
collate=utf8_unicode_ci;

create table test
(
	id int auto_increment
		primary key,
	logo_id int null,
	introduction_text varchar(255) null,
	school_year varchar(255) not null,
	test_number int not null,
	created datetime not null,
	term varchar(255) not null,
	constraint FK_D87F7E0CF98F144A
		foreign key (logo_id) references logo (id)
)
collate=utf8_unicode_ci;

create table problem_test_association
(
	id int auto_increment
		primary key,
	problem_id int null,
	problem_template_id int null,
	test_id int null,
	variant varchar(255) not null,
	next_page tinyint(1) null,
	success_rate double null,
	created datetime not null,
	constraint FK_894CF27F1E5D0459
		foreign key (test_id) references test (id),
	constraint FK_894CF27F32497E7
		foreign key (problem_template_id) references problem_template (id),
	constraint FK_894CF27FA0DCED86
		foreign key (problem_id) references problem_final (id)
)
collate=utf8_unicode_ci;

create index IDX_894CF27F1E5D0459
	on problem_test_association (test_id);

create index IDX_894CF27F32497E7
	on problem_test_association (problem_template_id);

create index IDX_894CF27FA0DCED86
	on problem_test_association (problem_id);

create index IDX_D87F7E0CF98F144A
	on test (logo_id);

create table user
(
	id int auto_increment
		primary key,
	username varchar(255) not null,
	password varchar(255) not null,
	created datetime not null,
	is_admin tinyint(1) null,
	role_id int null,
	created_by_id int null,
	first_name varchar(255) null,
	last_name varchar(255) null,
	constraint FK_8D93D649B03A8386
		foreign key (created_by_id) references user (id),
	constraint FK_8D93D649D60322AC
		foreign key (role_id) references role (id)
)
collate=utf8_unicode_ci;

create table super_group
(
	id int auto_increment
		primary key,
	label varchar(255) not null,
	created datetime not null,
	created_by_id int null,
	constraint FK_59426E30B03A8386
		foreign key (created_by_id) references user (id)
)
collate=utf8_unicode_ci;

create table `group`
(
	id int auto_increment
		primary key,
	super_group_id int null,
	label varchar(255) not null,
	created datetime not null,
	created_by_id int null,
	constraint FK_6DC044C52186466C
		foreign key (super_group_id) references super_group (id),
	constraint FK_6DC044C5B03A8386
		foreign key (created_by_id) references user (id)
)
collate=utf8_unicode_ci;

create index IDX_6DC044C52186466C
	on `group` (super_group_id);

create index IDX_6DC044C5B03A8386
	on `group` (created_by_id);

create table group_category_rel
(
	group_id int not null,
	category_id int not null,
	primary key (group_id, category_id),
	constraint FK_3D3E30DE12469DE2
		foreign key (category_id) references category (id)
			on delete cascade,
	constraint FK_3D3E30DEFE54D947
		foreign key (group_id) references `group` (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_3D3E30DE12469DE2
	on group_category_rel (category_id);

create index IDX_3D3E30DEFE54D947
	on group_category_rel (group_id);

create index IDX_59426E30B03A8386
	on super_group (created_by_id);

create table super_group_category_rel
(
	super_group_id int not null,
	category_id int not null,
	primary key (super_group_id, category_id),
	constraint FK_861C47DB12469DE2
		foreign key (category_id) references category (id)
			on delete cascade,
	constraint FK_861C47DB2186466C
		foreign key (super_group_id) references super_group (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_861C47DB12469DE2
	on super_group_category_rel (category_id);

create index IDX_861C47DB2186466C
	on super_group_category_rel (super_group_id);

create table test_group_rel
(
	test_id int not null,
	group_id int not null,
	primary key (test_id, group_id),
	constraint FK_F5B5A80E1E5D0459
		foreign key (test_id) references test (id)
			on delete cascade,
	constraint FK_F5B5A80EFE54D947
		foreign key (group_id) references `group` (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_F5B5A80E1E5D0459
	on test_group_rel (test_id);

create index IDX_F5B5A80EFE54D947
	on test_group_rel (group_id);

create index IDX_8D93D649B03A8386
	on user (created_by_id);

create index IDX_8D93D649D60322AC
	on user (role_id);

create table user_group_rel
(
	user_id int not null,
	group_id int not null,
	primary key (user_id, group_id),
	constraint FK_77C8B220A76ED395
		foreign key (user_id) references user (id)
			on delete cascade,
	constraint FK_77C8B220FE54D947
		foreign key (group_id) references `group` (id)
			on delete cascade
)
collate=utf8_unicode_ci;

create index IDX_77C8B220A76ED395
	on user_group_rel (user_id);

create index IDX_77C8B220FE54D947
	on user_group_rel (group_id);
