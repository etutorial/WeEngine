<?php 
/**
 * [WeEngine System] Copyright (c) 2014 WE7.CC
 * WeEngine is NOT a free software, it under the license terms, visited http://www.we7.cc/ for more details.
 */
defined('IN_IA') or exit('Access Denied');

load()->model('user');
load()->classs('oauth2/oauth2client');

$dos = array('display', 'valid_mobile', 'register');
$do = in_array($do, $dos) ? $do : 'display';

$_W['page']['title'] = '注册选项 - 用户设置 - 用户管理';
if (empty($_W['setting']['register']['open'])) {
	itoast('本站暂未开启注册功能，请联系管理员！', '', '');
}
$register_type = !empty($_GPC['register_type']) ? $_GPC['register_type'] : 'system';
if ($register_type == 'system') {
	$extendfields = OAuth2Client::create($register_type)->systemFields();
}

if ($do == 'valid_mobile' || $do == 'register' && $register_type == 'mobile') {
	$validate_mobile = OAuth2Client::create('mobile')->validateMobile();
	if (is_error($validate_mobile)) {
		iajax(-1, $validate_mobile['message']);
	}
}

if ($do == 'valid_mobile') {
	iajax(0, '本地校验成功');
}

if ($do == 'register') {
	if(checksubmit() || $_W['ispost'] && $_W['isajax']) {
		$register_user = OAuth2Client::create($register_type)->register();
		if ($register_type == 'system') {
			if (is_error($register_user)) {
				itoast($register_user['message']);
			} else {
				itoast($register_user['message'], url('user/login'));
			}
		}

		if ($register_type == 'mobile') {
			if (is_error($register_user)) {
				iajax(-1, $register_user['message']);
			} else {
				iajax(0, $register_user['message'], url('user/login'));
			}
		}
	}
}

template('user/register');