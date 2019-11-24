<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 24.11.19
 * Time: 11:30
 */

namespace App\TeacherModule\Services;

/**
 * Class ProblemConditionsMatrix
 * @package App\TeacherModule\Helpers
 */
class ProblemConditionsMatrix
{
    /**
     * @var array
     */
    protected $matrix;

    /**
     * ProblemConditionsMatrix constructor.
     */
    public function __construct()
    {
        /**
         * Matrix contains problemConditions mapped to it's types
         * This approach was chosen due to the app performance impacts of loading this mapping from DB
         */
        $this->matrix = [
            1 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => null
                ],
                [
                    'accessor' => 1,
                    'validationFunctionKey' => 'positive'
                ],
                [
                    'accessor' => 2,
                    'validationFunctionKey' => 'zero'
                ],
                [
                    'accessor' => 3,
                    'validationFunctionKey' => 'negative'
                ]
            ],
            2 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => null
                ],
                [
                    'accessor' => 1,
                    'validationFunctionKey' => 'positive'
                ],
                [
                    'accessor' => 2,
                    'validationFunctionKey' => 'zero'
                ],
                [
                    'accessor' => 3,
                    'validationFunctionKey' => 'negative'
                ],
                [
                    'accessor' => 4,
                    'validationFunctionKey' => 'integer'
                ],
                [
                    'accessor' => 5,
                    'validationFunctionKey' => 'positiveSquare'
                ]
            ],
            3 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => 'arithmeticSequenceType'
                ]
            ],
            4 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => 'geometricSequenceType'
                ]
            ],
            5 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => 'linearEquationType'
                ]
            ],
            6 => [
                [
                    'accessor' => 0,
                    'validationFunctionKey' => 'quadraticEquationType'
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function getMatrix(): array
    {
        return $this->matrix;
    }
}