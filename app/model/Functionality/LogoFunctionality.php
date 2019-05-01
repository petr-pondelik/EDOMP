<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:00
 */

namespace App\Model\Functionality;

use App\Model\Entity\Logo;
use App\Model\Repository\LogoRepository;
use Kdyby\Doctrine\EntityManager;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFunctionality
 * @package App\Model\Functionality
 */
class LogoFunctionality extends BaseFunctionality
{
    /**
     * LogoFunctionality constructor.
     * @param EntityManager $entityManager
     * @param LogoRepository $logoRepository
     */
    public function __construct
    (
        EntityManager $entityManager,
        LogoRepository $logoRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $logoRepository;
    }

    /**
     * @param ArrayHash $data
     * @return int
     * @throws \Exception
     */
    public function create(ArrayHash $data): int
    {
        $logo = new Logo();
        $logo->setExtensionTmp($data->extension_tmp);
        $this->em->persist($logo);
        $this->em->flush();
        return $logo->getId();
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object
     * @throws \Exception
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $logo = $this->repository->find($id);
        if(isset($data->extension_tmp))
            $logo->setExtensionTmp($data->extension_tmp);
        if(isset($data->extension))
            $logo->setExtension($data->extension);
        if(isset($data->path))
            $logo->setPath($data->path);
        if(isset($data->label))
            $logo->setLabel($data->label);
        $this->em->persist($logo);
        $this->em->flush();
        return $logo;
    }

    public function deleteEmpty(): void
    {
        $this->repository->createQueryBuilder("er")
            ->delete()
            ->where("er.path IS NULL")
            ->getQuery()->execute();
    }
}