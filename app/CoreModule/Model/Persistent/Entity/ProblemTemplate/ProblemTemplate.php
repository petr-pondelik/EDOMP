<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 27.4.19
 * Time: 22:17
 */

namespace App\CoreModule\Model\Persistent\Entity\ProblemTemplate;

use App\CoreModule\Model\Persistent\Entity\Problem;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Model\Persistent\Repository\ProblemTemplate\ProblemTemplateRepository")
 *
 * Class ProblemTemplate
 * @package App\CoreModule\Model\Persistent\Entity
 */
class ProblemTemplate extends Problem
{
    /**
     * @ORM\Column(type="json", nullable=true)
     * @var string
     */
    protected $matches;

    /**
     * ProblemTemplate constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->isTemplate = true;
    }

    /**
     * @return string|null
     */
    public function getMatches(): ?string
    {
        return $this->matches;
    }

    /**
     * @param string|null $matches
     */
    public function setMatches(?string $matches): void
    {
        $this->matches = $matches;
    }

}