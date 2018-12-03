<?php

namespace otherLogin;

/**
 *
 * @author Administrator
 *        
 */
class qqLogin {
	// TODO - Insert your code here
	
	private function  getconfig(){
		//QQ登陆配置
		return  array(
				'APPID'=>'xxxxxxxxxxxx',//应用唯一标识
				'APPKEY'=>'xxxxxxxxxxxx',//应用唯一标识
				'CALLBACK'=>'http://xxxxxxxxxxxx/qqlogin.html',//回调地址
				'STATE'=>md5("123"),//加密方式
				//功能扩展
				'SCOPE'=>'get_user_info,add_share,list_album,add_album,upload_pic,add_topic,add_one_blog,add_weibo,check_page_fans,add_t,add_pic_t,del_t,get_repost_list,get_info,get_other_info,get_fanslist,get_idolist,add_idol,del_idol,get_tenpay_addr'//回调域
		);
		
		
	}
	/**
	 *
	 *
	 * Administrator
	 * 方法说明：跳转到QQ登陆
	 */
	public function  jumpQQLogin(){
		$config=$this->getconfig();
		$response_type='code';
		$get_Authorization_Code_Url='https://graph.qq.com/oauth2.0/authorize?response_type='.$response_type.'&client_id='.$config['APPID'].'&redirect_uri='.$config['CALLBACK'].'&state='.$config['STATE'].'&scope='.$config['SCOPE'];
		header("location:".$get_Authorization_Code_Url);
		
	}
	/**
	 * 
	 * @return mixed[]
	 * Administrator
	 * 在回调地址中调用
	 * 方法说明：获得用户的token 和openid 
	 */
	public function  getTokenAndOpenid(){
		$config=$this->getconfig();
		$code=$_GET['code'];//得到code
		$grant_type='authorization_code';
		$get_Access_Token_Url='https://graph.qq.com/oauth2.0/token?grant_type='.$grant_type.'&client_id='.$config['APPID'].'&client_secret='.$config['APPKEY'].'&code='.$code.'&redirect_uri='.$config['CALLBACK'];
		
		
		
		$response = file_get_contents($get_Access_Token_Url);
		$params = array();
		parse_str($response, $params);//获得token
		
		
		$get_openid_url='https://graph.qq.com/oauth2.0/me?access_token='.$params['access_token'];
		$get_openid_return=file_get_contents($get_openid_url);
		
		
		
		//--------检测错误是否发生
		if(strpos($get_openid_return, "callback") !== false){
			
			$lpos = strpos($get_openid_return, "(");
			$rpos = strrpos($get_openid_return, ")");
			$get_openid_return = substr($get_openid_return, $lpos + 1, $rpos - $lpos -1);
		}
		
		
		$openid_arr=json_decode($get_openid_return,true);
		$access_token_and_openid=[
				'token'=>$params['access_token'],//获得的token
				'openid'=>$openid_arr['openid']//获得openid
		];
		
		
		return $access_token_and_openid;
	}
	
	/**
	 * 
	 * @return mixed
	 * Administrator
	 * 在回调地址中调用
	 * 方法说明：返回登陆用户信息
	 * 调用方式 ：1.引入此类
	 * 2.配置文件更改
	 * 3.$qq=new qqLogin();
    	$userinfo=$qq->getuserinfo();	
	 */
	public function getuserinfo(){
		$config=$this->getconfig();
		$access_token_and_openid=$this->getTokenAndOpenid();//得到token和openid
		$get_userinfo_url='https://graph.qq.com/user/get_user_info?access_token='.$access_token_and_openid['token'].'&oauth_consumer_key='.$config['APPID'].'&openid='.$access_token_and_openid['openid'];
		
		$array_user_info=file_get_contents($get_userinfo_url);

		
		$array_user_info=json_decode($array_user_info,true);
		$array_user_info['openid']=$access_token_and_openid['openid'];
		return ($array_user_info);
	}
	
}
