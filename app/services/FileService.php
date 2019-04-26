<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.3.19
 * Time: 21:54
 */

namespace App\Services;

use App\Model\Managers\LogoManager;
use App\Model\Managers\TestManager;
use Nette\FileNotFoundException;
use Nette\Http\IRequest;
use Nette\IOException;
use Nette\Utils\FileSystem;
use Nette\Utils\Strings;


/**
 * Class FileService
 * @package app\services
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
     * @var LogoManager
     */
    protected $logoManager;

    /**
     * @var TestManager
     */
    protected $testManager;

    /**
     * FileService constructor.
     * @param string $logosDir
     * @param string $logosTmpDir
     * @param LogoManager $logoManager
     * @param TestManager $testManager
     */
    public function __construct
    (
        string $logosDir, string $logosTmpDir,
        LogoManager $logoManager, TestManager $testManager
    )
    {
        $this->logosDir = $logosDir;
        $this->logosTmpDir = $logosTmpDir;
        $this->logoManager = $logoManager;
        $this->testManager = $testManager;
    }

    public function clearLogosTmpDir()
    {
        FileSystem::delete($this->logosTmpDir);
    }

    /**
     * @param int $logoId
     */
    public function deleteLogoFile(int $logoId)
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
     * @throws \Dibi\Exception
     */
    public function uploadFile(IRequest $httpRequest): string
    {
        //Get next value of sequence for logo table
        $id = $this->logoManager->getSequenceVal();

        //Get uploaded file extension
        $extension = $this->getFileExtension($httpRequest->getFile("logo_file")->name);

        FileSystem::createDir($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        if(
            !file_put_contents(
            $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension,
                file_get_contents($httpRequest->getFile('logo_file'))
            )
        ){
            throw new IOException('Chyba při ukládání souboru.');
        }

        //Insert logo into DB with temporary extension column
        $this->logoManager->create([
            'extension_tmp' => $extension
        ]);

        return $id;
    }

    /**
     * @param IRequest $httpRequest
     * @return string
     * @throws \Dibi\Exception
     */
    public function revertFileUpload(IRequest $httpRequest): string
    {
        $id = $httpRequest->getRawBody();
        bdump($id);

        //Delete logo temporary directory
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        //Delete logo from DB based on it's ID
        $this->logoManager->delete($id);

        return "";
    }

    /**
     * @param IRequest $httpRequest
     * @return string
     * @throws \Dibi\Exception
     */
    public function updateFile(IRequest $httpRequest): string
    {
        //Get updated logo id
        $id = $httpRequest->getUrl()->getQueryParameter("logo_id");

        bdump($id);

        $extension = $this->getFileExtension($httpRequest->getFile("logo_file")->name);

        FileSystem::createDir($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        if(
        !file_put_contents(
            $this->logosTmpDir . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $extension,
            file_get_contents($httpRequest->getFile('logo_file'))
        )
        ){
            throw new IOException('Chyba při ukládání souboru.');
        }

        //Update logo DB record's temporary extension column
        $this->logoManager->update($id,[
           "extension_tmp" => $extension
        ]);

        return $id;

    }

    /**
     * @param IRequest $httpRequest
     * @return string
     */
    public function revertFileUpdate(IRequest $httpRequest): string
    {
        $id = $httpRequest->getRawBody();
        bdump($id);

        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        return "";
    }

    /**
     * @param int $id
     * @throws \Dibi\Exception
     */
    public function finalStore(int $id): void
    {
        FileSystem::copy($this->logosTmpDir . DIRECTORY_SEPARATOR . $id, $this->logosDir . DIRECTORY_SEPARATOR . $id);
        FileSystem::delete($this->logosTmpDir . DIRECTORY_SEPARATOR . $id);

        $fileRecord = $this->logoManager->getById($id);

        bdump($id);
        bdump($fileRecord);

        //Set logo it's final path
        $this->logoManager->update(
            $id,
            [
                //'path' => DIRECTORY_SEPARATOR . 'data_public' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . 'logo' . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . 'file' . $fileRecord->extension
                "extension" => $fileRecord->extension_tmp,
                "path" => DIRECTORY_SEPARATOR . "data_public" . DIRECTORY_SEPARATOR . "logos" . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR . "file" . $fileRecord->extension_tmp,
            ]
        );
    }

    /**
     * @param int $testId
     * @throws \Dibi\Exception
     * @throws \Dibi\NotSupportedException
     */
    public function createTestZip(int $testId)
    {
        $zip = new \ZipArchive();

        $variantRows = $this->testManager->getVariants($testId);

        //Check files existence
        foreach ($variantRows as $row){
            if(!file_exists(DATA_DIR  . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $testId . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($row->variant) . '.tex' ))
                throw new FileNotFoundException('Soubor s testem nenalezen.');
        }

        $logoId = $this->testManager->getById($testId)->logo_id;
        $logoExt = $this->logoManager->getById((int) $logoId)->extension;

        //Check logo file existence
        if(!file_exists(LOGOS_DIR . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt))
            throw new FileNotFoundException('Soubor s logem nenalezen');

        if(!$zip->open(DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $testId . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip', \ZipArchive::CREATE))
            throw new IOException('Zip archiv nemohl být vytvořen.');

        foreach ($variantRows as $row)
            $zip->addFile(
                DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $testId . DIRECTORY_SEPARATOR . 'variant_' . Strings::lower($row->variant) . '.tex',
                'variant_' . Strings::lower($row->variant) . '.tex'
            );

        $zip->addFile(LOGOS_DIR . DIRECTORY_SEPARATOR . $logoId . DIRECTORY_SEPARATOR . 'file' . $logoExt, 'file' . $logoExt);

        $zip->close();
    }

    /**
     * @param int $testId
     */
    public function moveTestDirToPublic(int $testId)
    {
        FileSystem::copy(
            DATA_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . $testId . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip',
            DATA_PUBLIC_DIR . DIRECTORY_SEPARATOR . 'tests' . DIRECTORY_SEPARATOR . 'test_' . $testId . '.zip'
            );
    }

}