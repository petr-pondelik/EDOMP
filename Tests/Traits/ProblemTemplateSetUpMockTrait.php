<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:03
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;

/**
 * Trait ProblemTemplateSetUpMockTrait
 * @package App\Tests\Traits
 */
trait ProblemTemplateSetUpMockTrait
{
    /**
     * @var ProblemTemplate
     */
    protected $problemTemplateMock;

    protected function setUpProblemTemplateMock(): void
    {
        $this->problemTemplateMock = $this->getMockBuilder(ProblemTemplate::class)->disableOriginalConstructor()->getMock();
    }
}