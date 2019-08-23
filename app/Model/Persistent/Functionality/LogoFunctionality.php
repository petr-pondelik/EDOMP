<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 29.4.19
 * Time: 19:00
 */

namespace App\Model\Persistent\Functionality;

use App\Model\Persistent\Entity\Logo;
use App\Model\Persistent\Manager\ConstraintEntityManager;
use App\Model\Persistent\Repository\LogoRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\Utils\ArrayHash;

/**
 * Class LogoFunctionality
 * @package App\Model\Persistent\Functionality
 */
class LogoFunctionality extends BaseFunctionality
{
    /**
     * LogoFunctionality constructor.
     * @param ConstraintEntityManager $entityManager
     * @param LogoRepository $logoRepository
     */
    public function __construct
    (
        ConstraintEntityManager $entityManager,
        LogoRepository $logoRepository
    )
    {
        parent::__construct($entityManager);
        $this->repository = $logoRepository;
    }

    /**
     * @param ArrayHash $data
     * @return Object|null
     * @throws \Exception
     */
    public function create(ArrayHash $data): ?Object
    {
        $logo = new Logo();
        $logo->setExtensionTmp($data->extension_tmp);
        $logo->setLabel('label');
        $this->em->persist($logo);
        $this->em->flush();
        return $logo;
    }

    /**
     * @param int $id
     * @param ArrayHash $data
     * @return Object|null
     * @throws EntityNotFoundException
     * @throws \App\Exceptions\EntityException
     */
    public function update(int $id, ArrayHash $data): ?Object
    {
        $logo = $this->repository->find($id);
        if(!$logo){
            throw new EntityNotFoundException('Entity for update not found.');
        }
        if(isset($data->extension_tmp)){
            $logo->setExtensionTmp($data->extension_tmp);
        }
        if(isset($data->extension)){
            $logo->setExtension($data->extension);
        }
        if(isset($data->path)){
            $logo->setPath($data->path);
        }
        if(isset($data->label)){
            $logo->setLabel($data->label);
        }
        $this->em->persist($logo);
        $this->em->flush();
        return $logo;
    }

    public function deleteEmpty(): void
    {
        $this->repository->createQueryBuilder('er')
            ->delete()
            ->where('er.path IS NULL')
            ->getQuery()->execute();
    }
}