<?php

namespace Varbox;

use Exception;
use Illuminate\Support\Facades\Http;

trait VarboxLicense
{
    /**
     * The endpoint where to send usage statistics.
     *
     * @var string
     */
    protected static $endpoint = 'https://varbox.io/api/reports/store';

    /**
     * Part of the actual token.
     * Used to match request signature.
     *
     * @var string
     */
    protected static $token = 'iNLk4nzIyMt4MJOvnIgLigOEjnkd4PhfzVvLVu9Hfsi4QvekZo';

    /**
     * Check to to see if a license code exists.
     * If it does not, throw a notification bubble.
     *
     * @return void
     */
    private function checkLicenseCodeExists()
    {
        if (!$this->shouldCheckLicenseCode()) {
            return;
        }

        if (!$this->isValidLicenseCode(config('varbox.license.code'))) {
            flash()->warning("<strong>You're using unlicensed software!</strong><br />Please <a href='https://varbox.io/buy' target='_blank'>purchase a license code</a> to hide this message.");
        }
    }

    /**
     * Send usage statistics to the varbox.io website.
     * Used to track unlicensed usage and general usage statistics.
     *
     * No GDPR implications, since no client info is send, only server info.
     *
     * @return void
     */
    private function checkLicenseCodeValid()
    {
        if (!$this->shouldCheckLicenseCode()) {
            return;
        }

        if (rand(1, 100) != 1) {
            return;
        }

        try {
            Http::withToken(static::$token)->post(static::$endpoint, [
                'app_url' => $_SERVER['HTTP_HOST'] ?? null,
                'laravel_version' => $this->app->version() ?? null,
                'varbox_license' => config('varbox.license.code') ?? null,
                'server_ip' => $_SERVER['SERVER_ADDR'] ?? null,
                'server_name' => $_SERVER['SERVER_NAME'] ?? null,
                'server_software' => $_SERVER['SERVER_SOFTWARE'] ?? null,
            ]);
        } catch (Exception $e) {}
    }

    /**
     * Determine if checking for valid license code format should be executed.
     *
     * @return bool
     */
    private function shouldCheckLicenseCode()
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests() || $this->app->isLocal()) {
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
    private static function isValidLicenseCode($d0)
    {
        if(ctype_lower($d0)===true){return false;}if(substr_count($d0,'-')!==(1+1)*2){return false;}if(strlen($d0)-(1+1)*2!==(5+(2+3))*2+5){return false;}foreach([1+(1+1)*2,(5+5)*1+1,(2+3+1+4)*2-3,(2+3+1+4)*2+3]as $f1){if(substr($d0,$f1,1)!=='-'){return false;}}for($g2=0;$g2<=(1+1)*1;$g2++){$f1=substr($d0,$g2,1);if(strpos('ABCDEFGHJKLMNPQRSTUVWXYZ',$f1)===false){return false;}}for($g2=3;$g2<=(1+1)*2;$g2++){$f1=substr($d0,$g2,1);if(strpos('23456789',$f1)===false){return false;}}for($g2=(1+1)*1;$g2>=1;$g2--){$f1=substr($d0,strlen($d0)-$g2,1);if(strpos('ABCDEFGHJKLMNPQRSTUVWXYZ',$f1)===false){return false;}}for($g2=(1+1)*2+1;$g2>=(1+1)*2-1;$g2--){$f1=substr($d0,strlen($d0)-$g2,1);if(strpos('23456789',$f1)===false){return false;}}return true;
    }
}
