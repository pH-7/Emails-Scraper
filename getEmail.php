<?php
/**
 * @author         Pierre-Henry Soria <phenrysoria@gmail.com>
 * @copyright      (c) 2017, Pierre-Henry Soria. All Rights Reserved.
 * @license        GNU General Public License; <https://www.gnu.org/licenses/gpl-3.0.en.html>
 */

class getEmails
{
    const EMAIL_STORAGE_FILE = 'emails.txt';

     public function __construct($sFilePath)
     {
         $aUrls = $this->getUrls($sFilePath);

         foreach($aUrls as $sUrl) {
             $rPage = $this->getContents($sUrl);
             $this->getAndSaveEmails($rPage);
         }
         $this->removeDuplicate();
     }

     protected function getAndSaveEmails($sPageContent)
     {
          preg_match_all('/([\w+\.]*\w+@[\w+\.]*\w+[\w+\-\w+]*\.\w+)/is', $sPageContent, $aResults);

         foreach($aResults[1] as $sCurrentEmail) {
             file_put_contents(self::EMAIL_STORAGE_FILE, $sCurrentEmail . "\r\n", FILE_APPEND);
         }
     }

     protected function getContents($sUrl)
     {
         if (function_exists('curl_init')) {
            $rCh = curl_init();
            curl_setopt($rCh, CURLOPT_URL, $sUrl);
            curl_setopt($rCh, CURLOPT_HEADER, 0);
            curl_setopt($rCh, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($rCh, CURLOPT_FOLLOWLOCATION, 1);
            $mResult = curl_exec($rCh);
            curl_close($rCh);
            unset($rCh);
            return $mResult;
        } else {
            return file_get_contents($sUrl);
        }
     }

     protected function getUrls($sFilePath)
     {
         return file($sFilePath);
     }

     protected function removeDuplicate()
     {
         $aEmails = file(self::EMAIL_STORAGE_FILE);
         $aEmails = array_unique($aEmails);
         file_put_contents(self::EMAIL_STORAGE_FILE, implode('', $aEmails));
     }
}

new getEmails('file-with-urls-to-get-emails.txt');
