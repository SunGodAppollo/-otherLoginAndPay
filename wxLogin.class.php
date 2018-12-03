<?php

namespace Org\otherLogin;

/**
 *
 * @author Administrator
 *        
 */
class wxLogin {
	// TODO - Insert your code here
	
	private function  getconfig(){
		//微信扫码登陆配置
		return  array(
		'APPID'=>'wxa8d8e6944a0b8557',//应用唯一标识
        'APPSECRET'=>'318ff0ea6e8c00898b9f98731a5aa07a',//应用唯一标识
        'REDIRECT_URI'=>'http://99boliyuan.com/Home/Login/wxlogin.html',
        'SCOPE'=>'snsapi_login',//应用授权作用域
        'STATE'=>md5('boliyuanwx')//应用加密
		);
		
		
	}
	/**
	 * 
	 * 
	 * Administrator
	 * 方法说明：跳转到微信登陆
	 */
	public function  jumpWxLogin(){
		$config=$this->getconfig();
		 $wx_login_url='https://open.weixin.qq.com/connect/qrconnect?appid='.$config['APPID'].'&redirect_uri='.$config['REDIRECT_URI'].'&scope='.$config['SCOPE'].'&state='.$config['STATE'].'#wechat_redirect';
        header("location:".$wx_login_url);
		
	}
	
	public function  getTokenAndOpenid(){
		$config=$this->getconfig();
	    //登陆验证1
		$code=$_GET['code'];
        $state=$_GET['state'];
        if($state!=$config['STATE']){
            echo("加密码不正确!");die();
        }
        $grant_type='authorization_code';
        
        $get_access_token_url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$config['APPID'].'&secret='.$config['APPSECRET'].'&code='.$code.'&grant_type='.$grant_type;
        
        $json_data = file_get_contents($get_access_token_url);
        
        //获取用户信息
        //转换成数组
        $json_arr = json_decode($json_data,true);

        
        $access_token=$json_arr['access_token'];
        $openid=$json_arr['openid'];
        
		$access_token_and_openid=[
			'token'=>$json_arr['access_token'],//获得的token
			'openid'=>$json_arr['openid']//获得openid
		];
			
			
			return $access_token_and_openid;
		
       						
	}
	
	
	public function getuserinfo(){
		$config=$this->getconfig();
		$access_token_and_openid=$this->getTokenAndOpenid();//得到token和openid
		 $get_user_info_url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token_and_openid['token'].'&openid='.$access_token_and_openid['openid'];
        $user_info= file_get_contents($get_user_info_url);
        $array_user_info = json_decode($user_info,true);	
        $array_user_info['openid']=$access_token_and_openid['openid'];
		return ($array_user_info);
	}
	
}

