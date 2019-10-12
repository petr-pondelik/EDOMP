<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\AdminModule\Presenters;

use App\Arguments\UserInformArgs;
use App\Arguments\ValidatorArgument;
use App\Components\DataGrids\UserGridFactory;
use App\Components\Forms\UserForm\IUserIFormFactory;
use App\Components\HeaderBar\IHeaderBarFactory;
use App\Components\SectionHelpModal\ISectionHelpModalFactory;
use App\Components\SideBar\ISideBarFactory;
use App\Helpers\FlashesTranslator;
use App\Model\Persistent\Entity\BaseEntity;
use App\Model\Persistent\Entity\User;
use App\Model\Persistent\Functionality\UserFunctionality;
use App\Model\Persistent\Repository\UserRepository;
use App\Services\Authorizator;
use App\Services\MailService;
use App\Services\NewtonApiClient;
use App\Services\Validator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserPresenter
 * @package App\AdminModule\Presenters
 */
class UserPresenter extends EntityPresenter
{
    /**
     * @var MailService
     */
    protected $mailService;

    /**
     * UserPresenter constructor.
     * @param Authorizator $authorizator
     * @param Validator $validator
     * @param NewtonApiClient $newtonApiClient
     * @param IHeaderBarFactory $headerBarFactory
     * @param ISideBarFactory $sideBarFactory
     * @param FlashesTranslator $flashesTranslator
     * @param UserRepository $userRepository
     * @param UserFunctionality $userFunctionality
     * @param UserGridFactory $userGridFactory
     * @param IUserIFormFactory $userFormFactory
     * @param ISectionHelpModalFactory $sectionHelpModalFactory
     * @param MailService $mailService
     */
    public function __construct
    (
        Authorizator $authorizator,
        Validator $validator,
        NewtonApiClient $newtonApiClient,
        IHeaderBarFactory $headerBarFactory,
        ISideBarFactory $sideBarFactory,
        FlashesTranslator $flashesTranslator,
        UserRepository $userRepository,
        UserFunctionality $userFunctionality,
        UserGridFactory $userGridFactory,
        IUserIFormFactory $userFormFactory,
        ISectionHelpModalFactory $sectionHelpModalFactory,
        MailService $mailService
    )
    {
        parent::__construct
        (
            $authorizator, $validator, $newtonApiClient, $headerBarFactory, $sideBarFactory, $flashesTranslator, $sectionHelpModalFactory,
            $userRepository, $userFunctionality, $userGridFactory, $userFormFactory
        );
        $this->mailService = $mailService;
    }

    /**
     * @param BaseEntity $entity
     * @return bool
     */
    public function isEntityAllowed(BaseEntity $entity): bool
    {
        return $this->user->isInRole('admin') || $this->authorizator->isEntityAllowed($this->user->identity, $entity);
    }

    /**
     * @param $name
     * @return DataGrid
     */
    public function createComponentEntityGrid($name): DataGrid
    {
        $grid = $this->gridFactory->create($this, $name);

        $grid->addAction('resendPassword', '', 'resendPassword!')
            ->setIcon('key')
            ->setTitle('Přeposlat heslo')
            ->setClass('btn btn-primary btn-sm ajax');

        $grid->addAction('delete', '', 'delete!')
            ->setIcon('trash')
            ->setTitle('Odstranit uživatele')
            ->setClass('btn btn-danger btn-sm ajax');

        $grid->addAction('edit', '', 'update!')
            ->setIcon('edit')
            ->setTitle('Editovat uživatele')
            ->setClass('btn btn-primary btn-sm');

        $grid->addInlineEdit()
            ->setIcon('pencil-alt')
            ->setTitle('Upravit inline')
            ->setClass('btn btn-primary btn-sm ajax')
            ->onControlAdd[] = static function ($container) {
            $container->addText('email', '');
            $container->addText('username', '');
            $container->addText('firstName', '');
            $container->addText('lastName', '');
        };

        $grid->getInlineEdit()->onSetDefaults[] = static function ($container, User $item) {
            $container->setDefaults([
                'email' => $item->getEmail(),
                'username' => $item->getUsername(),
                'firstName' => $item->getFirstName(),
                'lastName' => $item->getLastName()
            ]);
        };

        $grid->getInlineEdit()->onSubmit[] = [$this, 'handleInlineUpdate'];

        return $grid;
    }

    /**
     * @param int $id
     */
    public function handleResendPassword(int $id): void
    {
        $password = Random::generate(8);
        try{
            $user = $this->functionality->updatePassword($id, $password);
            $this->mailService->sendInvitationEmail($user, $password);
        } catch (\Exception $e) {
            $this->informUser(new UserInformArgs('resendPassword', true, 'error', $e, true));
        }
        $this->informUser(new UserInformArgs('resendPassword', true, 'success', null, true));
    }

    /**
     * @param ArrayHash $row
     * @return array
     * @throws \App\Exceptions\ValidatorException
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        $validationFields['email'] = new ValidatorArgument($row->email, 'email');
        $validationFields['username'] = new ValidatorArgument($row->username, 'username');
        return $this->validator->validatePlain($validationFields);
    }
}