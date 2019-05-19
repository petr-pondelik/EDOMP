<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.2.19
 * Time: 12:28
 */

namespace App\Helpers;

/**
 * Class ConstHelper
 * @package App\Services
 */
class ConstHelper
{

    //Roles
    const ADMIN_ROLE = 1;
    const TEACHER_ROLE = 2;

    //Admin supergroup and group ID
    const ADMIN_GROUP = 1;

    //Result conditions constants
    const RESULT = 1;
    const RESULT_POSITIVE = 1;
    const RESULT_ZERO = 2;
    const RESULT_NEGATIVE = 3;

    //Discriminant conditions constants
    const DISCRIMINANT = 2;
    const DISCRIMINANT_POSITIVE = 1;
    const DISCRIMINANT_ZERO =  2;
    const DISCRIMINANT_NEGATIVE = 3;
    const DISCRIMINANT_INTEGER = 4;
    const DISCRIMINANT_POSITIVE_SQUARE = 5;

    //Types constants
    const LINEAR_EQ = 1;
    const QUADRATIC_EQ = 2;
    const ARITHMETIC_SEQ = 3;
    const GEOMETRIC_SEQ = 5;

    //Types group of equations
    const EQUATIONS = [self::LINEAR_EQ, self::QUADRATIC_EQ];

    //Types group of sequences
    const SEQUENCES = [self::ARITHMETIC_SEQ, self::GEOMETRIC_SEQ];

    //Structure types constants
    const BODY_FINAL = 0;

    //Maximal number of parameters in prototype
    const PARAMETERS_MAX = 6;

    //Maximal complexity of all the parameters range
    const COMPLEXITY_MAX = 200000;

}