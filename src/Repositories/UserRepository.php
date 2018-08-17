<?php
/**
 * Created by PhpStorm.
 * User: hahaxixi2017
 * Date: 2018/8/1
 * Time: 17:44
 */

namespace Encore\Admin\Repositories;

use Encore\Admin\Auth\Database\User;

class UserRepository
{
    /** @var User 注入的User model */
    protected $user;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
}