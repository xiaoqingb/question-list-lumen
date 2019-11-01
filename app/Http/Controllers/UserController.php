<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function register(Request $request){
        // step 1. 验证数据
        $nickName = $request->input('nickName');
        $email = $request->input('email');
        $password = $request->input('password');

        $validator = Validator::make($request->all(), [
            'email' => ['required','regex:/^.+@.+$/i'],
            'nickName' => 'required',
            'password'=>['required','min:6']
        ],[
            'email.required'=>'邮箱不能为空',
            'email.regex'=>'邮箱格式有误',
            'name.required'=>'昵称不能为空',
            'password.required'=>'密码不能为空',
            'password.min'=>'密码不能少于6位'
        ]);
        if ($validator->fails()){
            return[
              'code'=>'0001',
              'msg'=>$validator->errors()->first()
            ];
        }

        // step 2. 判断一下这个邮箱是否已经被注册过了
        $result = DB::table('users')->where('email',$email)->exists();
        if($result){
            return [
                'code' => '0001',
                'msg' => '该邮箱已被注册'
            ];
        }

        // step 3. 创建新的账号
        $result = DB::table('users')->insert([
            [
                'name' => $nickName,
                'email' => $email,
                'password' => md5($password)
            ]
        ]);
        // 为什么会有这个错呢？
        if(!$result){
            return [
                'code' => '0002',
                'msg' => '注册时发生了未知的错误'
            ];
        }
        return [
            'code' => '0000',
            'msg' => '注册成功'
        ];

    }
    //
    public function login(Request $request){
        $email = $request->input('email');
        $password = $request->input('password');
        $validator = Validator::make($request->all(), [
            'email' => ['required','regex:/^.+@.+$/i'],
            'password'=>['required']
        ],[
            'email.required'=>'邮箱不能为空',
            'email.regex'=>'邮箱格式有误',
            'password.required'=>'密码不能为空',
        ]);
        if ($validator->fails()){
            return[
                'code'=>'0001',
                'msg'=>$validator->errors()->first()
            ];
        }
        $result= DB::table('users')->where('email',$email)->exists();
        if(!$result){
            return[
                'code'=>'0002',
                'msg'=>'帐号或密码错误',
            ] ;
        }
        $result=DB::table('users')->where(['email'=>$email,'password'=>md5($password)])->get();
        if (count($result)===0){
            return[
                'code'=>'0002',
                'msg'=>'帐号或密码错误',
            ];
        }
        session_start();
        $_SESSION['name']=$result[0]->name;
        $_SESSION['id']=$result[0]->id;
        setcookie('user',$result[0]->id.'::'.$result[0]->name,time()+7*24*3600,'/');
        return[
            'code'=>'0000',
            'msg'=>'登录成功',
        ];
    }

    public  function logout(Request $request){
        setcookie("user", "", time()-3600);
        return [
            'code'=>'0000',
            'msg'=>'注销成功'
        ];
    }

    public function getNickName(Request $request){
        if (isset($_COOKIE['user'])){
            $_SESSION['id']=explode('::',$_COOKIE['user'])[0];
            $_SESSION['name']=explode('::',$_COOKIE['user'])[1];
        }
        if (!$_SESSION['name']){
            return[
                'code'=>'0001',
                'msg'=>'您还未登录'
            ];
        }

        return [
            'code'=>'0000',
            'msg'=>'获取用户名成功',
            'data'=>$_SESSION['name']
        ];
    }
}
