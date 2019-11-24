SET FOREIGN_KEY_CHECKS=0;

DELETE FROM edomp.difficulty;

DELETE FROM edomp.super_group;

DELETE FROM edomp.`group`;

DELETE FROM edomp.role;

DELETE FROM edomp.user;

DELETE FROM edomp.user_group_rel;

DELETE FROM edomp.problem_type;

DELETE FROM edomp.problem_condition_type;

DELETE FROM edomp.problem_condition;

DELETE FROM edomp.problem_tp_problem_condition_tp_rel;

SET FOREIGN_KEY_CHECKS=1;