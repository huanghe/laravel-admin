<?php
/**
 * Created by PhpStorm.
 * User: hahaxixi2017
 * Date: 2018/3/13
 * Time: 19:05
 */

namespace Encore\Admin\Services;

use Auth;
use Encore\Admin\Repositories\UserRepository;

class UserService
{

    const EMAIL_CONFIRM_KEY_PREFIX = 'email_confirm:';

    /** @var UserRepository 注入的UserRepository Repository */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     *  author:HAHAXIXI
     *  created_at: 2018-3-19
     *  updated_at: 2018-3-
     * @param $username
     * @param $password
     * @return mixed
     *  desc   :    登录获取token
     */
    public function getLoginToken($username, $password)
    {
        filter_var($username, FILTER_VALIDATE_EMAIL) ?
            $credentials['email'] = $username :
            $credentials['username'] = $username;

        $credentials['password'] = $password;
        return Auth::guard('admin')->attempt($credentials);
    }

    public function getUserByAuthApi()
    {
        return Auth::guard('admin')->user();
    }

    /**
     *  author:HAHAXIXI
     *  created_at: 2018-3-19
     *  updated_at: 2018-3-
     * @param $token
     * @return mixed
     *  desc   :    登录获取token
     */
    public function respondWithToken($token)
    {
        return [
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ];
    }
}