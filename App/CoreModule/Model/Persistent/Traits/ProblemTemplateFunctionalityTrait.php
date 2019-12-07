<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 30.4.19
 * Time: 12:53
 */

namespace App\CoreModule\Model\Persistent\Traits;

use App\CoreModule\Model\Persistent\Entity\Difficulty;
use App\CoreModule\Model\Persistent\Entity\ProblemTemplate\ProblemTemplate;
use App\CoreModule\Model\Persistent\Entity\ProblemType;
use App\CoreModule\Model\Persistent\Entity\SubTheme;
use App\CoreModule\Model\Persistent\Entity\TemplateJsonData;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Functionality\TemplateJsonDataFunctionality;
use App\CoreModule\Model\Persistent\Repository\DifficultyRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemConditionTypeRepository;
use App\CoreModule\Model\Persistent\Repository\ProblemTypeRepository;
use App\CoreModule\Model\Persistent\Repository\SubThemeRepository;
use App\CoreModule\Model\Persistent\Repository\TemplateJsonDataRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;
use Nette\Utils\DateTime;
use Nette\Utils\Json;

/**
 * Trait ProblemTemplateFunctionalityTrait
 * @package App\CoreModule\Model\Persistent\Traits
 */
trait ProblemTemplateFunctionalityTrait
{
    /**
     * @var ProblemTypeRepository
     */
    protected $problemTypeRepository;

    /**
     * @var ProblemConditionTypeRepository
     */
    protected $problemConditionTypeRepository;

    /**
     * @var ProblemConditionRepository
     */
    protected $problemConditionRepository;

    /**
     * @var DifficultyRepository
     */
    protected $difficultyRepository;

    /**
     * @var SubThemeRepository
     */
    protected $subThemeRepository;

    /**
     * @var TemplateJsonDataRepository
     */
    protected $templateJsonDataRepository;

    /**
     * @var TemplateJsonDataFunctionality
     */
    protected $templateJsonDataFunctionality;

    /**
     * @param array $firstArr
     * @param array $secondArr
     * @return array
     */
    public function intersectJsonDataArrays(array $firstArr, array $secondArr): array
    {
        return array_uintersect($firstArr, $secondArr, static function ($first, $second) {
            return strcmp(serialize($first), serialize($second));
        });
    }

    /**
     * @param ProblemTemplate $entity
     * @param iterable $data
     * @param int|null $entityId
     * @param bool $fromDataGrid
     * @return ProblemTemplate
     * @throws \Nette\Utils\JsonException
     * @throws EntityNotFoundException
     */
    public function setBasics(ProblemTemplate $entity, iterable $data, int $entityId = null, bool $fromDataGrid = false): ProblemTemplate
    {
        if (isset($data['textBefore'])) {
            $entity->setTextBefore($data['textBefore']);
        }

        if (isset($data['body'])) {
            $entity->setBody($data['body']);
        }

        if (isset($data['textAfter'])) {
            $entity->setTextAfter($data['textAfter']);
        }

        if (isset($data['type'])) {
            /** @var ProblemType|null $problemType */
            $problemType = $this->problemTypeRepository->find($data['type']);
            if (!$problemType) {
                throw new EntityNotFoundException('ProblemType not found.');
            }
            $entity->setProblemType($problemType);
        }

        if (isset($data['difficulty'])) {
            /** @var Difficulty|null $difficulty */
            $difficulty = $this->difficultyRepository->find($data['difficulty']);
            if (!$difficulty) {
                throw new EntityNotFoundException('Difficulty not found.');
            }
            $entity->setDifficulty($difficulty);
        }

        if (isset($data['subTheme'])) {
            /** @var SubTheme|null $subTheme */
            $subTheme = $this->subThemeRepository->find($data['subTheme']);
            if (!$subTheme) {
                throw new EntityNotFoundException('SubTheme not found.');
            }
            $entity->setSubTheme($subTheme);
        }

        if (isset($data['matches'])) {
            $entity->setMatches($data['matches']);
        }

        if (isset($data['studentVisible'])) {
            $entity->setStudentVisible($data['studentVisible']);
        }

        if (isset($data['userId'])) {
            /** @var User|null $user */
            $user = $this->userRepository->find($data['userId']);
            if (!$user) {
                throw new EntityNotFoundException('User not found.');
            }
            $entity->setCreatedBy($user);
        }

        if (isset($data['created'])) {
            $entity->setCreated(DateTime::from($data['created']));
        }

        if (!$fromDataGrid) {
            $attached = $this->attachConditions($entity, $data);
            $entity = $attached->template;

            if (!$entityId) {
                $entityId = $this->repository->getSequenceVal();
            }

            $entityJsonData = [];

            /** @var TemplateJsonData[] $entityJsons */
            if ($entityJsons = $this->templateJsonDataRepository->findBy(['templateId' => $entityId])) {
                $entityJsonData = Json::decode($entityJsons[0]->getJsonData());

                // Unset picked template JSON
                unset($entityJsons[0]);

                // Make merge of all template recorded JSONs
                foreach ($entityJsons as $json) {
                    $problemConditionTypeId = $json->getProblemConditionType()->getId();
                    if (isset($data['condition_' . $problemConditionTypeId]) && $data['condition_' . $problemConditionTypeId] !== 0) {
                        $arr = Json::decode($json->getJsonData());
                        $entityJsonData = $this->intersectJsonDataArrays($entityJsonData, $arr);
                    }
                }
            }

            if ($entityJsonData) {
                // Reindex array key to start from 0 (array_values) and encode data to JSON string
                $entityJsonData = Json::encode(array_values($entityJsonData));
                $entity->setMatches($entityJsonData);
            }

            // Comment this for testing purposes
            $this->templateJsonDataFunctionality->deleteByTemplate($entityId);

        }

        return $entity;
    }

    /**
     * @param int $id
     * @param iterable $data
     * @param bool $fromDataGrid
     * @return ProblemTemplate
     * @throws EntityNotFoundException
     * @throws \Nette\Utils\JsonException
     */
    public function baseUpdate(int $id, iterable $data, bool $fromDataGrid = false): ProblemTemplate
    {
        $template = $this->repository->find($id);
        if (!$fromDataGrid) {
            $template->setConditions(new ArrayCollection());
        }
        return $this->setBasics($template, $data, $id, $fromDataGrid);
    }

    /**
     * @param ProblemTemplate $template
     * @param iterable $data
     * @return ArrayHash
     */
    protected function attachConditions(ProblemTemplate $template, iterable $data): ArrayHash
    {
        $hasCondition = false;
        $problemCondTypes = $this->problemConditionTypeRepository->findNonValidation($data['type']);

        foreach ($problemCondTypes as $problemCondType) {
            // Get ConditionType ID
            $condTypeId = $problemCondType->getId();

            // Get ConditionType value from created problem
            $condTypeVal = $data['condition_' . $condTypeId];

            // Template has condition
            if ($condTypeVal) {
                $hasCondition = true;
            }

            $condition = $this->problemConditionRepository->findOneBy([
                'problemConditionType.id' => $condTypeId,
                'accessor' => $condTypeVal
            ]);

            $template->addCondition($condition);
        }

        return ArrayHash::from([
            'template' => $template,
            'hasCondition' => $hasCondition
        ]);
    }
}