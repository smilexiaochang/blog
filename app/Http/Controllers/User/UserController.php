<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Model\UserModel;
use App\User;
use function FastRoute\TestFixtures\empty_options_cached;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

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

    /**
     * 用户注册
     */
    public function reg(Request $request)
    {
        $u_name = $request->input('u_name');
        $u_pwd1 = $request->input('u_pwd1');
        $u_pwd2 = $request->input('u_pwd2');
        if(empty($u_name)){
            echo "用户名不能为空";die;
        }
        if(empty($u_pwd1)){
            echo "密码不能为空";die;
        }
        if(empty($u_pwd2)){
            echo "重复密码不能为空";die;
        }
        //根据用户名查询
        $u = UserModel::where(['u_name'=>$u_name])->first();
        if(!$u){
            //
            if($u_pwd1==$u_pwd2){
                $data = [
                    'u_name'=>$u_name,
                    'u_pwd'=>$u_pwd1
                ];
                $res = UserModel::insert($data);
                if($res){
                    echo "注册成功";
                }
            }else{
                echo "注册失败";
            }
        }else{
            //已注册
            echo "已注册";
        }

    }

    /**
     * 用户登录
     */
    public function login(Request $request)
    {
        $u_name = $request->input('u_name');
        $u_pwd = $request->input('u_pwd');

        if(empty($u_name)){
            echo "用户名不能为空";die;
        }
        if(empty($u_pwd)){
            echo "密码不能为空";die;
        }

        $u = UserModel::where(['u_name'=>$u_name])->first();
        if($u){
            //验证密码
            $u = $u->toArray();
            if($u_pwd == $u['u_pwd']){
                echo "登陆成功";
            }else{
                echo "用户名或密码错误";
            }
        }else{
            //没有用户信息
            echo "用户名或密码错误";
        }
    }

    /**
     * 修改密码
     */
    public function editPwd(Request $request)
    {
        $u_name = $request->input('u_name');
        $u_pwd1 = $request->input('u_pwd1');
        $u_pwd2 = $request->input('u_pwd2');
        if(empty($u_name)){
            echo "用户名不能为空";die;
        }
        if(empty($u_pwd1)){
            echo "密码不能为空";die;
        }
        if(empty($u_pwd2)){
            echo "新密码不能为空";die;
        }
        $u = UserModel::where(['u_name'=>$u_name])->first();
        if($u){
            $u = $u->toArray();
            if($u_pwd1 == $u['u_pwd']){
                //更改密码
                $res = UserModel::where('u_name',$u_name)->update(['u_pwd'=>$u_pwd2]);
                if($res){
                    echo "修改成功";
                }else{
                    echo "修改失败";
                }
            }else{
                echo "用户名或密码错误";
            }
        }else{
            //没有用户信息
            echo "没有用户信息";
        }
    }

    /**
     * 查询天气
     */
    public function getWeather(Request $request)
    {
        $city = $request->input('city');
        $url = "http://api.k780.com/?app=weather.future&weaid={$city}&appkey=42238&sign=72c3c55b2a6ea113cedbc41234d530c2&format=json";
        $weather = file_get_contents($url);
        $weather = json_decode($weather,true);
        if($weather['success']==0){
            echo '查看天气请输入城市名称来查看';die;
        }
        $msg = '';
        foreach ($weather['result'] as $k => $v){
            $msg .= $v['days']."-".$v['citynm']."-".$v['temperature']."\n";
        }
        echo $msg;
    }

    //解密测试
    public function decrypt1()
    {
        $data = file_get_contents("php://input");
        $dec_data = base64_decode($data);
        dd($dec_data);
    }

    //解密测试
    public function decrypt2()
    {
        $key = "password";     //加密解密密码
        $iv = "abcabcabcabcabca";    //初始向量
        $data = file_get_contents("php://input");
        $data = base64_decode($data);
        $dec_data = openssl_decrypt($data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);
        echo '<pre>';print_r($dec_data);echo '</pre>';
    }

    //非对称加密解密
    public function rsadecrypt1()
    {
        $enc_data = file_get_contents("php://input");
        $pub_key = openssl_get_publickey("file://".storage_path('keys/public.key'));
        openssl_public_decrypt($enc_data,$data,$pub_key);

        echo $data;
    }

    //20190613作业练习
    public function rsadecrapy2()
    {
        $data = file_get_contents("php://input");
        $body_data = unserialize($data);
        $enc_data = $body_data['enc_data'];
        $signature = $body_data['signature'];

        //解密
        $key = "password";
        $iv = "abcabcabcabcabca";
        $dec_data = openssl_decrypt($enc_data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);

        //验证签名
        //1、获取公钥
        $public_key = openssl_get_publickey("file://".storage_path('keys/public.key'));
        $ok = openssl_verify($dec_data, $signature, $public_key);

        //echo $ok;die;

        if ($ok == 1) {
            //加密
            $key = "password";
            $iv = "abcabcabcabcabca";
            $enc_data = openssl_encrypt($dec_data,'AES-128-CBC',$key,OPENSSL_RAW_DATA,$iv);

            //签名
            $priva_key = openssl_get_privatekey("file://".storage_path('keys/priva.pem'));
            openssl_sign($dec_data, $signature, $priva_key);

            $arr = [
                'enc_data'=>$enc_data,
                'signature'=>$signature
            ];
            $body_data = serialize($arr);
            $client = new Client();
            $url = "http://www.1810api.com/test/rsadecrapy3";
            $response = $client->request('POST',$url,[
                'body'=>$body_data
            ]);
            //openssl_free_key($private_key);

            echo "<hr>";
            echo $response->getBody();

        } elseif ($ok == 0) {
            echo "bad";
        } else {
            echo "ugly, error checking signature";
        }
    }

    //openssl验签
    public function openssltest1()
    {
        $data = $_POST;
        echo '<pre>';print_r($data);echo '</pre>';

        //验签
        $signature = base64_decode($data['signature']);
        unset($data['signature']);

        $str0 = "";
        foreach ($data as $k=>$v){
            $str0 .= $k.'='.$v.'&';
        }
        $str = rtrim($str0,'&');

        //验签
        $sign = openssl_verify($str,$signature,openssl_get_publickey("file://".storage_path('keys/public.key')));
        if($sign == 1){
            echo "ok";
        }else{
            echo "bad";
        }
    }

}



















