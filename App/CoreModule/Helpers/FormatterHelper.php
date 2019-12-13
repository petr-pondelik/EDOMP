<?php
/**
 * Created by PhpStorm.
 * User: wiedzmin
 * Date: 10.4.19
 * Time: 10:49
 */

namespace App\CoreModule\Helpers;

use Nette\Utils\ArrayHash;

/**
 * Class FormatterHelper
 * @package App\CoreModule\Helpers
 */
class FormatterHelper
{
    /**
     * @var StringsHelper
     */
    protected $stringsHelper;

    /**
     * FormatterHelper constructor.
     * @param StringsHelper $stringsHelper
     */
    public function __construct
    (
        StringsHelper $stringsHelper
    )
    {
        $this->stringsHelper = $stringsHelper;
    }

    /**
     * @param ArrayHash $resultArray
     * @return string
     */
    public function formatResult(ArrayHash $resultArray): string
    {
        $result = '';
        foreach ($resultArray as $key => $resItem) {
            if ($key !== 'type') {
                $result .= '$$' . $key . ' = ' . $resItem . '$$';
            }
        }
        return $result;
    }
}