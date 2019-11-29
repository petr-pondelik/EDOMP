SET FOREIGN_KEY_CHECKS=0;

TRUNCATE TABLE arithmetic_sequence_template;
TRUNCATE TABLE difficulty;
TRUNCATE TABLE doctrine_migrations;
TRUNCATE TABLE filter;
TRUNCATE TABLE filter_difficulty_rel;
TRUNCATE TABLE filter_problem_condition_rel;
TRUNCATE TABLE filter_problem_type_rel;
TRUNCATE TABLE filter_sub_theme_rel;
TRUNCATE TABLE geometric_sequence_template;
TRUNCATE TABLE `group`;
TRUNCATE TABLE group_theme_rel;
TRUNCATE TABLE linear_equation_template;
TRUNCATE TABLE logo;
TRUNCATE TABLE problem;
TRUNCATE TABLE problem_condition;
TRUNCATE TABLE problem_condition_problem_rel;
TRUNCATE TABLE problem_condition_type;
TRUNCATE TABLE problem_final;
TRUNCATE TABLE problem_final_test_variant_association;
TRUNCATE TABLE problem_template;
TRUNCATE TABLE problem_tp_problem_condition_tp_rel;
TRUNCATE TABLE problem_type;
TRUNCATE TABLE quadratic_equation_template;
TRUNCATE TABLE role;
TRUNCATE TABLE sub_theme;
TRUNCATE TABLE super_group;
TRUNCATE TABLE super_group_theme_rel;
TRUNCATE TABLE template_json_data;
TRUNCATE TABLE test;
TRUNCATE TABLE test_group_rel;
TRUNCATE TABLE test_variant;
TRUNCATE TABLE theme;
TRUNCATE TABLE user;
TRUNCATE TABLE user_group_rel;


SET FOREIGN_KEY_CHECKS=1;