<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 12.9.19
 * Time: 13:32
 */

namespace App\CoreModule\Model\Persistent\Functionality\ProblemTemplate;

use App\CoreModule\Model\Persistent\Functionality\BaseFunctionality;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Model\Persistent\Manager\ConstraintEntityManager;
use App\CoreModule\Model\Persistent\Repository\UserRepository;

/**
 * Class ProblemTemplateFunctionality
 * @package App\CoreModule\Model\Persistent\Functionality\ProblemTemplate
 */
abstract class ProblemTemplateFunctionality extends BaseFunctionality
{
    /**
     * @var UserFunctionality
     */
    protected $userRepository;

    /**
     * ProblemTemplateFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param UserRepository $userRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        UserRepository $userRepository
    )
    {
        parent::__construct($entityManager);
        $this->userRepository = $userRepository;
    }
}