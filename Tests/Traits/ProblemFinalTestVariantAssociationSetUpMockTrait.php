<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 28.11.19
 * Time: 16:24
 */

namespace App\Tests\Traits;

use App\CoreModule\Model\Persistent\Entity\ProblemFinalTestVariantAssociation;

/**
 * Trait ProblemFinalTestVariantAssociationSetUpMockTrait
 * @package App\Tests\Traits
 */
trait ProblemFinalTestVariantAssociationSetUpMockTrait
{
    /**
     * @var ProblemFinalTestVariantAssociation
     */
    protected $problemFinalTestVariantAssociationMock;

    protected function setUpProblemFinalTestVariantAssociation(): void
    {
        $this->problemFinalTestVariantAssociationMock = $this->getMockBuilder(ProblemFinalTestVariantAssociation::class)->disableOriginalConstructor()->getMock();
    }
}