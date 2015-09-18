<?php
if (!defined('BASEPATH')) exit('No direct script access allowed');

Class Kaznachey{
	
	private $id;
	private $currency = 'UAH';
	private $response = '';
	private $status = '';
	private $exp_time = FALSE;
	private $sig = '';
	private $lang = 'RU';
	private $ci;
	
	public function __construct()
	{
		$this->ci =& get_instance();
	}
	
	public function _init($data)
	{
		$this->id = $data['id'];
		$this->sig = $data['sig'];
	}
	
	public function pay($data){
		$url = "http://payment.kaznachey.net/api/PaymentInterface/CreatePayment";
		
		$this->response = base_url().$data['lang'].$this->response;
		$this->status = base_url().$data['lang'].'/status/'.$data['order_id'];
		
		 $payment = Array(
			//Обязательные поля
		   "EMail" => $data['email'], //Емайл клиента
		   "PhoneNumber" => $data['phone'], //Номер телефона клиента
		   
		   "MerchantInternalPaymentId" => $data['order_id'],// Номер платежа в системе мерчанта
		   "MerchantInternalUserId" => "", //Номер пользователя в системе мерчанта
		   
		   "StatusUrl" => $this->response,// url для ответа платежного сервера с состоянием платежа.
		   "ReturnUrl" => $this->status, //url возврата ползователя после платежа.
		   
			//По возможности нужно заполнить эти поля.
		   "CustomMerchantInfo" => "",// Любая информация
		   "BuyerCountry" => "",//Страна
		   "BuyerFirstname" => $data['name'],//Имя,
		   "BuyerPatronymic" => "",// отчество
		   "BuyerLastname" => "",//Фамилия
		   "BuyerStreet" => "",// Адрес
		   "BuyerZone" => "",//   Область
		   "BuyerZip" => "",//  Индекс
		   "BuyerCity" => "",//   Город,
		   
		   //аналогичная информация о доставке. Если информация совпадает можно скопировать.
		   "DeliveryFirstname" => $data['name'],// 
		   "DeliveryPatronymic" => "",//
		   "DeliveryLastname" => "",//
		   "DeliveryZip" => "",//     
		   "DeliveryCountry" => "",//   
		   "DeliveryStreet" => "",//   
		   "DeliveryCity" => "",//      ,
		   "DeliveryZone" => "",//      0,
		);
				
		//Формируем подпись.
		$signature =
				strtoupper($this->id).
				number_format(($data['amount'] * $data['curs']), 2, '.', '').//Общаяя сумма. Внимание сумма должна быть в формать 123.23 Дробная часть отделяется точкой и не иметь лишних нулей!
				number_format($data['all'], 2, '.', '').//колличество товара
				''. //Идентификатор пользователея в системе мерчента. Исспользуется для анализа в системе Казначей
				$data['order_id']. //Идентификатор платежа в системе мерчента. Исспользуется для анализа в системе Казначей
				$data['system']. //Идентификатор выбранной платежной системы.
				strtoupper($this->sig); //Секретный ключ мерчанта
		
		$signature = md5($signature);
		
		$request = Array(
			"SelectedPaySystemId" => $data['system'],//Выбранная платёжная система
			"Products" => $data['products'],// Продукты
			"PaymentDetails" => $payment, //Детали платежа
			"Signature" => $signature, //Подпись
			"MerchantGuid" => $this->id //Идентификатор мерчанта
		);
		
		unset($signature);
		unset($payment);
		unset($data);
		
		$json = json_encode($request);
		unset($request);
		
		// Отправляем запрос на сервер.
		$res = $this->sendRequest($url, $json);
		unset($json);
		
		$result = json_decode($res, true);
		if(!empty($result)){
			if($result["ErrorCode"] == 0){
				return $result["ExternalForm"];
			}
		}
		return false;
	}
	
	public function system(){
		// Ссылка для получения информации о клиенте
		$url = "http://payment.kaznachey.net/api/PaymentInterface/GetMerchatInformation";
		
		
		//Формируем массив запроса
		$request = Array(
			// Идентификатор мерчанта
			"MerchantGuid" => $this->id,
			
			//  Формируем подпись запроса md5 ({Идентификатор мерчанта}.{секретный ключ мерчанта})
			"Signature"=>md5(strtoupper($this->id).strtoupper($this->sig))
		);
			
		//Создаём запрос в виде JSON
		// делаем запрос к мерчанту
		$json = json_encode($request);
		
		if(false != ($res = json_decode($this->sendRequest($url ,$json),true))){
			if(!empty($res['PaySystems'])){
				return array('system' => $res['PaySystems'], 'link' => $res['TermToUse']);
			}
			return false;
		}
		return false;
	}
	
	function response(){
		$data = file_get_contents("php://input");
		
		if(!empty($data)){
			$data = json_decode($data, true);

			if(!empty($data)){
				if(isset($data['ErrorCode'])){
					$sum = $data['ErrorCode'].$data['MerchantInternalPaymentId'].$data['MerchantInternalUserId'].number_format($data['Sum'], 2, '.', '').$data['CustomMerchantInfo'].'4BEF0604-131C-46D5-8AF6-21CBBDFEFB35';
					$sum = md5($sum);
					
					if($data['Signature'] == $sum){
						/*if($data['ErrorCode'] != 0){
							return false;
						}*/
						return array(
							'id' => $data['MerchantInternalPaymentId'],
							'error' => $data['ErrorCode']
						);
					}
				}
			}
		}
		return false;
	}
	
	function sendRequest($url, $data, $post = true)
    {
        $curl =curl_init();
        if (!$curl)
            return false;

        curl_setopt($curl, CURLOPT_URL,$url);
        if($post != false){
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Expect: ","Content-Type: application/json; charset=UTF-8",'Content-Length: '.strlen($data)));
			curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
		}
        curl_setopt($curl, CURLOPT_RETURNTRANSFER,True);
       // curl_setopt($curl, CURLOPT_TIMEOUT, 3);
       // curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 3);
        $res =  curl_exec($curl);
        curl_close($curl);
		
		return $res;
    }
    
    function currency($currency = "rub"){
		//$url = "https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5";
		
		//$data = file_get_contents($url);
		
		//if(!empty($data)){
			if($currency == 'rub'){
				$data = $this->ci->db->query("SELECT `value` FROM `curs` WHERE `currency` = 'RUR' AND `date` >= DATE_ADD(NOW(), INTERVAL -6 HOUR)")->row();
				if(!empty($data)){
					return $data->value;
				}
				
				$data = $this->sendRequest('http://www.bank.gov.ua/control/ru/curmetal/detail/currency?period=daily', '', false);
				if(!empty($data)){
					preg_match('#<td class="attribute">10&nbsp;Російських рублів<\/td>.*?</tr>#is', $data, $arr);
					
					if(isset($arr[0])){
						preg_match_all('#<td class="value" nowrap="nowrap">(.*?)</td>#is', $arr[0], $arr);

						if(isset($arr[1][0])){
							$currency = (float)$arr[1][0];
							$currency = ($currency / 10);
							
							$this->ci->db->update('curs', array('value', $currency), array('currency' => 'RUR'));
							return $currency;
						}
					}
				}
				$data = $this->ci->db->query("SELECT `value` FROM `curs` WHERE `currency` = 'RUR'")->row();
				if(!empty($data)){
					return $data->value;
				}
			}else{
				$data = $this->ci->db->query("SELECT `value` FROM `curs` WHERE `currency` = 'EUR' AND `date` >= DATE_ADD(NOW(), INTERVAL -6 HOUR)")->row();
				if(!empty($data)){
					return $data->value;
				}
				$data = $this->sendRequest('http://www.bank.gov.ua/control/ru/curmetal/detail/currency?period=daily', '', false);
				if(!empty($data)){
					preg_match('#<td class="attribute">100&nbsp;Євро<\/td>.*?</tr>#is', $data, $arr);
					
					if(isset($arr[0])){
						preg_match_all('#<td class="value" nowrap="nowrap">(.*?)</td>#is', $arr[0], $arr);

						if(isset($arr[1][0])){
							$currency = (float)$arr[1][0];
							$currency = ($currency / 100);
							
							$this->ci->db->update('curs', array('value', $currency), array('currency' => 'EUR'));
							return $currency;
						}
					}
				}
				$data = $this->ci->db->query("SELECT `value` FROM `curs` WHERE `currency` = 'EUR'")->row();
				if(!empty($data)){
					return $data->value;
				}
			}
		//}
		return 0;
	}
    
    function convert($price, $currency = "rub"){
		$new = 0;
		$url = "https://api.privatbank.ua/p24api/pubinfo?exchange&coursid=5";
		
		$data = file_get_contents($url);
		
		if(!empty($data)){
			if($currency == 'rub'){
				if(preg_match("/ccy=\"RUR\" base_ccy=\"UAH\" buy=\"(.*?)\"/i", $data, $m)){
					if(isset($m[1])){
						$new = $price * $m[1];
					}
				}
			}else{
				if(preg_match("/ccy=\"EUR\" base_ccy=\"UAH\" buy=\"(.*?)\"/i", $data, $m)){
					if(isset($m[1])){
						$new = $price * $m[1];
					}
				}
			}
		}
		return $new;
	}
}
