<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 17.4.19
 * Time: 22:00
 */

namespace App\TeacherModule\Presenters;

use App\CoreModule\Arguments\UserInformArgs;
use App\CoreModule\Arguments\ValidatorArgument;
use App\TeacherModule\Components\DataGrids\UserGridFactory;
use App\TeacherModule\Components\Forms\UserForm\IUserFormFactory;
use App\CoreModule\Components\HeaderBar\IHeaderBarFactory;
use App\CoreModule\Components\HelpModal\IHelpModalFactory;
use App\CoreModule\Components\SideBar\ISideBarFactory;
use App\CoreModule\Helpers\FlashesTranslator;
use App\CoreModule\Model\Persistent\Entity\BaseEntity;
use App\CoreModule\Model\Persistent\Entity\User;
use App\CoreModule\Model\Persistent\Functionality\UserFunctionality;
use App\CoreModule\Model\Persistent\Repository\UserRepository;
use App\CoreModule\Services\Authorizator;
use App\CoreModule\Services\MailService;
use App\TeacherModule\Services\NewtonApiClient;
use App\CoreModule\Services\Validator;
use Nette\Utils\ArrayHash;
use Nette\Utils\Random;
use Ublaboo\DataGrid\DataGrid;

/**
 * Class UserPresenter
 * @package App\TeacherModule\Presenters
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
     * @param IUserFormFactory $userFormFactory
     * @param IHelpModalFactory $sectionHelpModalFactory
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
        IUserFormFactory $userFormFactory,
        IHelpModalFactory $sectionHelpModalFactory,
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
     * @throws \App\CoreModule\Exceptions\ValidatorException
     */
    public function validateInlineUpdate(ArrayHash $row): array
    {
        $validationFields['email'] = new ValidatorArgument($row->email, 'email');
        $validationFields['username'] = new ValidatorArgument($row->username, 'username');
        return $this->validator->validatePlain($validationFields);
    }
}