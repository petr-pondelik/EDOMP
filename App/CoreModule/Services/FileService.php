<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.3.19
 * Time: 21:54
 */

namespace App\CoreModule\Services;

use App\CoreModule\Model\Persistent\Entity\Logo;
use App\CoreModule\Model\Persistent\Entity\Test;
use App\CoreModule\Model\Persistent\Entity\TestVariant;
use App\CoreModule\Model\Persistent\Functionality\LogoFunctionality;
use App\CoreModule\Model\Persistent\Repository\LogoRepository;
use App\CoreModule\Model\Persistent\Repository\TestRepository;
use Doctrine\ORM\EntityNotFoundException;
use Nette\FileNotFoundException;
use Nette\Http\IRequest;
use Nette\IOException;
use Nette\Security\User;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


/**
 * Class FileService
 * @package App\CoreModule\Services
 */
final class FileService
{
    /**
     * @var string
     */
    protected $dataPublicDir;

    /**
     * @var string
     */
    protected $logosDir;

    /**
     * @var string
     */
    protected $logosTmpDir;

    /**
     * @var string
     */
    protected $coreTemplatesDir;

    /**
     * @var string
     */
    protected $studentTemplatesDir;

    /**
     * @var string
     */
    protected $teacherTemplatesDir;

    /**
     * @var string
     */
    protected $testDataDir;

    /**
     * @var string
     */
    protected $testTemplatesDataDir;

    /**
     * @var TestRepository
     */
    protected $testRepository;

    /**
     * @var LogoRepository
     */
    protected $logoRepository;

    /**
     * @var LogoFunctionality
     */
    protected $logoFunctionality;

    /**
     * @var User
     */
    protected $user;

    /**
     * FileService constructor.
     * @param string $dataPublicDir
     * @param string $logosDir
     * @param string $logosTmpDir
     * @param string $coreTemplatesDir
     * @param string $studentTemplatesDir
     * @param string $teacherTemplatesDir
     * @param string $testDataDir
     * @param string $testTemplatesDataDir
     * @param TestRepository $testRepository
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     * @param User $user
     */
    public function __construct
    (
        string $dataPublicDir,
        string $logosDir,
        string $logosTmpDir,
        string $coreTemplatesDir,
        string $studentTemplatesDir,
        string $teacherTemplatesDir,
        string $testDataDir,
        string $testTemplatesDataDir,
        TestRepository $testRepository,
        LogoRepository $logoRepository,
        LogoFunctionality $logoFunctionality,
        User $user
    )
    {
        $this->dataPublicDir = $dataPublicDir;
        $this->logosDir = $logosDir;
        $this->logosTmpDir = $logosTmpDir;
        $this->coreTemplatesDir = $coreTemplatesDir;
        $this->studentTemplatesDir = $studentTemplatesDir;
        $this->teacherTemplatesDir = $teacherTemplatesDir;
        $this->testDataDir = $testDataDir;
        $this->testTemplatesDataDir = $testTemplatesDataDir;
        $this->testRepository = $testRepository;
        $this->logoRepository = $logoRepository;
        $this->logoFunctionality = $logoFunctionality;
        $this->user = $user;
    }

    /**
     * @param string $file
     * @return string
     */
    public function read(string $file): string
    {
        return FileSystem::read($file);
    }

    /**
     * @return string
     */
    public function getUserCustomTestTemplatePath(): string
    {
        return $this->testTemplatesDataDir . $this->user->getId() . DIRECTORY_SEPARATOR . 'testTemplate.latte';
    }

    /**
     * @return string
     */
    public function getUserTestTemplatePath(): string
    {
        $userCustomTemplatePath = $this->getUserCustomTestTemplatePath();
        try {
            FileSystem::read($userCustomTemplatePath);
        } catch (IOException $e) {
            return $this->teacherTemplatesDir . DIRECTORY_SEPARATOR . 'pdf' . DIRECTORY_SEPARATOR . 'testPdf' . DIRECTORY_SEPARATOR . 'default.latte';
        }
        return $userCustomTemplatePath;
    }

    /**
     * @return string
     */
    public function readUserTemplate(): string
    {
        return $this->read($this->getUserTestTemplatePath());
    }

    /**
     * @param string $templateStr
     */
    public function updateTestTemplate(string $templateStr): void
    {
        FileSystem::write($this->testTemplatesDataDir . $this->user->getId() . DIRECTORY_SEPARATOR . 'testTemplate.latte', $templateStr);
    }

    public function resetTestTemplate(): void
    {
        FileSystem::delete($this->testTemplatesDataDir . $this->user->getId());
    }

    public function clearLogosTmpDir(): void
    {
        FileSystem::delete($this->logosTmpDir);
    }

    /**
     * @param int $logoId
     */
    public function deleteLogoFile(int $logoId): void
    {
        FileSystem::delete($this->logosDir . DIRECTORY_SEPARATOR . $logoId);
    }

    /**
     * @param string $fileName
     * @return string
     */
    public function getFileExtension(string $fileName): string
    {
        return Strings::lower(
            Strings::substring($fileName, Strings::indexOf($fileName, '.', -1))
        );
    }

    /**
     * @param IRequest $httpRequest
     * @return string
     * @throws \Exception
     */
    public function uploadFile(IRequest $httpRequest): string
    {
        //Get next value of sequence for logo table
        $id = $this->logoRepository->getSequenceVal();

        //Get uploaded file extension
        $extension = $this->getFileExtension($httpRequest->getFile('logo')->name);

        FileSystem::createDir($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        if (!copy($httpRequest->getFile('logo'), $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension)) {
            throw new IOException('Chyba p??i ukl??d??n?? souboru.');
        }

        //Insert logo into DB with temporary extension column
        $this->logoFunctionality->create(ArrayHash::from([
            'extensionTmp' => $extension
        ]));

        return $id;
    }

    /**
     * @param IRequest $httpRequest
     * @return string
     * @throws \Exception
     */
    public function revertFileUpload(IRequest $httpRequest): string
    {
        $id = $httpRequest->getRawBody();

        // Delete logo temporary directory
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        // Delete logo from DB based on it's ID
        try {
            $this->logoFunctionality->delete($id, true, true);
        } catch (EntityNotFoundException $e) {
            return '';
        }

        return '';
    }

    /**
     * @param IRequest $httpRequest
     * @return string
     * @throws \Exception
     */
    public function updateFile(IRequest $httpRequest): string
    {
        //Get updated logo id
        $id = $httpRequest->getUrl()->getQueryParameter('logo_id');

        $extension = $this->getFileExtension($httpRequest->getFile('logo')->name);

        FileSystem::createDir($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        if (!copy($httpRequest->getFile('logo'), $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension)) {
            throw new IOException('Chyba p??i ukl??d??n?? souboru.');
        }

        //Update logo DB record's temporary extension column
        $this->logoFunctionality->update($id, ArrayHash::from([
            'extensionTmp' => $extension
        ]));

        return $id;

    }

    /**
     * @param IRequest $httpRequest
     * @return string
     */
    public function revertFileUpdate(IRequest $httpRequest): string
    {
        $id = $httpRequest->getRawBody();
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);
        return '';
    }

    /**
     * @param int $id
     * @return Logo
     * @throws \Exception
     */
    public function finalStore(int $id): Logo
    {
        FileSystem::copy($this->logosTmpDir . DIRECTORY_SEPARATOR . $id, $this->logosDir . DIRECTORY_SEPARATOR . $id);
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        $fileRecord = $this->logoRepository->find($id);

        // Set logo it's final path
        return $this->logoFunctionality->update(
            $id,
            ArrayHash::from([
                'extension' => $fileRecord->getExtensionTmp(),
                'path' => DIRECTORY_SEPARATOR . 'data_public' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $fileRecord->getExtensionTmp(),
            ])
        );
    }

    /**
     * @param TestVariant $testVariant
     * @param string $template
     */
    public function createTestVariantFile(TestVariant $testVariant, string $template): void
    {
        FileSystem::write($this->testDataDir . $testVariant->getTest()->getId() . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($testVariant->getLabel()) . '.tex', $template);
    }

    /**
     * @param Test $test
     */
    public function createTestZip(Test $test): void
    {
        $zip = new \ZipArchive();

        //Check files existence
        $testVariants = $test->getTestVariants()->getValues();
        foreach ($testVariants as $testVariant) {
            if (!file_exists($this->testDataDir . $test->getId() . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($testVariant) . '.tex')) {
                throw new FileNotFoundException('Soubor s testem nenalezen.');
            }
        }

        $logoId = $test->getLogo()->getId();
        $logoExt = $test->getLogo()->getExtension();

        //Check logo file existence
        if (!file_exists($this->logosDir . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt)) {
            throw new FileNotFoundException('Soubor s logem nenalezen.');
        }

        if (!$zip->open($this->testDataDir . $test->getId() . DIRECTORY_SEPARATOR . 'test_' . $test->getId() . '.zip', \ZipArchive::CREATE)) {
            throw new IOException('Zip archiv nemohl b??t vytvo??en.');
        }

        foreach ($testVariants as $testVariant) {
            $zip->addFile(
                $this->testDataDir . $test->getId() . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($testVariant) . '.tex',
                'variant_' . Strings::lower($testVariant) . '.tex'
            );
        }

        $zip->addFile($this->logosDir . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt, 'file' . $logoExt);

        $zip->close();
    }

    /**
     * @param int $testId
     */
    public function moveTestDirToPublic(int $testId): void
    {
        FileSystem::copy(
            $this->testDataDir . $testId . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip',
            $this->dataPublicDir . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip'
        );
    }
}