<?php

namespace Varbox\Traits;

trait CanCheckLicense
{
    /**
     * Check to to see if a license code exists.
     * If it does not, throw a notification bubble.
     *
     * @return void
     */
    private function checkLicenseCodeExists()
    {
        if (!$this->shouldCheckForLicenseCode()) {
            return;
        }

        if (!$this->isValidLicenseCodeFormat(config('varbox.license.code'))) {
            flash()->warning("<strong>You're using unlicensed software!</strong><br />Please ask your developer to <a href='https://varbox.io/buy' target='_blank'>purchase a license code</a> to hide this message.");
        }
    }

    /**
     * Determine if checking for valid license code format should be executed.
     *
     * @return bool
     */
    private function shouldCheckForLicenseCode()
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests() || $this->app->isProduction()) {
            return false;
        }

        if (in_array($this->app['request']->ip() ?? '', ['127.0.0.1', '::1'])) {
            return false;
        }

        return true;
    }

    /**
     * Check that the license code is valid for the version of software being run.
     *
     * This method is intentionally obfuscated.
     * It's not terribly difficult to crack, but consider how much time it will take you to do so.
     * It might be cheaper to just buy a license code.
     * And in the process, you'd support the people who have created it, and who keep putting in time, every day, to make it better.
     *
     * @param string $d0
     * @return bool
     */
    private static function isValidLicenseCodeFormat($d0)
    {
        if(ctype_lower($d0)===true){return false;}if(substr_count($d0,'-')!==(1+1)*2){return false;}if(strlen($d0)-(1+1)*2!==(5+(2+3))*2+5){return false;}foreach([1+(1+1)*2,(5+5)*1+1,(2+3+1+4)*2-3,(2+3+1+4)*2+3]as $f1){if(substr($d0,$f1,1)!=='-'){return false;}}for($g2=0;$g2<=(1+1)*1;$g2++){$f1=substr($d0,$g2,1);if(strpos('ABCDEFGHJKLMNPQRSTUVWXYZ',$f1)===false){return false;}}for($g2=3;$g2<=(1+1)*2;$g2++){$f1=substr($d0,$g2,1);if(strpos('23456789',$f1)===false){return false;}}for($g2=(1+1)*1;$g2>=1;$g2--){$f1=substr($d0,strlen($d0)-$g2,1);if(strpos('ABCDEFGHJKLMNPQRSTUVWXYZ',$f1)===false){return false;}}for($g2=(1+1)*2+1;$g2>=(1+1)*2-1;$g2--){$f1=substr($d0,strlen($d0)-$g2,1);if(strpos('23456789',$f1)===false){return false;}}return true;
    }
}
