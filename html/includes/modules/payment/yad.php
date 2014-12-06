<?php

if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

$payment_lang = ROOT_PATH . 'languages/' .$GLOBALS['_CFG']['lang']. '/payment/yad.php';

if (file_exists($payment_lang))
{
    global $_LANG;

    include_once($payment_lang);
}

/* The basic modules of information */
if (isset($set_modules) && $set_modules == TRUE)
{
    $i = isset($modules) ? count($modules) : 0;

    /* Code */
    $modules[$i]['code']    = basename(__FILE__, '.php');

    /* Description of the corresponding language */
    $modules[$i]['desc']    = 'yad_desc';

    /* Whether to support */
    $modules[$i]['is_cod']  = '0';

    /* Whether to support the on-line payment */
    $modules[$i]['is_online']  = '1';

    /* Pay */
    $modules[$i]['pay_fee'] = '0.0%';

    /* Author */
    $modules[$i]['author']  = 'ECSHOPRU';

    /* Website */
    $modules[$i]['website'] = 'http://www.ecshoprus.ru';

    /* Version */
    $modules[$i]['version'] = '1.0.0';

    /* Configuration information */
    $modules[$i]['config'] = array(
        array('name' => 'yad_account', 'type' => 'text', 'value' => ''),
        array('name' => 'yad_account2', 'type' => 'text', 'value' => ''),
        array('name' => 'yad_key',     'type' => 'text', 'value' => ''),
        array('name' => 'yad_work', 'type' => 'select', 'value' => ''),
    );

    //проверяем, что нет шаблона и вставляем шаблон темплейта.
    $sql = "SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('mail_templates') . " WHERE template_code = 'yandex_new_payment'";
						    if ($GLOBALS['db']->getOne($sql) == 0)
						    {
						       $sql = "INSERT INTO " . $GLOBALS['ecs']->table('mail_templates')."(`template_id`, `template_code`, `is_html`, `template_subject`, `template_content`, `last_modify`, `last_send`, `type`) VALUES".
                               " ('', 'yandex_new_payment', 0, 'Оплата YandexMoney', 'Уважаемый менеджер оплачен товар по системе Yandex Money', 0, 0, 'template');";

                               $result = $GLOBALS['db']->query($sql);
						    }

    return;
}

/**
 * Class
 */
class yad
{
    /**
     * Constructors
     *
     * @access  public
     * @param
     *
     * @return void
     */
    function yad()
    {
    }

    function __construct()
    {
        $this->yad();
    }

    /**
     * Generated to pay code
     * @param   array   $order      Order Information
     * @param   array   $payment    Payment Information
     */
    function get_code($order, $payment)
    {
        $data_vid           = trim($payment['yad_account']);
        $data_orderid       = $order['order_id'];
        $data_vamount       = $order['order_amount'];
        $mch_name = $GLOBALS['_CFG']['shop_name'];  //магазина название

       	$sql = "SELECT pay_config FROM " . $GLOBALS['ecs']->table('payment') ."WHERE pay_code = 'YAD'";
		$get_pay_config = $GLOBALS['db']->getOne($sql);
        $store = unserialize($get_pay_config);
	       $code_list = array();
	       foreach ($store as $key=>$value)
	       {
	       $code_list[$value['name']] = $value['value'];
	       }


        $real_method = $payment['yad_work'];
        switch ($real_method){
            case 'merchant':
                $def_url  = '<br /><form style="text-align:center;" method=post action="https://money.yandex.ru/eshop.xml" target="_blank">';
                break;
            case 'test':
                $def_url  = '<br /><form style="text-align:center;" method=post action="https://demomoney.yandex.ru/eshop.xml" target="_blank">';
                break;

        }


        $def_url .= "<input class=\"wide\" name=\"scid\" value=\"".$code_list['yad_account2']."\" type=\"hidden\">";
        $def_url .= "<input type=\"hidden\" name=\"ShopID\" value=\"".$code_list['yad_account']."\">";
        $def_url .= "<input type=\"hidden\" name=\"Sum\" value=\"$data_vamount\">";
        $def_url .= "<select name=\"paymentType\">".
            "<option value=\"PC\">Со счета в Яндекс.Деньгах</option>".
            "<option value=\"AC\">С банковской карты</option>".
            "<option value=\"GP\">По коду через терминал</option>".
            "<option value=\"WM\">Оплата WebMoney</option>".
            "<option value=\"MC\">Платеж со счета мобильного телефона</option>".
            "<option value=\"SB\">Выставление счета в Сбербанк Онлайн</option>".
            "<option value=\"AB\">Выставление счета в АльфаКлик</option>".
        "</select>";
        $def_url .= "<input type=\"hidden\" name=\"CustomerNumber\" value=\"".$order['log_id']."\">";
        $def_url .= "<input type=\"hidden\" name=\"orderNumber\" value=\"".$order['order_sn']."\">";
        $def_url .= "<input type=\"hidden\" name=\"cms_name\" value=\"ecshop\">";
        $def_url .= "<input type=submit value='" .$GLOBALS['_LANG']['pay_button']. "'>";
        $def_url .= "</form>";

        return $def_url;
    }

    /**
     * Response operation
     */

 function respond()
 {
            $dt = new DateTime("now");
            $payment        = get_payment(basename(__FILE__, '.php'));

            $sql = "SELECT pay_config FROM " . $GLOBALS['ecs']->table('payment') ."WHERE pay_code = 'yad'";
		    $get_pay_config = $GLOBALS['db']->getOne($sql);
		    $store = unserialize($get_pay_config);
		        $code_list = array();
		        foreach ($store as $key=>$value)
		        {
		         $code_list[$value['name']] = $value['value'];
		        }



          //проверка корректности данных  CustomerNumber
	      $customerNumber2=$_REQUEST['orderNumber'];
	      $log_id=$_REQUEST['customerNumber'];
          $orderSumAmount1=$_REQUEST['orderSumAmount'];
          $invoiceId1=$_REQUEST['invoiceId'];
          $shopId1=$_REQUEST['shopId'];
          $pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
          $st = !empty($_REQUEST['st']) ? trim($_REQUEST['st']) : '';

          //проверка MD5
          $real_method = $payment['yad_work'];
	        switch ($real_method){
	            case 'merchant':
	                $orderSumCurrencyPaycash1='643';
		            $orderSumBankPaycash1='1001';
	                break;
	            case 'test':
	                $orderSumCurrencyPaycash1='10643';
		            $orderSumBankPaycash1='1003';
	                break;

	        }



		    if (check_money($log_id, $orderSumAmount1))
	        {
	        $myMD5=$_REQUEST['action'].';'.
		    $_REQUEST['orderSumAmount'].';'.
		    $orderSumCurrencyPaycash1.';'.
		    $orderSumBankPaycash1.';'.
		    trim($code_list['yad_account']).';'.
		    $_REQUEST['invoiceId'].';'.
		    $_REQUEST['customerNumber'].';'.
		    $code_list['yad_key'];
		    if (strtoupper(md5($myMD5))==$_REQUEST['md5']) {$md5Check=1;} else {$md5Check=0;}
	        } else {$md5Check=0;}

          if ($pay_code=='yad' && $st=='check')
		  {

			   // попали сюда после того как проверили, что совпадает номер и сумма перевода.
			   if ($pay_code=='yad' && $st=='check' && $md5Check==1)
			    {   //так как запись о покупке есть выдаем ответ яндексу о том, что параметры совпали.
						#################################################################
						# Header   Europe/Moscow     Europe/London
						#################################################################
						header("Content-Type: text/xml; charset=UTF-8");
						header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
						header("Cache-Control: no-store, no-cache, must-revalidate");
						header("Cache-Control: post-check=0, pre-check=0", false);
						header("Pragma: no-cache");
						echo '<?xml version="1.0" encoding="UTF-8"?><checkOrderResponse performedDatetime="'.$dt->format(DATE_W3C).'" code="0" invoiceId="'.$invoiceId1.'" shopId="'.$shopId1.'"/>';
						die();
			    }
				else
				{
						#################################################################
						# Header   Europe/Moscow     Europe/London
						#################################################################
						header("Content-Type: text/xml; charset=UTF-8");
						header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
						header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
						header("Cache-Control: no-store, no-cache, must-revalidate");
						header("Cache-Control: post-check=0, pre-check=0", false);
						header("Pragma: no-cache");
						echo '<?xml version="1.0" encoding="UTF-8"?><checkOrderResponse performedDatetime="'.$dt->format(DATE_W3C).'" code="100" invoiceId="'.$invoiceId1.'" shopId="'.$shopId1.'" message="Ошибка в заказе" techMessage="Неверный номер заказа или сумма"/>';
						die();
				}

		  }

		  //****************************************************************************************************************************************************
		    if ($pay_code=='yad' && $st=='aviso')
		  {
		          if ($pay_code=='yad' && $st=='aviso' && $md5Check==1)
				  {
          //#################################################################
					//# Header   Europe/Moscow     Europe/London
					//#################################################################
					header("Content-Type: text/xml; charset=UTF-8");
					header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
					header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
					header("Cache-Control: no-store, no-cache, must-revalidate");
					header("Cache-Control: post-check=0, pre-check=0", false);
					header("Pragma: no-cache");
                    echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.$dt->format(DATE_W3C).'" code="0" invoiceId="'.$invoiceId1.'" shopId="'.$shopId1.'"/>';
                    /* отправим майл админу, если оплата успешна */
                    $action_note='test';
                    order_paid($log_id,PS_PAYED,$action_note);//оплачиваем товар
                    die();

					}

				    else

				  {
					#################################################################
					# Header   Europe/Moscow     Europe/London
					#################################################################
					header("Content-Type: text/xml; charset=UTF-8");
					header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
					header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
					header("Cache-Control: no-store, no-cache, must-revalidate");
					header("Cache-Control: post-check=0, pre-check=0", false);
					header("Pragma: no-cache");
					echo '<?xml version="1.0" encoding="UTF-8"?><paymentAvisoResponse performedDatetime="'.$dt->format(DATE_W3C).'" code="1" invoiceId="'.$invoiceId1.'" shopId="'.$shopId1.'" message="Ошибка в заказе 1'.$code_list[yad_account].'2" techMessage="Неверный номер заказа"/>';
					die();
				  }
            }







          if ($pay_code=='yad' && $st=='ok')
		  {
		    //echo $md5Check.'myMD-'.$myMD5.'  sitemd-'.$_REQUEST['md5'];
            return true;
		  }

          if ($pay_code=='yad' && $st=='bad')
		  {
            return false;
		  }
 }
}

?>