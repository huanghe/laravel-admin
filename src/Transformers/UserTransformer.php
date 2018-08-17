<?php

namespace Encore\Admin\Transformers;

use Encore\Admin\Auth\Database\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     *  author:HAHAXIXI
     *  created_at: 2018-8-19
     *  updated_at: 2018-3-
     * @param User $user
     * @return array
     *  desc   :    个人信息统一返回格式
     */
    public function transform(User $user)
    {
        return [
            'username' => $user->username,
            'email' => $user->email,
            'avatar' => $user->avatar,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }


}