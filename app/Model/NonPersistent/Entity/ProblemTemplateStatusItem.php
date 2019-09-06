<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 5.9.19
 * Time: 21:18
 */

namespace App\Model\NonPersistent\Entity;

use Nette\Utils\Json;

/**
 * Class ProblemTemplateStatusItem
 * @package App\Model\NonPersistent\Entity
 */
class ProblemTemplateStatusItem implements \Serializable
{
    /**
     * @var string
     */
    protected $rule;

    /**
     * @var bool
     */
    protected $validated;

    /**
     * ProblemTemplateStatusItem constructor.
     * @param string $rule
     * @param bool $validated
     */
    public function __construct(string $rule, bool $validated)
    {
        $this->rule = $rule;
        $this->validated = $validated;
    }

    /**
     * @return string
     * @throws \Nette\Utils\JsonException
     */
    public function serialize(): string
    {
        $arr = [];
        foreach ($this as $key => $value){
            $arr[$key] = $value;
        }
        return Json::encode($arr);
    }

    /**
     * @param string $serialized
     * @return $this
     * @throws \Nette\Utils\JsonException
     */
    public function unserialize($serialized)
    {
        $data = Json::decode($serialized);
        foreach ($data as $key => $value){
            if(property_exists(static::class, $key)){
                $this->{$key} = $value;
            }
        }
        return $this;
    }

    /**
     * @return bool
     */
    public function isValidated(): bool
    {
        return $this->validated;
    }

    /**
     * @param bool $validated
     */
    public function setValidated(bool $validated): void
    {
        $this->validated = $validated;
    }

    /**
     * @return string
     */
    public function getRule(): string
    {
        return $this->rule;
    }

    /**
     * @param string $rule
     */
    public function setRule(string $rule): void
    {
        $this->rule = $rule;
    }
}