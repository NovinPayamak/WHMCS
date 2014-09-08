<?php

	if (!defined('WHMCS'))
	{
		exit('This file cannot be accessed directly');
	}

	if (!function_exists('SendSM'))
	{
<<<<<<< HEAD
		function SendSM($gateway, $message){
			
			if(strlen($message['numbers']) == 10 )
				$message['numbers'] = '0'.$message['numbers'];
				
			$client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8'));
			$flash = false;
			$res = $client->Send(
				array(
					'Auth' 	=> array('number' => $gateway['number'],'pass' => $gateway['password']),
					'Recipients' => array($message['numbers']),
					'Message' => array($message['content']),
					'Flash' => $flash
					)
				);
			
			return $res->Status;
			
			
		}
=======
		function SendSMS($gateway, $message){
			if(empty($message['flash'])) $message['flash'] = false;
		$sms_client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
			return $sms_client->Send(array(
				'Auth' => array('number' => $gateway['number'],'pass' => $gateway['pass']),
				'Recipients' => array($message['numbers']),
				'Message' => array($message['content']),
				'Flash' => $message['flash']
			));
	}
>>>>>>> origin/master
	}

	if (!function_exists('GetSQLValueString'))
	{
		function GetSQLValueString($theValue, $theType, $theDefinedValue = '', &$theNotDefinedValue = '')
		{
			$theValue = (get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue);
			$theValue = (function_exists('mysql_real_escape_string') ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue));
			switch ($theType)
			{
				case 'text':
				{
					$theValue = ($theValue != '' ? '\'' . $theValue . '\'' : 'NULL');
					break;
				}
				case 'long':
				{
				}
				case 'int':
				{
					$theValue = ($theValue != '' ? intval($theValue) : 'NULL');
					break;
				}
				case 'double':
				{
					$theValue = ($theValue != '' ? '\'' . doubleval($theValue) . '\'' : 'NULL');
					break;
				}
				case 'date':
				{
					$theValue = ($theValue != '' ? '\'' . $theValue . '\'' : 'NULL');
					break;
				}
				case 'defined':
				{
					$theValue = ($theValue != '' ? $theDefinedValue : $theNotDefinedValue);
				}
			}
			return $theValue;
		}
	}

	if (isset($_GET['clearlog']))
	{
		mysql_query('TRUNCATE TABLE mod_smsaddon5_logs');
		header('Location: addonmodules.php?module=sms_addon&logs');
		exit();
	}
	if (isset($_GET['SendSingleSms']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		
		if ($_POST['customer'] == 'none')
		{
			$recipient_number = $_POST['recipient'];
			$customer = 'هيچ يک (ارسال تکی)';
		}
		else
		{
			$exploded       = explode('|', $_POST['customer']);
			$recipient_number = $exploded[0];
			$customer = $exploded[1];
		}
		if ($recipient_number == '')
		{
			$time              = time();
			$_SESSION['error'] = 'فاقد شماره موبايل';
			$error             = 1;
		}
		if ($error != 1)
		{
			$gateway['username']  = $row_mod['username'];
			$gateway['password']    = $row_mod['password'];
			$gateway['number']    = $row_mod['number'];
			$message['numbers'] = $recipient_number;
			$message['content'] = $_POST['content'];

			$response = SendSM($gateway, $message);
			$time     = time();
			$_SESSION['error'] = 'ارسال با موفقيت انجام شد.<br />';
			mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . $time . '\', \'' . $customer . '\', \'' . $recipient_number . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $_POST['content']) . '\')');
		}
		header('Location: addonmodules.php?module=sms_addon&error');
		exit();
	}
	
	if (isset($_GET['SendMassSms']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);

		if ((!isset($_POST['content']) || $_POST['content'] == ''))
		{
			$content = $_SESSION['content'];
			$force = $_SESSION['force'];
		}
		else
		{
			$content             = $_POST['content'];
			$force             = $_POST['force'];
			$_SESSION['content'] = $_POST['content'];
			$_SESSION['force'] = $_POST['force'];
		}
		if (($content == '' || $force == ''))
		{
			$_SESSION['masssmserror'] = 'پيامک خالی قابل ارسال نمی باشد';
			header('Location: addonmodules.php?module=sms_addon&masssms');
			exit();
		}
		
		$t = 0;
		if($_SESSION['first'] > 0)
			$page = $_SESSION['page'] - 0;
		else
			$page = 0;
		$_SESSION['first'] ++;
		$start                = $page * 20;
		$querylimit           = ' LIMIT ' . $start . ',20';
		
		$tel             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
		$row_tel         = @mysql_fetch_array($tel);
		$report             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
		$row_report         = @mysql_fetch_array($report);
		if ($row_tel['id'] != '' && $row_report['id'] != '')
		{
			$customers            = @mysql_query('SELECT id FROM tblclients' . $querylimit);
			$all_customers        = @mysql_query('SELECT id FROM tblclients');
			$total = mysql_num_rows($all_customers);
						
			while($item_user = mysql_fetch_array($customers))
			{
				$show = mysql_query( "SELECT * FROM `tblcustomfieldsvalues` WHERE `fieldid`='".$row_tel['id']."' AND `relid`='".$item_user['id']."' " );
				$item = mysql_fetch_array( $show );
				if($item['value'] > 0)
				{
					$gateway['password']    = $row_mod['password'];
					$gateway['number']    = $row_mod['number'];
					$message['numbers'] = $item['value'];
					$message['content'] = $content . $row_mod['businessname'];
<<<<<<< HEAD
					$response = SendSM( $gateway, $message );
					mysql_query("INSERT INTO `mod_smsaddon5_logs` (`time`,`client`,`mobilenumber`,`result`,`text`) VALUES('".time(  )."','".$item_user['id']."','".$item['value']."','".$response."','".$content . $row_mod['businessname']."')");
=======

					$responseA = SendSMS($gateway, $message);
					$response = $responseA->Status;
					mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_customers['id'] . '\', \'' . $row_tels['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $content . $row_mod['businessname']) . '\')');
					continue;
>>>>>>> origin/master
				}
				$t++;
			}
		}
		
		$_SESSION['page'] = $page + 1;
		if($total == $t)
		{
			$_SESSION['masssmserror']= 'تمام پیامک ها ارسال شدند.';
			$_SESSION['first'] = 0;
			$_SESSION['page'] = 0;
		}
		else
			$_SESSION['masssmserror'] = 'تعداد پیامک های ارسالی: ' .$t.'<br>برای ادامه ارسال بر روی دکمه ارسال کلیک نمایید.';
		header('Location: addonmodules.php?module=sms_addon&masssms');
		exit();
		
	}
	if (isset($_GET['cleanup']))
	{
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5`');
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5_codes`');
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5_logs`');
		header('Location: addonmodules.php?module=sms_addon');
		exit();
	}
	if (isset($_GET['next']))
	{
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5`');
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5_codes`');
		mysql_query('DROP TABLE IF EXISTS `mod_smsaddon5_logs`');

		if (!(mysql_query('CREATE TABLE `mod_smsaddon5` (
		  `id` bigint(255) NOT NULL auto_increment,
		  `new_bill` tinyint(1) NOT NULL default \'0\',
		  `changepass` tinyint(1) NOT NULL default \'0\',
		  `orders` tinyint(1) NOT NULL default \'0\',
		  `newticket` tinyint(1) NOT NULL default \'0\',
		  `ticketreply` tinyint(1) NOT NULL default \'0\',
		  `ordersadmin` tinyint(1) NOT NULL default \'0\',
		  `newticketadmin` tinyint(1) NOT NULL default \'0\',
		  `ticketreplyadmin` tinyint(1) NOT NULL default \'0\',
		  `clientadd` tinyint(1) NOT NULL default \'0\',
		  `clientaddadmin` tinyint(1) NOT NULL default \'0\',
		  `payclient` tinyint(1) NOT NULL default \'0\',
		  `payadmin` tinyint(1) NOT NULL default \'0\',
		  `adminmobile` longtext,
		  `businessname` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `sender` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `mobilenumberfield` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `notificationfield` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `username` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `password` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `number` longtext,
		  `no_area` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `senderforce` tinyint(1) NOT NULL default \'0\',
		  `modulecreate` tinyint(1) NOT NULL default \'0\',
		  `modulecreatetext` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `passwordchangetxt` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `clientaddtxtclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `clientaddtxtadmin` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `clientpaytxtclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `adminpaytxtclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ticketopentxtclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ticketopentxtadmin` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ticketreplytext` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ticketreplytextadmin` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ordertextclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `ordertextadmin` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `invoicetextclient` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `numbercorrection` tinyint(1) NOT NULL default \'0\',
		  `countrycode` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `leadingzeros` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `logsperpage` bigint(255) default \'50\',
		  `domainxdays` bigint(255) default \'0\',
		  `domainxdaystext` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `modulesuspend` tinyint(1) NOT NULL default \'0\',
		  `modulesuspendtext` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `dueinvoice` tinyint(1) NOT NULL default \'0\',
		  `dueinvoicetext` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `urgency1` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `urgency2` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  `urgency3` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM')))
		{
		exit(mysql_error());
		(bool) true;
		}
		mysql_query('CREATE TABLE `mod_smsaddon5_logs` (
		`id` bigint(255) NOT NULL auto_increment,
		`time` longtext NOT NULL,
		`client` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		`mobilenumber` longtext,
		`result` longtext NOT NULL,
		`text` longtext CHARACTER SET utf8 COLLATE utf8_persian_ci,
		PRIMARY KEY (`id`)
		) ENGINE=MyISAM');
		if (!(mysql_query('INSERT INTO `mod_smsaddon5` (`new_bill`, `changepass`, `orders`, `newticket`, `ticketreply`, `ordersadmin`, `newticketadmin`, `ticketreplyadmin`, `adminmobile`, `sender`, `mobilenumberfield`, `notificationfield`, `username`, `password`, `no_area`, `senderforce`, `modulecreate`, `modulecreatetext`, `passwordchangetxt`, `ticketopentxtclient`, `ticketopentxtadmin`, `ticketreplytext`, `ticketreplytextadmin`, `ordertextclient`, `ordertextadmin`, `invoicetextclient`, `numbercorrection`, `countrycode`, `leadingzeros`, `logsperpage`, `domainxdays`, `domainxdaystext`, `modulesuspend`, `modulesuspendtext`, `dueinvoice`, `dueinvoicetext`, `urgency1`, `urgency2`, `urgency3`) VALUES (0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, \'No\', 0, 0, \'سرويس {domain} فعال گرديده است. لطفا ايميل خود را چک کنيد.\', \'کلمه عبور شما به روزرسانی گرديد. آدرس ايميل شما: {emailaddress}. کلمه رمز: {password}.\', \'کاربر محترم {clientname} تيکت شما با عنوان {subject} دريافت گرديده و به زودی پاسخ داده شده و به روز رسانی می گردد.\', \'کاربر با نام {clientname} در شاخه {department} اقدام به بازکردن تيکت جديد با عنوان {subject} نموده است.\', \'تيکت با عنوان {subject} به روز رسانی گرديده است. لطفا جهت بررسی به حيطه کاربری خود وارد شويد.\', \'کاربر با نام {clientname} در شاخه {department} اقدام به ارسال پاسخ در تيکت با عنوان {subject} نموده است.\', \'کاربر گرامی از سفارش شما به ارزش {amount}  ریال سپاس گزاريم. تاريخ سررسيد سفارش {duedate}.\', \'سفارش جديد به ارزش {amount} به تاريخ سررسيد {duedate} ثبت گرديده است.\', \'صورت حساب جديدی با تاريخ سررسيد {duedate} و به ارزش {amount} برای شما ايجاد گرديده است.\', 0, NULL, NULL, 50, 0, \'{domain} طی {remainingdays} روز آينده منقضی ميگردد.\', 0, \'{domain} به وضعيت تعليق در آمده است. لطفا جهت رفع هرچه سريعتر مشکل با ما تماس بگيريد.\', 1, \'ُسفارش شما به ارزش {amount} در تاريخ {duedate} منقضی گرديد.\', \'1\', \'1\', \'1\');')))
		{
			exit(mysql_error());
			(bool) true;
		}

		header('Location: addonmodules.php?module=sms_addon&settings&firstuse');
		exit();
	}

	if (isset($_GET['dosettings']))
	{
		mysql_query('UPDATE mod_smsaddon5 SET username=\'' . $_POST['username'] . '\', password=\'' . $_POST['password'] . '\', number=\'' . $_POST['number'] . '\', senderforce=\'' . $_POST['senderforce'] . '\'');
		if ($_POST['git'] == 'anamenu')
		{
			header('Location: addonmodules.php?module=sms_addon');
		}
		else
		{
			header('Location: addonmodules.php?module=sms_addon&modifysettings&firstuse');
		}
		exit();
	}
	if (isset($_GET['mdosettings']))
	{
		mysql_query('UPDATE mod_smsaddon5 SET new_bill=\'' . $_POST['new_bill'] . '\', changepass=\'' . $_POST['changepass'] . '\', orders=\'' . $_POST['orders'] . '\', payclient=\'' . $_POST['payclient'] . '\', payadmin=\'' . $_POST['payadmin'] . '\', clientadd=\'' . $_POST['clientadd'] . '\', clientaddadmin=\'' . $_POST['clientaddadmin'] . '\', newticket=\'' . $_POST['newticket'] . '\', ticketreply=\'' . $_POST['ticketreply'] . '\', ordersadmin=\'' . $_POST['ordersadmin'] . '\', newticketadmin=\'' . $_POST['newticketadmin'] . '\', ticketreplyadmin=\'' . $_POST['ticketreplyadmin'] . '\', adminmobile=\'' . $_POST['adminmobile'] . '\', businessname=' . GetSQLValueString($_POST['businessname'], 'text') . ', mobilenumberfield=\'' . $_POST['mobilenumberfield'] . '\', notificationfield=' . GetSQLValueString($_POST['notificationfield'], 'text') . ', clientpaytxtclient=' . GetSQLValueString($_POST['clientpaytxtclient'], 'text') . ', adminpaytxtclient=' . GetSQLValueString($_POST['adminpaytxtclient'], 'text') . ', clientaddtxtclient=' . GetSQLValueString($_POST['clientaddtxtclient'], 'text') . ', clientaddtxtadmin=' . GetSQLValueString($_POST['clientaddtxtadmin'], 'text') . ', no_area=' . GetSQLValueString($_POST['no_area'], 'text') . ', modulecreate=\'' . $_POST['modulecreate'] . '\', modulecreatetext=' . GetSQLValueString($_POST['modulecreatetext'], 'text') . ', passwordchangetxt=' . GetSQLValueString($_POST['passwordchangetxt'], 'text') . ', ticketopentxtclient=' . GetSQLValueString($_POST['ticketopentxtclient'], 'text') . ', ticketopentxtadmin=' . GetSQLValueString($_POST['ticketopentxtadmin'], 'text') . ', ticketreplytext=' . GetSQLValueString($_POST['ticketreplytext'], 'text') . ', ticketreplytextadmin=' . GetSQLValueString($_POST['ticketreplytextadmin'], 'text') . ', ordertextclient=' . GetSQLValueString($_POST['ordertextclient'], 'text') . ', ordertextadmin=' . GetSQLValueString($_POST['ordertextadmin'], 'text') . ', invoicetextclient=' . GetSQLValueString($_POST['invoicetextclient'], 'text') . ', countrycode=' . GetSQLValueString($_POST['countrycode'], 'text') . ', logsperpage=\'' . $_POST['logsperpage'] . '\', domainxdays=\'' . $_POST['domainxdays'] . '\', domainxdaystext=' . GetSQLValueString($_POST['domainxdaystext'], 'text') . ', modulesuspend=\'' . $_POST['modulesuspend'] . '\', modulesuspendtext=' . GetSQLValueString($_POST['modulesuspendtext'], 'text') . ', dueinvoice=\'' . $_POST['dueinvoice'] . '\', dueinvoicetext=' . GetSQLValueString($_POST['dueinvoicetext'], 'text') . ', urgency1=\'' . $_POST['urgency1'] . '\', urgency2=\'' . $_POST['urgency2'] . '\', urgency3=\'' . $_POST['urgency3'] . '\'') or die(mysql_error());
		header('Location: addonmodules.php?module=sms_addon');
		exit();
	}
	if (isset($_GET['settings']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($row_mod['id'] > 0)
		{
			echo '
				<style>
				fieldset{
					-moz-border-radius: 5px 5px 5px 5px;
					-webkit-border-radius: 5px 5px 5px 5px;
					border-radius: 5px 5px 5px 5px;
					border: 4px solid #ccc;
					padding: 5px;
					margin: 10px 0;
					direction: rtl;
					font-family: Tahoma;
					text-align: right;
				}
				
				fieldset *{
					font-family: Tahoma;
				}

				fieldset legend{
					padding: 5px 30px;
					background-color: #ccc;
					font-weight: 900;
					margin-right: 14px;
				}
				</style>
				<form action=\'addonmodules.php?module=sms_addon&dosettings\' method=\'post\'>
					<fieldset>
						<legend>تنظيمات درگاه</legend>
					<table width=\'70%\' border=\'0\' id="Gateway" cellpadding="5" cellspacing="5">
					<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'> کلمه عبور:</td>
					<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'password\' name=\'password\' value=\'' . $row_mod['password'] . '\'></td></tr>
					<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>شماره ارسال :</td>
					<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'text\' name=\'number\' value=\'' . $row_mod['number'] . '\'></td></tr>
					<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>ارسال به عنوان Flash:</td>
					<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'senderforce\' value=\'1\'';

			if ($row_mod['senderforce'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'senderforce\' value=\'0\'';
			if ($row_mod['senderforce'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /></td></tr>';
			
			echo '<table width=\'70%\' border=\'0\'>';
			echo '<tr><td colspan=\'2\' align=\'center\'><input type=\'submit\' value=\'ذخيره تنظيمات\'>';
			if (!isset($_GET['firstuse']))
			{
				echo '<input type="hidden" name="git" value="anamenu"> <input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';">';
			}
			echo '</td></tr>
			</table>
			</fieldset>
			</form>';
		}
		else
		{
			echo 'نصب افزونه با مشکل مواجه شده است. برای نصب مجدد <a href=\'addonmodules.php?module=sms_addon&cleanup\'>اينجا</a> کليک کنيد.';
			return 1;
		}
	}
	
	if (isset($_GET['modifysettings']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($row_mod['id'] > 0)
		{
			echo '
			<style>
				fieldset{
					-moz-border-radius: 5px 5px 5px 5px;
					-webkit-border-radius: 5px 5px 5px 5px;
					border-radius: 5px 5px 5px 5px;
					border: 4px solid #ccc;
					padding: 5px;
					margin: 10px 0;
					direction: rtl;
					font-family: Tahoma;
					text-align: right;
				}
				
				fieldset *{
					font-family: Tahoma;
				}

				fieldset legend{
					padding: 5px 30px;
					background-color: #ccc;
					font-weight: 900;
					margin-right: 14px;
				}
			</style>
			<fieldset>
				<legend>تنظيمات ماژول</legend>
			<form action=\'addonmodules.php?module=sms_addon&mdosettings\' method=\'post\'>
			<table width=\'70%\' border=\'0\' cellspacing="5" cellpadding="5">
			<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>نام فيلد شماره موبايل:</td>';
			$al           = mysql_query('SELECT fieldname FROM tblcustomfields WHERE type=\'client\' AND fieldtype=\'text\'');
			$totalRows_al = mysql_num_rows($al);
			if ($totalRows_al == 0)
			{
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;background:#f58c8c;\'>شما بايد فيلد اضافه ای تعريف نماييد</td></tr>';
			}
			else
			{
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><select name=\'mobilenumberfield\'><option value=\'\'></option>';
				$a = 0;
				while( $row_al = mysql_fetch_assoc($al) )
				{
					echo '<option value=\'' . $row_al['fieldname'] . '\'';
					if ($row_mod['mobilenumberfield'] == $row_al['fieldname'])
					{
						echo ' selected=\'selected\'';
						$a = 1;
					}
					echo '>' . $row_al['fieldname'] . '</option>';
				}
				if ($a != 0)
				{
					echo '</select><br />In order to use this addon module, you have to create a custom client field for mobile phones and select it here.</td></tr>';
				}
				else
				{
					echo '</select><br /><font style=\'background-color:red;color:#ffffff\'>لطفا فيلد مورد نظر را انتخاب نماييد</font></td></tr>';
				}
			}
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>نام فیلد ارسال پیامک:</td>';
			$al2           = mysql_query('SELECT fieldname FROM tblcustomfields WHERE type=\'client\' AND fieldtype=\'dropdown\'');
			$totalRows_al2 = mysql_num_rows($al2);
			if ($totalRows_al2 == 0)
			{
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;background:#f58c8c;\'>You must create a custom client field as dropdown and ask client if he/she wants to receive SMS messages</td></tr>';
			}
			else
			{
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><select name=\'notificationfield\'><option value=\'\'></option>';
				$a = 0;
				
				while( $row_al2 = mysql_fetch_assoc($al2) )
				{
					echo '<option value=\'' . $row_al2['fieldname'] . '\'';
					if ($row_mod['notificationfield'] == $row_al2['fieldname'])
					{
						echo ' selected=\'selected\'';
						$a = 1;
					}
					echo '>' . $row_al2['fieldname'] . '</option>';
				}
				if ($a != 0)
				{
					echo '</select><br />In order to use this addon module, you have to create a custom client field as dropdown and ask client if he/she wants to receive SMS messages and select the field here.</td></tr>';
				}
				else
				{
					echo '</select><br /><font style=\'background-color:red;color:#ffffff\'>لطفا فيلد مورد نظر را انتخاب نماييد</font></td></tr>';
				}
			}
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>فيلد عدم ارسال:</td>';
			$al3           = @mysql_query('SELECT fieldoptions FROM tblcustomfields WHERE type=\'client\' AND fieldtype=\'dropdown\'');
			$totalRows_al3 = mysql_num_rows($al3);
			if ($totalRows_al3 == 0)
			{
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;background:#f58c8c;\'>You must create a custom client field as dropdown and ask client if he/she wants to receive SMS messages</td></tr>';
			}
			else
			{
				while ($row_al3 = mysql_fetch_assoc($al3))
				{
					$patlat = explode(',', $row_al3['fieldoptions']);
					foreach ($patlat as $isim)
					{
						$secenekler[] = $isim;
					}
				}
				echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><select name=\'no_area\'><option value=\'\'></option>';
				foreach ($secenekler as $secenek)
				{
					echo '<option value=\'' . $secenek . '\'';
					if ($row_mod['no_area'] == $secenek)
					{
						echo ' selected=\'selected\'';
					}
					echo '>' . $secenek . '</option>';
				}
				echo '</select><br />Name of the dropdown option to choose if client doesn\'t want to receive sms messages.</td></tr>';
			}
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>شماره موبایل مدیر :</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'text\' name=\'adminmobile\' value=\'' . $row_mod['adminmobile'] . '\'><br />شماره موبایل مدیر را با فرمت صحیح وارد نمایید.در صورتی که چند مدیر دارید شماره موبایل مدیران را با ; جدا نمایید</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;\'>تعداد نمایش پیامک ها در هر صفحه:</td>';
			echo '<td style=\'border-top:1px solid black;\'><input type=\'text\' name=\'logsperpage\' value=\'' . $row_mod['logsperpage'] . '\'></td></tr>';
			echo '<tr><td colspan=\'2\'>&nbsp;</td></tr>';
			echo '<tr><td colspan=\'2\'><font color=\'red\'><b>تنظیمات پیامک ها</b></font></td></tr>';
			echo '<tr><td colspan=\'2\'><b><u>ارسال پیامک به مشتریان:</u></b></td></tr>';
			echo '<tr><td style=\'border-bottom:1px solid black;\'>در زمان ايجاد فاکتور (ارسال روزانه):</td>';
			echo '<td style=\'border-bottom:1px solid black;\'><input type=\'radio\' name=\'new_bill\' value=\'1\'';
			if ($row_mod['new_bill'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'new_bill\' value=\'0\'';
			if ($row_mod['new_bill'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'invoicetextclient\' cols=\'70\' rows=\'1\'>' . $row_mod['invoicetextclient'] . '</textarea><br />متغیر ها: {amount}, {duedate}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان تغییر کلمه عبور:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'changepass\' value=\'1\'';
			if ($row_mod['changepass'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'changepass\' value=\'0\'';
			if ($row_mod['changepass'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'passwordchangetxt\' cols=\'70\' rows=\'1\'>' . $row_mod['passwordchangetxt'] . '</textarea><br />متغیر ها:{emailaddress}, {password}</td></tr>';
			//start
			
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان ثبت نام کاربر:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'clientadd\' value=\'1\'';
			if ($row_mod['clientadd'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'clientadd\' value=\'0\'';
			if ($row_mod['clientadd'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'clientaddtxtclient\' cols=\'70\' rows=\'1\'>' . $row_mod['clientaddtxtclient'] . '</textarea><br />متغیر ها:{firstname}, {lastname}</td></tr>';
			
			
			//end
			//start
			
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان پرداخت فاکتور:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'payclient\' value=\'1\'';
			if ($row_mod['payclient'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'payclient\' value=\'0\'';
			if ($row_mod['payclient'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'clientpaytxtclient\' cols=\'70\' rows=\'1\'>' . $row_mod['clientpaytxtclient'] . '</textarea><br />متغیر ها:{firstname}, {lastname}, {invoiceid}, {amount}</td></tr>';
			
			
			//end
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>هنگام ارسال سفارش جدید:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'orders\' value=\'1\'';
			if ($row_mod['orders'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'orders\' value=\'0\'';
			if ($row_mod['orders'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'ordertextclient\' cols=\'70\' rows=\'1\'>' . $row_mod['ordertextclient'] . '</textarea><br />متغیر ها: {amount}, {duedate}, {orderid}, {ordernumber}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>هنگام ارسال تیکت پشتیبانی:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'newticket\' value=\'1\'';
			if ($row_mod['newticket'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'newticket\' value=\'0\'';
			if ($row_mod['newticket'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'ticketopentxtclient\' cols=\'70\' rows=\'1\'>' . $row_mod['ticketopentxtclient'] . '</textarea><br />متغیر ها:{ticketid}, {clientname}, {department}, {departmentid}, {subject}, {message}, {priority}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>هنگام پاسخ دادن به تیکت پشتیبانی:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'ticketreply\' value=\'1\'';
			if ($row_mod['ticketreply'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'ticketreply\' value=\'0\'';
			if ($row_mod['ticketreply'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br />If enabled, client will be texted when admin replies to his/her support ticket.<br /><textarea dir=\'rtl\' name=\'ticketreplytext\' cols=\'70\' rows=\'1\'>' . $row_mod['ticketreplytext'] . '</textarea><br />متغیر ها: {ticketid}, {replyid}, {admin}, {departmentid}, {department}, {subject}, {message}, {priority}, {status}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان ایجاد سرویس:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'modulecreate\' value=\'1\'';
			if ($row_mod['modulecreate'] == 1)
				{
				echo ' checked=\'checked\'';
				}
			echo '> فعال <input type=\'radio\' name=\'modulecreate\' value=\'0\'';
			if ($row_mod['modulecreate'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'modulecreatetext\' cols=\'70\' rows=\'1\'>' . $row_mod['modulecreatetext'] . '</textarea><br />متغیر ها: {domain}, {username}, {password}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان بسته شدن سرویس:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'modulesuspend\' value=\'1\'';
			if ($row_mod['modulesuspend'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'modulesuspend\' value=\'0\'';
			if ($row_mod['modulesuspend'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'modulesuspendtext\' cols=\'70\' rows=\'1\'>' . $row_mod['modulesuspendtext'] . '</textarea><br />متغیر ها: {domain}, {username}, {password}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>قبل از انقضای سرويس(Daily Cron):</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'text\' name=\'domainxdays\' value=\'' . $row_mod['domainxdays'] . '\' style=\'width:30px\'> days before, set 0 to disable<br /><textarea dir=\'rtl\' name=\'domainxdaystext\' cols=\'70\' rows=\'1\'>' . $row_mod['domainxdaystext'] . '</textarea><br />متغیر ها: {domain}, {remainingdays}, {expirydate}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;\'>در زمان انقضای سفارش(Daily Cron):</td>';
			echo '<td style=\'border-top:1px solid black;\'><input type=\'radio\' name=\'dueinvoice\' value=\'1\'';
			if ($row_mod['dueinvoice'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'dueinvoice\' value=\'0\'';
			if ($row_mod['dueinvoice'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'dueinvoicetext\' cols=\'70\' rows=\'1\'>' . $row_mod['dueinvoicetext'] . '</textarea><br />متغیر ها: {amount}, {duedate}</td></tr>';
			echo '<tr><td colspan=\'2\'><b><u>ارسال پیامک به مدیریت:</u></b></td></tr>';
			echo '<tr><td style=\'border-bottom:1px solid black;\'>هنگام ارسال سفارش جدید:</td>';
			echo '<td style=\'border-bottom:1px solid black;\'><input type=\'radio\' name=\'ordersadmin\' value=\'1\'';
			if ($row_mod['ordersadmin'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'ordersadmin\' value=\'0\'';
			if ($row_mod['ordersadmin'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'ordertextadmin\' cols=\'70\' rows=\'1\'>' . $row_mod['ordertextadmin'] . '</textarea><br />متغیر ها: {amount}, {duedate}, {orderid}, {ordernumber}</td></tr>';
			//start
			
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان ثبت نام کاربر:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'clientaddadmin\' value=\'1\'';
			if ($row_mod['clientaddadmin'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'clientaddadmin\' value=\'0\'';
			if ($row_mod['clientaddadmin'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'clientaddtxtadmin\' cols=\'70\' rows=\'1\'>' . $row_mod['clientaddtxtadmin'] . '</textarea><br />متغیر ها:{firstname}, {lastname}</td></tr>';
			
			
			//end
			//start
			
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>در زمان پرداخت فاکتور:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'payadmin\' value=\'1\'';
			if ($row_mod['payadmin'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'payadmin\' value=\'0\'';
			if ($row_mod['payadmin'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'adminpaytxtclient\' cols=\'70\' rows=\'1\'>' . $row_mod['adminpaytxtclient'] . '</textarea><br />متغیر ها:{firstname}, {lastname}, {invoiceid}, {amount}</td></tr>';
			
			
			//end
			echo '<tr><td style=\'border-top:1px solid black;border-bottom:1px solid black;\'>هنگام ارسال تیکت پشتیبانی جدید:</td>';
			echo '<td style=\'border-top:1px solid black;border-bottom:1px solid black;\'><input type=\'radio\' name=\'newticketadmin\' value=\'1\'';
			if ($row_mod['newticketadmin'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'newticketadmin\' value=\'0\'';
			if ($row_mod['newticketadmin'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br><br>Urgency: <input type="checkbox" name="urgency3" value="1"';
			if ($row_mod['urgency3'] == 1)
			{
				echo ' checked="checked"';
			}
			echo '> مهم <input type="checkbox" name="urgency2" value="1"';
			if ($row_mod['urgency2'] == 1)
			{
				echo ' checked="checked"';
			}
			echo '> متوسط <input type="checkbox" name="urgency1" value="1"';
			if ($row_mod['urgency1'] == 1)
			{
				echo ' checked="checked"';
			}
			echo '> کم اهميت<br /><textarea dir=\'rtl\' name=\'ticketopentxtadmin\' cols=\'70\' rows=\'1\'>' . $row_mod['ticketopentxtadmin'] . '</textarea><br />متغیر ها:{ticketid}, {clientname}, {department}, {departmentid}, {subject}, {message}, {priority}</td></tr>';
			echo '<tr><td style=\'border-top:1px solid black;\'>هنگام پاسخ دادن به تیکت پشتیبانی:</td>';
			echo '<td style=\'border-top:1px solid black;\'><input type=\'radio\' name=\'ticketreplyadmin\' value=\'1\'';
			if ($row_mod['ticketreplyadmin'] == 1)
			{
				echo ' checked=\'checked\'';
			}
			echo '> فعال <input type=\'radio\' name=\'ticketreplyadmin\' value=\'0\'';
			if ($row_mod['ticketreplyadmin'] == 0)
			{
				echo ' checked=\'checked\'';
			}
			echo '> غيرفعال<br /><textarea dir=\'rtl\' name=\'ticketreplytextadmin\' cols=\'70\' rows=\'1\'>' . $row_mod['ticketreplytextadmin'] . '</textarea><br />متغیر ها: {ticketid}, {replyid}, {userid}, {clientname}, {departmentid}, {department}, {subject}, {message}, {priority}, {status}</td></tr>';
			echo '<tr><td colspan=\'2\'><b><u>تنظیمات عمومی:</u></b></td></tr>';
			echo '<tr><td>امضا:</td>';
			echo '<td><textarea dir=\'rtl\' name=\'businessname\' cols=\'70\' rows=\'1\'>' . $row_mod['businessname'] . '</textarea><br />این متن به آخر تمامی متن پیامک های ارسال شده افزوده می شود.</td></tr>';
			echo '<tr><td colspan=\'2\' align=\'center\'><input type=\'submit\' value=\'ذخيره تنظيمات\'>';
			if (!isset($_GET['firstuse']))
			{
				echo '<input type="hidden" name="git" value="anamenu"> <input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';">';
			}
			echo '</td></tr>
			</table>
			</form>
			</fieldset>
			';
			return 1;
		}
		echo 'نصب افزونه با مشکل مواجه شده است. برای نصب مجدد <a href=\'addonmodules.php?module=sms_addon&cleanup\'>اينجا</a> کليک کنيد.';
		return 1;
	}
	
	if (isset($_GET['error']))
	{
		echo $_SESSION['error'] . '<br /><a href=\'javascript:history.go(-1)\'>بازگشت</a>';
		return 1;
	}
	if (isset($_GET['singlesms']))
	{
		$mod          = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod      = @mysql_fetch_assoc($mod);
		$customers       = @mysql_query('SELECT id, firstname, lastname FROM tblclients ORDER BY firstname, lastname ASC');
		$row_telfield = mysql_fetch_assoc(@mysql_query('SELECT id FROM tblcustomfields WHERE fieldname=' . @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
		echo '
		<style>
			fieldset{
				-moz-border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				border-radius: 5px 5px 5px 5px;
				border: 4px solid #ccc;
				padding: 5px;
				margin: 10px 0;
				direction: rtl;
				font-family: Tahoma;
				text-align: right;
			}
			
			fieldset *{
				font-family: Tahoma;
			}

			fieldset legend{
				padding: 5px 30px;
				background-color: #ccc;
				font-weight: 900;
				margin-right: 14px;
			}
		</style>
		
		<fieldset>
				<legend>تنظيمات ماژول</legend>
		<form method=\'post\' action=\'addonmodules.php?module=sms_addon&SendSingleSms\'>متن پيامک:<br /><textarea name=\'content\' style=\'width:350px;\' rows=\'10\'></textarea><br />دريافت کننده:<br /> <select name=\'customer\'><option value="none">کاربر مورد نظر</option>';
		while ($row_customers = mysql_fetch_assoc($customers))
		{
			$mod2      = @mysql_query('SELECT * FROM mod_smsaddon5');
			$row_mod2  = @mysql_fetch_assoc($mod2);
			$telu      = '';
			$row_telno = mysql_fetch_assoc(@mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_telfield['id'] . '\' AND relid=\'' . $row_customers['id'] . '\''));
			$telu      = $row_telno['value'];
			if ($telu != '')
			{
				$telu = str_replace(array(' ', '-', '(', ')', ''), '', $telu);

				if($telu[0] != '0') $telu = '0'.$telu;
			}
			echo '<option value="' . $telu . '|' . $row_customers['id'] . '"';
			if ($telu == '')
			{
				$telu = 'فاقد شماره موبايل';
				echo ' disabled="disabled"';
			}
			echo '>' . $row_customers['firstname'] . ' ' . $row_customers['lastname'] . ' (' . $telu . ')</option>';
		}
		echo '</select><br /><b>و يا</b> شماره موبايل:<br /><input type=\'text\' name=\'recipient\'><br /><br /><input type=\'submit\' value=\'ارسال پيامک\'>
		<input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';"></form></fieldset>';
		return 1;
	}
	
	if (isset($_GET['masssms']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);

		$customers        = @mysql_query('SELECT count(id) FROM tblclients');
		$row_customers    = @mysql_fetch_assoc($customers);
		$totalRows_mod = @mysql_num_rows($mod);
		
<<<<<<< HEAD
		$parameters['username'] = $row_mod['username'];
		$parameters['password'] = $row_mod['password'];
		
		$client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
		
		$credit = $client->CheckCredit(array(
				'Auth' => array('number' => $row_mod['number'],'pass' => $row_mod['password'])))->Credit;
				
		if ($credit < 0)
=======
		$sms_client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
		$credit = $sms_client->CheckCredit(array(
			'Auth' => array('number' => $gateway['number'],'pass' => $gateway['pass'])
		));
		
		
		if ($credit->Status != 1000)
>>>>>>> origin/master
		{
			$error  = 1;
			$credit_str = 'خطایی در ارتباط با درگاه رخ داده است. کد خطا: '. $credit;
		}
		else
		{
			$credit_str = 'اعتبار درگاه: ' . $credit->Credit . ' <b>تعداد مشترکين:</b> ' . $row_customers['count(id)'];
		}

		
		echo '<style>
			fieldset{
				-moz-border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				border-radius: 5px 5px 5px 5px;
				border: 4px solid #ccc;
				padding: 5px;
				margin: 10px 0;
				direction: rtl;
				font-family: Tahoma;
				text-align: right;
			}
			
			fieldset *{
				font-family: Tahoma;
			}

			fieldset legend{
				padding: 5px 30px;
				background-color: #ccc;
				font-weight: 900;
				margin-right: 14px;
			}
		</style>
		
		<fieldset>
			<legend>ارسال پيامک گروهی</legend>
			<br /><br />' . $credit_str . '<br /><br />';
		
		if ((isset($_SESSION['masssmserror']) && $_SESSION['masssmserror'] != ''))
		{
			echo '<font style="color:#f58c8c;font-weight:bold;">' . $_SESSION['masssmserror'] . '</font><br /><br />';
			$_SESSION['masssmserror'] = '';
		}
		
		echo '<form method=\'post\' action=\'addonmodules.php?module=sms_addon&SendMassSms\'>متن پيامک:<br /><textarea name=\'content\' style=\'width:350px;\' rows=\'10\'></textarea><br />ارسال پيامک به کل اعضا به صورت اجباری: <input type=\'radio\' name=\'force\' value=\'0\' checked=\'checked\'> خير <input type=\'radio\' name=\'force\' value=\'1\'> بله<br /><input type=\'submit\' value=\'ارسال پيامک گروهی\'>
		<input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';"></form></fieldset>';
		return 1;
	}
	
	if (isset($_GET['logs']))
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);

		$maxRows_log = $row_mod['logsperpage'];
		$pageNum_log = 0;
		
		if (isset($_GET['page']))
		{
			$pageNum_log = $_GET['page'] - 1;
		}
		
		$startRow_log    = $pageNum_log * $maxRows_log;
		$query_log       = 'SELECT * FROM mod_smsaddon5_logs ORDER BY id DESC';
		$query_limit_log = sprintf('%s LIMIT %d, %d', $query_log, $startRow_log, $maxRows_log);
		
		if (!($log = @mysql_query($query_limit_log)))
		{
			exit(mysql_error());
			(bool) true;
		}
		
		$all_log        = @mysql_query($query_log);
		$totalRows_log  = @mysql_num_rows($all_log);
		$totalPages_log = ceil($totalRows_log / $maxRows_log) - 1;
		echo '
		<style>
			fieldset{
				-moz-border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				border-radius: 5px 5px 5px 5px;
				border: 4px solid #ccc;
				padding: 5px;
				margin: 10px 0;
				direction: rtl;
				font-family: Tahoma;
				text-align: right;
			}
			
			fieldset *{
				font-family: Tahoma;
			}

			fieldset legend{
				padding: 5px 30px;
				background-color: #ccc;
				font-weight: 900;
				margin-right: 14px;
			}
		</style>
		
		<fieldset>
			<legend>پيامک های ارسالی</legend>
		';
		
		if ($totalRows_log == 0)
		{
			echo 'فاقد فعاليت';
			echo '<br /><input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';">';
			return 1;
		}
		
		echo '<input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';"><br /><br />';
		
		if (0 < $pageNum_log)
		{
			echo '<a href="addonmodules.php?module=sms_addon&logs&page=' . (max(0, $pageNum_log - 1) + 1) . '">صفحه قبل</a> | ';
		}
		
		if (1 < $totalPages_log)
		{
			for ($i = 0; $i <= $totalPages_log; $i++)
			{
				$abc = $i + 1;
				if ($pageNum_log == $i)
				{
					echo '(' . $abc . ') ';
				}
				else
				{
					echo '<a href=\'addonmodules.php?module=sms_addon&logs&page=' . $abc . '\'>' . $abc . '</a> ';
				}
			}
		}
		
		if ($pageNum_log < $totalPages_log)
		{
			echo '| <a href="addonmodules.php?module=sms_addon&logs&page=' . (min($totalPages_log, $pageNum_log + 1) + 1) . '">صفحه بعد</a>';
		}
		
		echo '<table width=\'70%\'>';
		echo '<tr>';
		echo '<td><b>زمان</b></td><td><b>کاربر</b></td><td><b>شماره موبايل</b></td><td><b>وضعيت</b></td><td><b>متن پيامک</b></td>';
		echo '</tr>';
		while ($row_log = mysql_fetch_assoc($log))
		{
			echo '<tr>';
			echo '<td style=\'border:1px solid black;\'>' . date('Y-m-d, G:i', $row_log['time']) . '</td><td style=\'border:1px solid black;\'>';
			if ((($row_log['client'] != 0 && $row_log['client'] != 'admin') && $row_log['client'] != ''))
			{
				$row_customeral = mysql_fetch_assoc(@mysql_query('SELECT firstname, lastname FROM tblclients WHERE id=\'' . $row_log['client'] . '\''));
				echo '<a href=\'clientssummary.php?userid=' . $row_log['client'] . '\' target=\'_blank\'>' . $row_customeral['firstname'] . ' ' . $row_customeral['lastname'] . '</a>';
			}
			else
			{
				echo $row_log['client'];
			}
			echo '</td><td style=\'border:1px solid black;\'>' . $row_log['mobilenumber'] . '</td><td style=\'border:1px solid black;\'>' . $row_log['result'] . '</td><td style=\'border:1px solid black;\'>' . $row_log['text'] . '</td>';
			echo '</tr>';
		}
		echo '</table><br />';
		if (0 < $pageNum_log)
		{
			echo '<a href="addonmodules.php?module=sms_addon&logs&page=' . (max(0, $pageNum_log - 1) + 1) . '">صفحه قبل</a> | ';
		}
		if (1 < $totalPages_log)
		{
			for ($i = 0; $i <= $totalPages_log; $i++)
			{
				$abc = $i + 1;
				if ($pageNum_log == $i)
				{
					echo '(' . $abc . ') ';
				}
				else
				{
					echo '<a href=\'addonmodules.php?module=sms_addon&logs&page=' . $abc . '\'>' . $abc . '</a> ';
				}
			}
		}
		if ($pageNum_log < $totalPages_log)
		{
			echo '| <a href="addonmodules.php?module=sms_addon&logs&page=' . (min($totalPages_log, $pageNum_log + 1) + 1) . '">صفحه بعد</a><br /><br />';
		}
		echo '<input type=\'button\' value=\'پاک کردن لاگ ارسال ها\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon&clearlog\';">';
		echo ' <input type=\'button\' value=\'بازگشت\' onclick="javascript:window.location=\'addonmodules.php?module=sms_addon\';">';
		return 1;
	}
	
	$mod           = @mysql_query('SELECT * FROM mod_smsaddon5');
	$row_mod       = @mysql_fetch_assoc($mod);
	if ($row_mod)
	{
<<<<<<< HEAD
		$parameters['username'] = $row_mod['username'];
		$parameters['password'] = $row_mod['password'];
		
		$client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
		
		$credit = $client->CheckCredit(array(
				'Auth' => array('number' => $row_mod['number'],'pass' => $row_mod['password'])))->Credit;
	
		if ($credit < 0)
=======
		$sms_client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
		$credit = $sms_client->CheckCredit(array(
			'Auth' => array('number' => $row_mod['username'],'pass' => $row_mod['password'])
		));

		if ($credit->Status != 1000)
>>>>>>> origin/master
		{
			$error  = 1;
			$credit_str = 'خطایی در ارتباط با درگاه رخ داده است. کد خطا: '. $credit;
		}
		else
		{
			$credit_str = 'اعتبار درگاه: ' . $credit;
		}
		
		echo '
		<style>
			fieldset{
				-moz-border-radius: 5px 5px 5px 5px;
				-webkit-border-radius: 5px 5px 5px 5px;
				border-radius: 5px 5px 5px 5px;
				border: 4px solid #ccc;
				padding: 5px;
				margin: 10px 0;
				direction: rtl;
				font-family: Tahoma;
				text-align: right;
			}
			
			fieldset *{
				font-family: Tahoma;
			}

			fieldset legend{
				padding: 5px 30px;
				background-color: #ccc;
				font-weight: 900;
				margin-right: 14px;
			}
		</style>
		
		<fieldset>
				<legend>تنظيمات ماژول</legend>'. $credit_str.
		'<ul>
		<li><a href=\'addonmodules.php?module=sms_addon&singlesms\'>ارسال پيامک تکی</a></li>
		<li><a href=\'addonmodules.php?module=sms_addon&masssms\'>ارسال پيامک چندتايی</a></li>
		</ul>
		<ul>
		<li><a href=\'addonmodules.php?module=sms_addon&settings\'>تنظيمات درگاه</a></li>
		<li><a href=\'addonmodules.php?module=sms_addon&modifysettings\'>تنظيمات ماژول</a></li>
		</ul>
		<ul>
		<li><a href=\'addonmodules.php?module=sms_addon&logs\'>پيامک های ارسالی</a></li>
		</ul>
		<center>ماژول ارسال پيامک نسخه 1.2.2 توسط <a href="http://www.novinpayamak.com/ target="_blank">نوین پیامک</a></center>
		</fieldset>
		';
		return 1;
	}
	echo '<form action=\'addonmodules.php?module=sms_addon&next\' method=\'post\'><input type=\'submit\' value=\'نصب افزونه\'></form>';
?>
