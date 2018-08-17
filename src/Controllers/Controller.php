<?php

namespace Encore\Admin\Controllers;

/**
 * Created by PhpStorm.
 * User: hahaxixi2017
 * Date: 2018/7/31
 * Time: 16:48
 */
use Dingo\Api\Routing\Helpers;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, Helpers;
}