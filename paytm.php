<?php

namespace aw2\paytm;

require_once("vendor/paytm/paytmchecksum/PaytmChecksum.php");

\aw2_library::add_service('paytm.generate_signature','',['namespace'=>__NAMESPACE__]);

/*
* Generate checksum by parameters we have in body
  @param 
	request_body <array>
	merchant_key <string>
  @return <array>
* 
*/
function generate_signature($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
	'request_body'=>'',
	'merchant_key'=>''
	), $atts) );
	
	$input_res=check_required_input($atts);
	if(isset($input_res['status']) && $input_res['status']==='error'){
		return $input_res;
	}
	
	$checksum = PaytmChecksum::generateSignature(json_encode($request_body, JSON_UNESCAPED_SLASHES), $merchant_key);

	return array("status"=>"success","signature"=>$checksum);
}

function verify_signature($atts,$content=null,$shortcode){
	
	if(\aw2_library::pre_actions('all',$atts,$content,$shortcode)==false)return;
	extract( shortcode_atts( array(
	'request_body'=>'',
	'merchant_key'=>'',
	'signature'=>''
	), $atts) );
	
	$input_res=check_required_input($atts);
	if(isset($input_res['status']) && $input_res['status']==='error'){
		return $input_res;
	}
	
	$verify_signature = PaytmChecksum::verifySignature(json_encode($request_body,JSON_UNESCAPED_SLASHES), $merchant_key,$signature );
	
	if($verify_signature){
		return array('status'=>'success','message signature is correct.');
	}
	
	return array('status'=>'error','message signature is incorrect.');
	
}

/*
	* This will check all the required parameters
	input : <array>
	return :
		<array>
	
*/
function check_required_input($input){
	
	$required_field=array();
	foreach($input as $key => $val){
		if(empty($val)){
			$required_field[]="$key parameter is required";
		}
	}
	
	if(!empty($required_field)){
		return array('status'=>'error','message'=>'missing required parameters ','errors'=>$required_field);
	}
}
?>