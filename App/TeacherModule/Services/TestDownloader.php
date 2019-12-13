<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.12.19
 * Time: 10:30
 */

namespace App\TeacherModule\Services;


use App\CoreModule\Interfaces\IDownloader;
use Nette\Application\IResponse;
use Nette\Application\Responses\FileResponse;

/**
 * Class Downloader
 * @package App\TeacherModule\Services
 */
final class TestDownloader implements IDownloader
{
    private const CONTENT_TYPE = 'application/zip';
    private const FORCE_DOWNLOAD = true;
    private const FORMAT = '.zip';
    private const FILE_NAME = 'test';

    /**
     * @var string
     */
    protected $testDataDir;

    /**
     * TestDownloader constructor.
     * @param string $testDataDir
     */
    public function __construct(string $testDataDir)
    {
        $this->testDataDir = $testDataDir;
    }

    /**
     * @param int $id
     * @return string
     */
    private static function getFileName(int $id): string
    {
        return self::FILE_NAME . '_' . $id . self::FORMAT;
    }

    /**
     * @param int|null $id
     * @return IResponse
     * @throws \Nette\Application\BadRequestException
     */
    public function download(int $id = null): IResponse
    {
        $response = new FileResponse(
            $this->testDataDir . $id . DIRECTORY_SEPARATOR . self::getFileName($id),
            self::getFileName($id),
            self::CONTENT_TYPE,
            self::FORCE_DOWNLOAD
        );
        return $response;
    }
}