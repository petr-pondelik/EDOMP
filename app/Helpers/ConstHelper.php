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
    // Roles
    public const ADMIN_ROLE = 1;
    public const TEACHER_ROLE = 2;

    // Admin supergroup and group ID
    public const ADMIN_GROUP = 1;

    // Result conditions constants
    public const RESULT = 1;
    public const RESULT_POSITIVE = 1;
    public const RESULT_ZERO = 2;
    public const RESULT_NEGATIVE = 3;

    // Discriminant conditions constants
    public const DISCRIMINANT = 2;
    public const DISCRIMINANT_POSITIVE = 1;
    public const DISCRIMINANT_ZERO =  2;
    public const DISCRIMINANT_NEGATIVE = 3;
    public const DISCRIMINANT_INTEGER = 4;
    public const DISCRIMINANT_POSITIVE_SQUARE = 5;

    // Difference validation conditions constants
    public const DIFFERENCE_VALIDATION = 3;
    public const DIFFERENCE_EXISTS = 0;

    // Quotient validation conditions constants
    public const QUOTIENT_VALIDATION = 4;
    public const QUOTIENT_EXISTS = 0;

    // Expression conditions
    public const EXPRESSION_VALIDATION = 5;
    public const EXPRESSION_VALID = 0;

    // Types constants
    public const LINEAR_EQ = 1;
    public const QUADRATIC_EQ = 2;
    public const ARITHMETIC_SEQ = 3;
    public const GEOMETRIC_SEQ = 4;

    // Types group of equations
    public const EQUATIONS = [self::LINEAR_EQ, self::QUADRATIC_EQ];

    // Types group of sequences
    public const SEQUENCES = [self::ARITHMETIC_SEQ, self::GEOMETRIC_SEQ];

    // Maximal number of parameters in prototype
    public const PARAMETERS_MAX = 6;

    // Maximal complexity of all the parameters range
    public const COMPLEXITY_MAX = 200000;
}