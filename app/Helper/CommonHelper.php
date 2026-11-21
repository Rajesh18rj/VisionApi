<?php

namespace App\Helper;

use App\Http\Model\general_settings;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CommonHelper
{
    public static $CurrentUser;
    public static function gs($key = null)
    {
        $general = Cache::get('GeneralSetting');
        if (!$general) {
            $general = general_settings::first();
            Cache::put('GeneralSetting', $general);
        }
        if ($key) return @$general->$key;
        return $general;
    }

    public static function getPaginate($paginate = null)
    {
        if (!$paginate) {
            $paginate = self::gs('paginate_number');
        }
        return $paginate;
    }


    public static function strLimit($title = null, $length = 10)
    {
        return Str::limit($title, $length);
    }

    public static function getTrx($length = 12)
    {
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ123456789';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
    return $randomString;
    }

    public static function replaceShortCode($name,$username,$template,$body)
    {
        if(is_array($username)){
	        $username = implode(',',$username);
	    }
		$message = str_replace("{{fullname}}", $name, $template);
	    $message = str_replace("{{username}}", $username, $message);
	    $message = str_replace("{{message}}", $body, $message);
	    return $message;
    }

    public static function getRealIP()
    {
        $ip = $_SERVER["REMOTE_ADDR"];
        //Deep detect ip
        if (filter_var(@$_SERVER['HTTP_FORWARDED'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED'];
        }
        if (filter_var(@$_SERVER['HTTP_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_X_REAL_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_X_REAL_IP'];
        }
        if (filter_var(@$_SERVER['HTTP_CF_CONNECTING_IP'], FILTER_VALIDATE_IP)) {
            $ip = $_SERVER['HTTP_CF_CONNECTING_IP'];
        }
        if ($ip == '::1') {
            $ip = '127.0.0.1';
        }

        return $ip;
    }



    public static function ipInfo()
    {
        $ip = self::getRealIP();


	    $xml = @simplexml_load_file("http://www.geoplugin.net/xml.gp?ip=" . $ip);


	    $country = @$xml->geoplugin_countryName;
	    $city = @$xml->geoplugin_city;
	    $area = @$xml->geoplugin_areaCode;
	    $code = @$xml->geoplugin_countryCode;
	    $long = @$xml->geoplugin_longitude;
	    $lat = @$xml->geoplugin_latitude;

	    $data['country'] = $country ?? [];
	    $data['city'] = $city ?? [];
	    $data['area'] = $area ?? [];
	    $data['code'] = $code ?? [];
	    $data['long'] = $long ?? [];
	    $data['lat'] = $lat ?? [];
	    $data['ip'] = $ip;
	    $data['time'] = date('Y-m-d h:i:s A');


	    return $data;
    }

    public static function osBrowser(){
	    $userAgent = $_SERVER['HTTP_USER_AGENT'];
	    $osPlatform = "Unknown OS Platform";
	    $osArray = array(
	        '/windows nt 10/i' => 'Windows 10',
	        '/windows nt 6.3/i' => 'Windows 8.1',
	        '/windows nt 6.2/i' => 'Windows 8',
	        '/windows nt 6.1/i' => 'Windows 7',
	        '/windows nt 6.0/i' => 'Windows Vista',
	        '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
	        '/windows nt 5.1/i' => 'Windows XP',
	        '/windows xp/i' => 'Windows XP',
	        '/windows nt 5.0/i' => 'Windows 2000',
	        '/windows me/i' => 'Windows ME',
	        '/win98/i' => 'Windows 98',
	        '/win95/i' => 'Windows 95',
	        '/win16/i' => 'Windows 3.11',
	        '/macintosh|mac os x/i' => 'Mac OS X',
	        '/mac_powerpc/i' => 'Mac OS 9',
	        '/linux/i' => 'Linux',
	        '/ubuntu/i' => 'Ubuntu',
	        '/iphone/i' => 'iPhone',
	        '/ipod/i' => 'iPod',
	        '/ipad/i' => 'iPad',
	        '/android/i' => 'Android',
	        '/blackberry/i' => 'BlackBerry',
	        '/webos/i' => 'Mobile'
	    );
	    foreach ($osArray as $regex => $value) {
	        if (preg_match($regex, $userAgent)) {
	            $osPlatform = $value;
	        }
	    }
	    $browser = "Unknown Browser";
	    $browserArray = array(
	        '/msie/i' => 'Internet Explorer',
	        '/firefox/i' => 'Firefox',
	        '/safari/i' => 'Safari',
	        '/chrome/i' => 'Chrome',
	        '/edge/i' => 'Edge',
	        '/opera/i' => 'Opera',
	        '/netscape/i' => 'Netscape',
	        '/maxthon/i' => 'Maxthon',
	        '/konqueror/i' => 'Konqueror',
	        '/mobile/i' => 'Handheld Browser'
	    );
	    foreach ($browserArray as $regex => $value) {
	        if (preg_match($regex, $userAgent)) {
	            $browser = $value;
	        }
	    }

	    $data['os_platform'] = $osPlatform;
	    $data['browser'] = $browser;

	    return $data;
	}

    // public static function hasTooManyLoginAttempts(Request $request)
    // {
    //     $data = self::limiter()->tooManyAttempts(
    //         self::throttleKey($request), self::maxAttempts()
    //     );
    //     return $data;
    // }

    // public static function limiter()
    // {
    //     return app(RateLimiter::class);
    // }

    // public static function throttleKey(Request $request)
    // {
    //     $data = $request->all();
    //     return Str::transliterate(Str::lower($request->input($data['username'])).'|'.$request->ip());
    // }

    // public static function maxAttempts()
    // {
    //     return '5';
    // }

    // public static function fireLockoutEvent(Request $request)
    // {
    //     event(new Lockout($request));
    // }

    // public static function sendLockoutResponse(Request $request)
    // {
    //     $data = $request->all();
    //     $seconds = self::limiter()->availableIn(
    //         self::throttleKey($request)
    //     );

    //     throw ValidationException::withMessages([
    //         $data['username'] => [trans('auth.throttle', [
    //             'seconds' => $seconds,
    //             'minutes' => ceil($seconds / 60),
    //         ])],
    //     ])->status(Response::HTTP_TOO_MANY_REQUESTS);
    // }

    // // protected function attemptLogin(Request $request)
    // // {
    // //     return $this->guard()->self::attempt(
    // //         $this->credentials($request), $request->boolean('remember')
    // //     );
    // // }

    // // public static function guard()
    // // {
    // //     return Auth::guard();
    // // }

    // // public static function credentials(Request $request)
    // // {
    // //     $data = $request->all();
    // //     return $request->only(self::username(), 'password');
    // // }

    // // public function username()
    // // {
    // //     return 'email';
    // // }
    // // public function attempt(array $credentials = [], $remember = false){
    // //     [
    // // };

    // public static function sendLoginResponse(Request $request)
    // {

    //     self::clearLoginAttempts($request);

    //     if ($response = self::authenticated($request, self::guard()->user())) {
    //         return $response;
    //     }

    //     // return $request->wantsJson()
    //     //             ? new JsonResponse([], 204)
    //     //             : redirect()->intended($this->redirectPath());
    // }

    // public static function clearLoginAttempts(Request $request)
    // {
    //     self::limiter()->clear(self::throttleKey($request));
    // }

    // public static function authenticated(Request $request, $user)
    // {
    //     //
    // }

    // public static function guard()
    // {
    //     return Auth::guard();
    // }

    // // public static function incrementLoginAttempts(Request $request)
    // // {
    // //     self::limiter()->hit(
    // //         self::throttleKey($request), $this->decayMinutes() * 60
    // //     );
    // // }

}
