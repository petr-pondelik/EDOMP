<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:17
 */

namespace App\Model\Persistent\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemTemplateRepository")
 *
 * Class ProblemTemplate
 * @package App\Model\Persistent\Entity
 */
abstract class ProblemTemplate extends Problem
{
    /**
     * @ORM\Column(type="json", nullable=true)
     * @var string
     */
    protected $matches;

    /**
     * ProblemTemplate constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTemplate = true;
    }

    /**
     * @return mixed
     */
    public function getMatches()
    {
        return $this->matches;
    }

    /**
     * @param null $matches
     */
    public function setMatches($matches = null): void
    {
        $this->matches = $matches;
    }
}