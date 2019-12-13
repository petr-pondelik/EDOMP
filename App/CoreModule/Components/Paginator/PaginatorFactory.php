<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 13.12.19
 * Time: 11:46
 */

namespace App\CoreModule\Components\Paginator;

use IPub\VisualPaginator\Components as VisualPaginator;

/**
 * Class PaginatorFactory
 * @package App\CoreModule\Components\Paginator
 */
final class PaginatorFactory
{
    public const STUDENT = 0;
    public const TEACHER = 1;

    /**
     * @var array
     */
    private static $templatePaths = [
        self::STUDENT => null,
        self::TEACHER => null
    ];

    /**
     * PaginatorFactory constructor.
     * @param string $studentPath
     * @param string $teacherPath
     */
    public function __construct
    (
        string $studentPath,
        string $teacherPath
    )
    {
        self::$templatePaths[self::STUDENT] = $studentPath . DIRECTORY_SEPARATOR . 'VisualPaginator' . DIRECTORY_SEPARATOR;
        self::$templatePaths[self::TEACHER] = $teacherPath . DIRECTORY_SEPARATOR . 'VisualPaginator' . DIRECTORY_SEPARATOR;
    }

    /**
     * @param int $type
     * @return string
     */
    private static function getTypeTemplateAddr(int $type): string
    {
        return self::$templatePaths[$type];
    }

    /**
     * @param int $type
     * @param string $templatePath
     * @return VisualPaginator\Control
     */
    public function create(int $type, string $templatePath): VisualPaginator\Control
    {
        $paginator = new VisualPaginator\Control();
        $paginator->setTemplateFile(self::getTypeTemplateAddr($type) . $templatePath);
        return $paginator;
    }
}