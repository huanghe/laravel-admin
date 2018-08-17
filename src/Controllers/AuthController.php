<?php

namespace Encore\Admin\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthController extends Controller
{
    /**
     *  author:HAHAXIXI
     *  created_at: 2018-8-16
     *  updated_at: 2018-8-
     * @param $permissions
     *  desc   :    权限校验，支持批量
     */
    public function PermissionCheck($permissions)
    {
        if (Auth::guard('admin')->user()->isRole('administrator')) {
            return 1;
        }
        $permission = explode(',', $permissions);
        if (count($permission) > 1) {
            collect($permission)->each(function ($permission) {
                call_user_func([$this, 'PermissionCheck'], $permission);
            });

            return 1;
        } else {
            $slug = $permission[0];
        }

        if (Auth::guard('admin')->user()->cannotDo($slug)) {
            return -1;
//            throw new UnauthorizedHttpException('no permission');
        }
    }

    /**
     *  author:HAHAXIXI
     *  created_at: 2018-8-16
     *  updated_at: 2018-8-
     * @param $slug
     * @return string
     *  desc   :    检查某个具体权限
     */
    public function can($slug)
    {
        return Auth::guard('admin')->user()->canDo($slug) ? 1 : -1;
    }

    /**
     *  author:HAHAXIXI
     *  created_at: 2018-8-16
     *  updated_at: 2018-8-
     * @return mixed
     *  desc   :    获取全部权限
     */
    public function allPermissions()
    {
        return Auth::guard('admin')->user()->permissions;
    }

}
