<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 26.9.19
 * Time: 16:58
 */

namespace App\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\FilterRepository")
 *
 * Class Filter
 * @package App\Model\Persistent\Entity
 */
class Filter extends BaseEntity
{
    /**
     * @ORM\Column(type="json_array", nullable=true)
     *
     * @var array|null
     */
    protected $data;

    /**
     * @ORM\Column(type="integer", nullable=false)
     * @Assert\Type(
     *     type="integer",
     *     message="Seq must be {{ type }}."
     * )
     * @Assert\NotBlank(
     *     message="Seq can't be blank."
     * )
     *
     * @var int
     */
    protected $seq;

    /**
     * @ORM\ManyToOne(targetEntity="App\Model\Persistent\Entity\Test", inversedBy="filters", cascade={"persist", "merge"})
     * @Assert\NotBlank(
     *     message="Test can't be blank."
     * )
     *
     * @var Test
     */
    protected $test;

    /**
     * @return array|null
     */
    public function getData(): ?array
    {
        return $this->data;
    }

    /**
     * @param iterable $data
     */
    public function setData(iterable $data): void
    {
        $this->data = $data;
    }

    /**
     * @return Test
     */
    public function getTest(): Test
    {
        return $this->test;
    }

    /**
     * @param Test $test
     */
    public function setTest(Test $test): void
    {
        $this->test = $test;
    }

    /**
     * @return int
     */
    public function getSeq(): int
    {
        return $this->seq;
    }

    /**
     * @param int $seq
     */
    public function setSeq(int $seq): void
    {
        $this->seq = $seq;
    }
}