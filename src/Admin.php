<?php

namespace Encore\Admin;

use Closure;
use Encore\Admin\Auth\Database\Menu;
use Encore\Admin\Layout\Content;
use Encore\Admin\Widgets\Navbar;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;
use InvalidArgumentException;

/**
 * Class Admin.
 */
class Admin
{
    /**
     * @var Navbar
     */
    protected $navbar;

    /**
     * @var array
     */
    public static $script = [];

    /**
     * @var array
     */
    public static $css = [];

    /**
     * @var array
     */
    public static $js = [];

    /**
     * @var array
     */
    public static $extensions = [];

    /**
     * @param $model
     * @param Closure $callable
     *
     * @return \Encore\Admin\Grid
     */
    public function grid($model, Closure $callable)
    {
        return new Grid($this->getModel($model), $callable);
    }

    /**
     * @param $model
     * @param Closure $callable
     *
     * @return \Encore\Admin\Form
     */
    public function form($model, Closure $callable)
    {
        return new Form($this->getModel($model), $callable);
    }

    /**
     * Build a tree.
     *
     * @param $model
     *
     * @return \Encore\Admin\Tree
     */
    public function tree($model, Closure $callable = null)
    {
        return new Tree($this->getModel($model), $callable);
    }

    public function userTree($user, $model, Closure $callable = null)
    {
        $tree = $this->tree($model, $callable);
        $this->roleCheck($tree, $user);
    }

    public function roleCheck($tree, $user)
    {
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

    /**
     * @param Closure $callable
     *
     * @return \Encore\Admin\Layout\Content
     */
    public function content(Closure $callable = null)
    {
        return new Content($callable);
    }

    /**
     * @param $model
     *
     * @return mixed
     */
    public function getModel($model)
    {
        if ($model instanceof EloquentModel) {
            return $model;
        }

        if (is_string($model) && class_exists($model)) {
            return $this->getModel(new $model());
        }

        throw new InvalidArgumentException("$model is not a valid model");
    }

    /**
     * Add css or get all css.
     *
     * @param null $css
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public static function css($css = null)
    {
        if (!is_null($css)) {
            self::$css = array_merge(self::$css, (array)$css);

            return;
        }

        $css = array_get(Form::collectFieldAssets(), 'css', []);

        static::$css = array_merge(static::$css, $css);

        return view('admin::partials.css', ['css' => array_unique(static::$css)]);
    }

    /**
     * Add js or get all js.
     *
     * @param null $js
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public static function js($js = null)
    {
        if (!is_null($js)) {
            self::$js = array_merge(self::$js, (array)$js);

            return;
        }

        $js = array_get(Form::collectFieldAssets(), 'js', []);

        static::$js = array_merge(static::$js, $js);

        return view('admin::partials.js', ['js' => array_unique(static::$js)]);
    }

    /**
     * @param string $script
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|void
     */
    public static function script($script = '')
    {
        if (!empty($script)) {
            self::$script = array_merge(self::$script, (array)$script);

            return;
        }

        return view('admin::partials.script', ['script' => array_unique(self::$script)]);
    }

    /**
     * Left sider-bar menu.
     *
     * @return array
     */
    public function menu()
    {
        return (new Menu())->toTree();
    }

    /**
     * Get admin title.
     *
     * @return Config
     */
    public function title()
    {
        return config('admin.title');
    }

    /**
     * Get current login user.
     *
     * @return mixed
     */
    public function user()
    {
        return Auth::guard('admin')->user();
    }

    /**
     * Set navbar.
     *
     * @param Closure|null $builder
     *
     * @return Navbar
     */
    public function navbar(Closure $builder = null)
    {
        if (is_null($builder)) {
            return $this->getNavbar();
        }

        call_user_func($builder, $this->getNavbar());
    }

    /**
     * Get navbar object.
     *
     * @return \Encore\Admin\Widgets\Navbar
     */
    public function getNavbar()
    {
        if (is_null($this->navbar)) {
            $this->navbar = new Navbar();
        }

        return $this->navbar;
    }


    /**
     *  author:HAHAXIXI
     *  created_at: 2018-7-31
     *  updated_at: 2018-7-
     *  desc   :    注册默认路由
     */
    public function registerDefaultRoutes()
    {
        $api = app('Dingo\Api\Routing\Router');
        $api->version('v1', [
            'prefix' => config('admin.route.prefix'),
            'namespace' => 'Encore\Admin\Controllers',
            'middleware' => [],
        ], function ($api) {
            $api->post('login', 'PublicController@login');
            // 需要 token 验证的接口
            $api->group(['middleware' => 'auth_without_permission'], function ($api) {//校验token的中间件api.auth

                $api->get('menu', 'PublicController@menu');
                $api->get('check_permission/{permissions}', 'AuthController@PermissionCheck');//批量权限检查
                $api->get('user_can/{permission}', 'AuthController@can');//检查某个具体权限
                $api->get('all_permissions', 'AuthController@allPermissions');//全部权限

            });
            // 需要 token 验证,并且验证权限的接口
            $api->group(['middleware' => config('admin.route.middleware')], function ($api) {//校验token的中间件api.auth

                /* resources:
                    Verb	    URI	                    Action	    Route Name
                    GET	        /photos	                index	    photos.index
                    GET	        /photos/create          create	    photos.create
                    POST	    /photos	                store	    photos.store
                    GET	        /photos/{photo}	        show	    photos.show
                    GET	        /photos/{photo}/edit	edit	    photos.edit
                    PUT/PATCH   /photos/{photo}	        update	    photos.update
                    DELETE	    /photos/{photo}	        destroy	    photos.destroy
                 */
                $api->resource('users', 'UserController', ['except' => ['create', 'edit']]);
                $api->resource('roles', 'RoleController', ['except' => ['create', 'edit']]);
                $api->resource('permissions', 'PermissionController', ['except' => ['create', 'edit']]);
                $api->resource('menus', 'MenuController', ['except' => ['create', 'edit']]);
                $api->resource('logs', 'LogController', ['only' => ['index', 'destroy']]);
            });

        });
    }

    /**
     * Extend a extension.
     *
     * @param string $name
     * @param string $class
     *
     * @return void
     */
    public static function extend($name, $class)
    {
        static::$extensions[$name] = $class;
    }
}
