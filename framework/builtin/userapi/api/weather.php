<?php
$message = $this->message;

$ret = preg_match('/(.+)天气/i', $this->message['content'], $matchs);
if(!$ret) {
	return $this->respText('请输入合适的格式, 城市+天气, 例如: 北京天气');
}
$city = $matchs[1];
$response = array();

$url = 'http://php.weather.sina.com.cn/xml.php?city=%s&password=DJOYnieT8234jlsK&day=0';
$obj = weather_http_request($url, urlencode(iconv('utf-8', 'gb2312', $city)));
if (!empty($obj) && !empty($obj->Weather)) {
	$data = $obj->Weather->city . '今日天气' . PHP_EOL .
							'今天白天'.$obj->Weather->status1.'，'. $obj->Weather->temperature1 . '摄氏度。' . PHP_EOL .
							$obj->Weather->direction1 . '，' . $obj->Weather->power1 . PHP_EOL .
							'今天夜间'.$obj->Weather->status2.'，'. $obj->Weather->temperature2 . '摄氏度。' . PHP_EOL .
							$obj->Weather->direction2 . '，' . $obj->Weather->power2 . PHP_EOL .
							'==================' . PHP_EOL .
							'【穿衣指数】：' . $obj->Weather->chy_shuoming . PHP_EOL .PHP_EOL .
							'【感冒指数】：' . $obj->Weather->gm_l . $obj->Weather->gm_s . PHP_EOL .PHP_EOL .
							'【空调指数】：' . $obj->Weather->ktk_s . PHP_EOL .PHP_EOL .
							'【污染物扩散条件】：' . $obj->Weather->pollution_l . $obj->Weather->pollution_s . PHP_EOL .PHP_EOL .
							'【洗车指数】：' . $obj->Weather->xcz_l . $obj->Weather->xcz_s . PHP_EOL .PHP_EOL .
							'【运动指数】：' . $obj->Weather->yd_l . $obj->Weather->yd_s . PHP_EOL .PHP_EOL .
							'【紫外线指数】：' . $obj->Weather->zwx_l . $obj->Weather->zwx_s . PHP_EOL .PHP_EOL .
							'【体感度指数】：' . $obj->Weather->ssd_l . $obj->Weather->ssd_s . PHP_EOL ;
} else {
	$url = 'http://www.sojson.com/open/api/weather/xml.shtml?city=%s';
	$obj = weather_http_request($url, $city);
	$data = $obj->city . '今日天气' . PHP_EOL .
						'AQI：' . $obj->environment->aqi  . PHP_EOL .
						'pm25：' . $obj->environment->pm25 . PHP_EOL .
						'空气质量：' . $obj->environment->quality . PHP_EOL .
						'温度：' . $obj->wendu . '摄氏度。' . PHP_EOL .
						'湿度：' . $obj->shidu . PHP_EOL .
						'风级：' . $obj->fengli . PHP_EOL .
						'风向：' . $obj->fengxiang . PHP_EOL .
						'日出时间：' . $obj->sunrise_1 . PHP_EOL .
						'日落时间：' . $obj->sunset_1 . PHP_EOL .
						'建议：' . $obj->environment->suggest . PHP_EOL;

}

$response = $this->respText($data);
return $response;

function weather_http_request($url, $city) {
	$url = sprintf($url, $city);
	$resp = ihttp_get($url);
	if ($resp['code'] == 200 && $resp['content']) {
		$obj = isimplexml_load_string($resp['content'], 'SimpleXMLElement', LIBXML_NOCDATA);
		return $obj;
	}
	return '';
}
