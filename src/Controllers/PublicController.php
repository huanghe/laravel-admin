<?php

namespace Encore\Admin\Controllers;

use Encore\Admin\Admin;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Requests\AuthorizationRequest;
use Encore\Admin\Services\UserService;
use Encore\Admin\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PublicController extends Controller
{
    /**
     * @var :inject userService;
     */
    protected $userService;

    /**
     * UserController constructor.
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * Handle a login request.
     *
     * @param Request $request
     *
     * @return mixed
     */
    public function login(AuthorizationRequest $request)
    {

        if (!$token = $this->userService->getLoginToken($request->username, $request->password)) {
            return $this->response->errorUnauthorized('未激活或用户名、密码错误');
        }
        $transformer = new UserTransformer();
        return $this->response->array(array_merge($this->userService->respondWithToken($token), $transformer->transform($this->userService->getUserByAuthApi())));
    }
    /**
     * Index interface.
     *
     * @return Content
     */
//    public function menu()
//    {
//        return (new Menu())->toTree();
//    }
    public function menu()
    {
        $tree = (new Menu())->toTree();
        return $this->roleCheck($tree, Auth::guard('admin')->user());
    }

    public function roleCheck($tree, $user)
    {
        if (isset($tree['id'])) {//遍历到最后一层
            if ($user->visible($tree['roles'])) {
                if (isset($tree['children'])) {
                    foreach ($tree['children'] as $item) {
                        $this->roleCheck($item, $user);
                    }
                }
            } else {
                unset($tree[0]);
            }
        }else{
            foreach ($tree as $key => $node) {
                if ($user->visible($node['roles'])) {
                    if (isset($node['children'])) {
                        foreach ($node['children'] as $item) {
                            $this->roleCheck($item, $user);
                        }
                    }
                } else {
                    unset($tree[$key]);
                }
            }
        }

        return $tree;
    }
}
