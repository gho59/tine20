<?php
/**
 * Tine 2.0 - http://www.tine20.org
 * 
 * @package     MailFiler
 * @license     http://www.gnu.org/licenses/agpl.html
 * @copyright   Copyright (c) 2010-2016 Metaways Infosystems GmbH (http://www.metaways.de)
 * @author      Philipp Schüle <p.schuele@metaways.de>
 */
class MailFiler_AllTests
{
    public static function main ()
    {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }
    
    public static function suite ()
    {
        $suite = new PHPUnit_Framework_TestSuite('Tine 2.0 MailFiler All Tests');
        $suite->addTestSuite('MailFiler_Frontend_AllTests');
        
        return $suite;
    }
}
