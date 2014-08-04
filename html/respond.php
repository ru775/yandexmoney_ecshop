<?php
/**
 * ECSHOP
 * ============================================================================
 * Файл выдачи ответов и трансляции запросов для плагинов ECSHOP 2.7.2
 * ============================================================================
 * $Author: zhuwenyuan $
 * $Date: 20013-12-27 17:50:52 +0400 () $
 * $Id: respond.php 14192 2008-02-27 09:50:52Z $
 */

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
require(ROOT_PATH . 'includes/lib_payment.php');
require(ROOT_PATH . 'includes/lib_order.php');
/* коды оплаты */
$pay_code = !empty($_REQUEST['code']) ? trim($_REQUEST['code']) : '';
$st = !empty($_REQUEST['st']) ? trim($_REQUEST['st']) : '';




                             /*
							  $data=file_get_contents('./1.txt');
							  $data.="\r\nGET:";
							  foreach($_GET as $k=>$v) {
							      $data.="$k=$v; ";
							  }
							  $data.="\r\nPOST:";
							  foreach($_POST as $k=>$v) {
							      $data.="$k=$v; ";
							  }
							  $data.="\r\n";
							  $data.="\r\n";
							  file_put_contents('./1.txt',$data);
							*/

if (empty($pay_code))
	{
	   $msg = $_LANG['pay_not_exist'];
	}



////////////////////////////////////////////////////////////////
// robokassa                                                  //
////////////////////////////////////////////////////////////////
 if ($pay_code=='robokassa')
  {
		  if ($pay_code=='robokassa' && $st=='ok') {
		    $msg     = $_LANG['pay_success'];
			  }elseif ($pay_code=='robokassa' && $st=='bad')

			  {
			    $msg     = $_LANG['pay_fail'];
		  }else

		  {
		    /* определяем, включены ли оплаты */
		    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
		    if ($db->getOne($sql) == 0)
		    {
		        $msg = $_LANG['pay_disabled'];
		    }
		    else
		    {
		        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

		        /* проверяем в модуле оплаты данные, оплата прошла корректно или нет */
		        if (file_exists($plugin_file))
		        {
		            /* создаем код в зависимости от завершения оплаты и выводим сообщение клиенту */
		            include_once($plugin_file);

		            $payment = new $pay_code();
		            $msg     = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];
		            /* отправим майл админу, если оплата успешна */
				    if ($_CFG['service_email'] != '')
				    {
				        $tpl = get_mail_template('robokassa_new_payment');
				        $smarty->assign('order', $order);
				        $smarty->assign('shop_name', $_CFG['shop_name']);
				        $smarty->assign('send_date', date($_CFG['time_format']));
				        $content = $smarty->fetch('str:' . $tpl['template_content']);
				        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
				    }


		        }
		        else
		        {
		            $msg = $_LANG['pay_not_exist'];
		        }
		    }


		  }


	}


////////////////////////////////////////////////////////////////
// Webmoney                                              //
////////////////////////////////////////////////////////////////
	if ($pay_code=='wm')
	 {
		  {
		    /* определяем, включены ли оплаты */
		    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
		    if ($db->getOne($sql) == 0)
		    {
		        $msg = $_LANG['pay_disabled'];
		    }
		    else
		    {
		        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

		        /* проверяем в модуле оплаты данные, оплата прошла корректно или нет */
		        if (file_exists($plugin_file))
		        {
		            /* создаем код в зависимости от завершения оплаты и выводим сообщение клиенту */
		            include_once($plugin_file);
		            $payment = new $pay_code();
		            $msg     = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];

				    /* отправим майл админу, если оплата успешна */
				    if ($_CFG['service_email'] != '')
				    {
				        $tpl = get_mail_template('webmoney_new_payment');
				        $smarty->assign('order', $order);
				        $smarty->assign('shop_name', $_CFG['shop_name']);
				        $smarty->assign('send_date', date($_CFG['time_format']));
				        $content = $smarty->fetch('str:' . $tpl['template_content']);
				        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
				    }

		        }
		        else
		        {
		            $msg = $_LANG['pay_not_exist'];
		        }
		    }
		  }
	 }

////////////////////////////////////////////////////////////////
// liqpay                                              //
////////////////////////////////////////////////////////////////
	if ($pay_code=='liqpay2')
	 {
		  {
		    /* определяем, включены ли оплаты */
		    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
		    if ($db->getOne($sql) == 0)
		    {
		        $msg = $_LANG['pay_disabled'];
		    }
		    else
		    {
		        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

		        /* проверяем в модуле оплаты данные, оплата прошла корректно или нет */
		        if (file_exists($plugin_file))
		        {
		            /* создаем код в зависимости от завершения оплаты и выводим сообщение клиенту */
		            include_once($plugin_file);
		            $payment = new $pay_code();
		            $msg     = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];

				    /* отправим майл админу, если оплата успешна */
				    if ($_CFG['service_email'] != '')
				    {
				        $tpl = get_mail_template('liqpay_new_payment');
				        $smarty->assign('order', $order);
				        $smarty->assign('shop_name', $_CFG['shop_name']);
				        $smarty->assign('send_date', date($_CFG['time_format']));
				        $content = $smarty->fetch('str:' . $tpl['template_content']);
				        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
				    }

		        }
		        else
		        {
		            $msg = $_LANG['pay_not_exist'];
		        }
		    }
		  }
	 }
////////////////////////////////////////////////////////////////
// Яндекс деньги                                              //
////////////////////////////////////////////////////////////////
     if ($pay_code=='yad')
    	{
    	/* определяем, включены ли оплаты */
		    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
		    if ($db->getOne($sql) == 0)
		    {
		        $msg = $_LANG['pay_disabled'];
		    }
		    else
    	    {

			    $pay_code = 'yad';
			    $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';
				/* проверяем в модуле оплаты данные, оплата прошла корректно или нет */
	            if (file_exists($plugin_file))
				{
				/* создаем код в зависимости от завершения оплаты и выводим сообщение клиенту */
				include_once($plugin_file);
				$payment = new $pay_code();
				$msg     = ($payment->respond()) ? $_LANG['pay_success'] : $_LANG['pay_fail'];

					/* отправим майл админу, если оплата успешна */
					if ($_CFG['service_email'] != '')
					{
					   $tpl = get_mail_template('yandex_new_payment');
					   $smarty->assign('order', $order);
					   $smarty->assign('shop_name', $_CFG['shop_name']);
					   $smarty->assign('send_date', date($_CFG['time_format']));
					   $content = $smarty->fetch('str:' . $tpl['template_content']);
					   send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
					}

				}
				else
				{
				$msg = $_LANG['pay_not_exist'];
				}

             }
    	}
//**************************************************************
// Z-Pay begin *************************************************
//**************************************************************
if ($pay_code=='zp')
	 {
			if ($pay_code=='zp' && $st=='fail')
			{
			$msg     = $_LANG['pay_fail'];

			}


		  //метод оплаты
		  if ($pay_code=='zp' && $st=='ok') {
		    $msg     = $_LANG['pay_success'];

			  }
           elseif ($pay_code=='zp' && $st=='')
		  {
            //$pay_id = $order['order_sn'];
		    /* проверка формы */
		    if (strpos($pay_code, '?') !== false)
		    {
		        $arr1 = explode('?', $pay_code);
		        $arr2 = explode('=', $arr1[1]);

		        $_REQUEST['code']   = $arr1[0];
		        $_REQUEST[$arr2[0]] = $arr2[1];
		        $_GET['code']       = $arr1[0];
		        $_GET[$arr2[0]]     = $arr2[1];
		        $pay_code           = $arr1[0];
		    }
		    /* отправим майл админу, если оплата успешна */

		    if ($_CFG['service_email'] != '')
		    {
		        $tpl = get_mail_template('zpayment_new_payment');
		        $smarty->assign('order', $order);
		        $smarty->assign('shop_name', $_CFG['shop_name']);
		        $smarty->assign('send_date', date($_CFG['time_format']));
		        $content = $smarty->fetch('str:' . $tpl['template_content']);
		        send_mail($_CFG['shop_name'], $_CFG['service_email'], $tpl['template_subject'], $content, $tpl['is_html']);
		    }


		    /* определяем, включены ли оплаты */
		    $sql = "SELECT COUNT(*) FROM " . $ecs->table('payment') . " WHERE pay_code = '$pay_code' AND enabled = 1";
		    if ($db->getOne($sql) == 0)
		    {
		        $msg = $_LANG['pay_disabled'];
		    }
		    else
		    {
		        $plugin_file = 'includes/modules/payment/' . $pay_code . '.php';

		        /* проверяем в модуле оплаты данные, оплата прошла корректно или нет */
		        if (file_exists($plugin_file))
		        {
		            /* создаем код в зависимости от завершения оплаты и выводим сообщение клиенту $pay_id*/
		            include_once($plugin_file);
                    $pay_id = '1';
                    $pay_am = '1';
		            $payment = new $pay_code();
		            $msg     = ($payment->respond($pay_id,$pay_am)) ? $_LANG['pay_success'] : $_LANG['pay_fail'];
		        }
		        else
		        {
		            $msg = $_LANG['pay_not_exist'];
		        }
		    }
		  }
	 }

// Z-Pay end
assign_template();
$position = assign_ur_here();
$smarty->assign('page_title', $position['title']);
$smarty->assign('ur_here',    $position['ur_here']);
$smarty->assign('page_title', $position['title']);
$smarty->assign('ur_here',    $position['ur_here']);
$smarty->assign('helps',      get_shop_help());

$smarty->assign('message',    $msg);
$smarty->assign('shop_url',   $ecs->url());

$smarty->display('respond.dwt');

?>