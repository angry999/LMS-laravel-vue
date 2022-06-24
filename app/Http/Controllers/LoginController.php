<?php

namespace App\Http\Controllers;

use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use LMS;
use App\LMS\LmsException;
use Exception;

class LoginController extends Controller
{
    private $isPremiunUser = false;

    public function login(LoginRequest $request)
    {
        $username = $request->input('email');
        $password = $request->input('password');

        try {
            $response = LMS::login($username, $password);
            $user = User::startSession($response->toArray());
            $user->syncProgress();
            $token = auth()->login($user);

            $this->getLicenceExpiration($user->session_id);

            return $this->sendToken($token, [
                'success' => true,
                'user' => $user,
                'premium' => $this->isPremiunUser,
            ]);
        } catch (LmsException $e) {
            switch ($e->getCode()) {
                case LmsException::INVALID_CREDENTIALS:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_INVALID_CREDENTIALS,
                    ]);
                case LmsException::LICENSE_EXPIRED:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_LICENSE_EXPIRED,
                    ]);
                case LmsException::ALREADY_LOGGED_IN:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_ALREADY_LOGGED_IN,
                    ]);
                default:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_UNKNOWN_ERROR,
                    ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'reason' => self::REASON_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $username = $request->input('email');

        try {
            LMS::resetPassword($username);
            return response()->json([
                'success' => true,
            ]);
        } catch (LmsException $e) {
            switch ($e->getCode()) {
                case LmsException::INVALID_USERNAME:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_INVALID_USERNAME,
                    ]);
                default:
                    return response()->json([
                        'success' => false,
                        'reason' => self::REASON_UNKNOWN_ERROR,
                    ]);
            }
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'reason' => self::REASON_SERVER_ERROR,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function refresh()
    {
        $newToken = auth()->refresh();

        return $this->sendToken($newToken);
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'success' => true,
        ]);
    }

    protected function sendToken($token, $body = null)
    {
        $response = new Response();

        if ($body) {
            $response->setContent($body);
        }

        return $response->withCookie(cookie()->forever('token', $token));
    }

    private function getLicenceExpiration($sessionId) {

        $url = "https://lms.speakingpal.com/services/speakingpal_rest/UserLicense?session_id=$sessionId";

        $client = new \GuzzleHttp\Client();
        $response = $client->get($url);

        try {

            $licenceExpirationDateStr = (string)$response->xml()->Data->ExpirationUtcTime;

            $licenceExpirationDate = Carbon::createFromFormat('Y-m-d\TH:i:s', $licenceExpirationDateStr)->toDateTimeString();

            $this->isPremiunUser = (string)$response->xml()->Data->Trial;

            return $licenceExpirationDate;

        } catch (Exception $e) {

            return false;

        }
    }
}
