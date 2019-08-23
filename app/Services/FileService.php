<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.3.19
 * Time: 21:54
 */

namespace App\Services;

use App\Model\Persistent\Entity\Test;
use App\Model\Persistent\Functionality\LogoFunctionality;
use App\Model\Persistent\Repository\LogoRepository;
use App\Model\Persistent\Repository\TestRepository;
use Nette\FileNotFoundException;
use Nette\Http\IRequest;
use Nette\IOException;
use Nette\Utils\ArrayHash;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


/**
 * Class FileService
 * @package App\Service
 */
class FileService
{
    /**
     * @var string
     */
    protected $logosDir;

    /**
     * @var string
     */
    protected $logosTmpDir;

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
     * FileService constructor.
     * @param string $logosDir
     * @param string $logosTmpDir
     * @param TestRepository $testRepository
     * @param LogoRepository $logoRepository
     * @param LogoFunctionality $logoFunctionality
     */
    public function __construct
    (
        string $logosDir, string $logosTmpDir,
        TestRepository $testRepository, LogoRepository $logoRepository, LogoFunctionality $logoFunctionality
    )
    {
        $this->logosDir = $logosDir;
        $this->logosTmpDir = $logosTmpDir;
        $this->testRepository = $testRepository;
        $this->logoRepository = $logoRepository;
        $this->logoFunctionality = $logoFunctionality;
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

        if(!copy($httpRequest->getFile('logo'), $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension)) {
            throw new IOException('Chyba při ukládání souboru.');
        }

        //Insert logo into DB with temporary extension column
        $this->logoFunctionality->create(ArrayHash::from([
            'extension_tmp' => $extension
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

        //Delete logo temporary directory
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        //Delete logo from DB based on it's ID
        $this->logoFunctionality->delete($id);

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

        if(!copy($httpRequest->getFile('logo'), $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension)){
            throw new IOException('Chyba při ukládání souboru.');
        }

        //Update logo DB record's temporary extension column
        $this->logoFunctionality->update($id, ArrayHash::from([
           'extension_tmp' => $extension
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
     * @throws \Exception
     */
    public function finalStore(int $id): void
    {
        FileSystem::copy($this->logosTmpDir . DIRECTORY_SEPARATOR . $id, $this->logosDir . DIRECTORY_SEPARATOR . $id);
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        $fileRecord = $this->logoRepository->find($id);

        //Set logo it's final path
        $this->logoFunctionality->update(
            $id,
            ArrayHash::from([
                'extension' => $fileRecord->getExtensionTmp(),
                'path' => DIRECTORY_SEPARATOR . 'data_public' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $fileRecord->getExtensionTmp(),
            ])
        );
    }

    /**
     * @param Test $test
     */
    public function createTestZip(Test $test): void
    {
        $zip = new \ZipArchive();

        //Check files existence
        $testVariants = $test->getTestVariants()->getValues();
        foreach ($testVariants as $testVariant){
            if(!file_exists(DATA_DIR  . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $test->getId() . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($testVariant) . '.tex' )){
                throw new FileNotFoundException('Soubor s testem nenalezen.');
            }
        }

        $logoId = $test->getLogo()->getId();
        $logoExt = $test->getLogo()->getExtension();

        //Check logo file existence
        if(!file_exists(LOGOS_DIR . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt)){
            throw new FileNotFoundException('Soubor s logem nenalezen');
        }

        if(!$zip->open(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $test->getId() . DIRECTORY_SEPARATOR . 'test_' . $test->getId() . '.zip', \ZipArchive::CREATE)){
            throw new IOException('Zip archiv nemohl být vytvořen.');
        }

        foreach ($testVariants as $testVariant){
            $zip->addFile(
                DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $test->getId() . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($testVariant) . '.tex',
                'variant_' . Strings::lower($testVariant) . '.tex'
            );
        }

        $zip->addFile(LOGOS_DIR . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt, 'file' . $logoExt);

        $zip->close();
    }

    /**
     * @param int $testId
     */
    public function moveTestDirToPublic(int $testId): void
    {
        FileSystem::copy(
            DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $testId . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip',
            DATA_PUBLIC_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip'
            );
    }

}