<?php
namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use App\User;
use function FastRoute\TestFixtures\empty_options_cached;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use GuzzleHttp\Client;

class LoginController extends Controller
{
    //注册
    public function reg(Request $request)
    {
        $account = $request->input('account');
        $password = $request->input('password');
        $password_confirm = $request->input('password_confirm');
        $email = $request->input('email');

        //dd($account);
        if(empty($account)){
            return ['msg'=>'账号不能为空','code'=>2];
        }
        if(empty($password)){
            return ['msg'=>'密码不能为空','code'=>2];
        }
        if(empty($password_confirm)){
            return ['msg'=>'重复密码不能为空','code'=>2];
        }
        if(empty($email)){
            return ['msg'=>'邮箱不能为空','code'=>2];
        }

        $u = UserModel::where(['email'=>$email])->first();
        //dd($u);
        if(!$u){
            //
            if($password==$password_confirm){
                $data = [
                    'account'=>$account,
                    'password'=>password_hash($password,PASSWORD_BCRYPT),
                    'email'=>$email
                ];
                $res = UserModel::insert($data);
                if($res){
                    return ['msg'=>'注册成功','code'=>1];
                }else{
                    return ['msg'=>'注册失败','code'=>2];
                }
            }else{
                return ['msg'=>'注册失败','code'=>2];
            }
        }else{
            //已注册
            return ['msg'=>'已注册','code'=>2];
        }
    }

    //登录
    public function login(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');

        if(empty($email)){
            return ['msg'=>'账号不能为空','code'=>2];
        }
        if(empty($password)){
            return ['msg'=>'密码不能为空','code'=>2];
        }

        $u = UserModel::where(['email'=>$email])->first();
        if($u){
            //验证密码
            $u = $u->toArray();
            if( password_verify($password,$u['password']) ){
                //登陆成功，生成token
                $token = md5($u['u_id'].Str::random(8).mt_rand(11,99999));
                echo $token;
                //return ['msg'=>'登录成功','code'=>1];
            }else{
                return ['msg'=>'用户名或密码错误','code'=>2];
            }
        }else{
            //没有用户信息
            return ['msg'=>'没有用户信息','code'=>2];
        }
    }
}