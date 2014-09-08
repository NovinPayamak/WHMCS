<?php
	function SendS($gateway, $message)
	{
		
		if(strlen($message['numbers']) == 10 )
				$message['numbers'] = '0'.$message['numbers'];
		if(empty($message['flash'])) $message['flash'] = false;
		
		$sms_client = new SoapClient('http://www.novinpayamak.com/services/SMSBox/wsdl', array('encoding' => 'UTF-8', 'connection_timeout' => 3));
			return $sms_client->Send(array(
				'Auth' => array('number' => $gateway['number'],'pass' => $gateway['pass']),
				'Recipients' => array($message['numbers']),
				'Message' => array($message['content']),
				'Flash' => $message['flash']
<<<<<<< HEAD
			))->Status;
		
	}
	
	function client_add($vars) {
		$mod = @mysql_query( 'SELECT * FROM mod_smsaddon5' );
		$row_mod = @mysql_fetch_array( $mod );
		
		if ($mod) {
			if ($row_mod['clientadd'] == 1) {
				if($vars['firstname'] !=" " AND $vars['lastname']!=" " )
				{
					$tel             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
					$row_tel         = @mysql_fetch_array($tel);
					$report             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
					$row_report         = @mysql_fetch_array($report);
					if ($row_tel['id'] != '' && $row_report['id'] != '')
					{
						$show = mysql_query( "SELECT * FROM `tblcustomfieldsvalues` WHERE `fieldid`='".$row_tel['id']."' AND `relid`='".$vars['userid']."' " );
						$item = mysql_fetch_array( $show );
						$clientaddtxtclient = str_replace( '{firstname}', $vars['firstname'], $row_mod['clientaddtxtclient'] );
						$clientaddtxtclient = str_replace( '{lastname}', $vars['lastname'], $clientaddtxtclient );
						$gateway['number'] = $row_mod['number'];
						$gateway['username'] = $row_mod['username'];
						$gateway['pass'] = $row_mod['password'];
						$message['numbers'] = $item['value'];
						$message['content'] = $clientaddtxtclient . $row_mod['businessname'];
						$response = SendS( $gateway, $message );
						mysql_query( 'INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time(  ) . '\', \'' . $vars['userid'] . '\', \'' . $vars['phonenumber'] . ( '\', \'' . $response . '\', \'' ) . $clientaddtxtclient . $row_mod['businessname'] . '\')' );
					}
				}
			}


			if ($row_mod['clientaddadmin'] == 1) {
				if($vars['firstname'] !=" " AND $vars['lastname']!=" " )
				{
			
					$clientaddtxtadmin = str_replace( '{firstname}', $vars['firstname'], $row_mod['clientaddtxtadmin'] );
					$clientaddtxtadmin = str_replace( '{lastname}', $vars['lastname'], $clientaddtxtadmin );
					$gateway['number'] = $row_mod['number'];
					$gateway['username'] = $row_mod['username'];
					$gateway['pass'] = $row_mod['password'];
					$message['numbers'] = $row_mod['adminmobile'];
					$message['content'] = $clientaddtxtadmin;
					$response = SendS( $gateway, $message );
					mysql_query( 'INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time(  ) . '\', \'admin\', \'' . $row_mod['adminmobile'] . ( '\', \'' . $response . '\', \'' . $clientaddtxtadmin . '\')' ) );
				}
			}
		}
		
		return true;
	
	}
	
	function invoice_paid($vars) {
		$mod = @mysql_query( 'SELECT * FROM mod_smsaddon5' );
		$row_mod = @mysql_fetch_array( $mod );
		
		if ($mod) {
			if ($row_mod['payclient'] == 1) {
				$tel             = @mysql_query(@sprintf('SELECT id FROM `tblcustomfields` WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
				$row_tel         = @mysql_fetch_array($tel);
				if ($row_tel['id'] != '' )
				{
					
					$show_invoice = mysql_query( "SELECT * FROM `tblinvoices` WHERE `id`='".$vars['invoiceid']."' " );
					$item_invoice = mysql_fetch_array( $show_invoice );
					
					$show_client = mysql_query( "SELECT * FROM `tblclients` WHERE `id`='".$item_invoice['userid']."' " );
					$item_client = mysql_fetch_array( $show_client );
					
					$show = mysql_query( "SELECT * FROM `tblcustomfieldsvalues` WHERE `fieldid`='".$row_tel['id']."' AND `relid`='".$item_invoice['userid']."' " );
					$item = mysql_fetch_array( $show );
					
					$clientaddtxtclient = str_replace( '{invoiceid}', $vars['invoiceid'], $row_mod['clientpaytxtclient'] );
					$clientaddtxtclient = str_replace( '{amount}', $item_invoice['total'], $clientaddtxtclient );
					$clientaddtxtclient = str_replace( '{firstname}', $item_client['firstname'], $clientaddtxtclient );
					$clientaddtxtclient = str_replace( '{lastname}', $item_client['lastname'], $clientaddtxtclient );
					$gateway['number'] = $row_mod['number'];
					$gateway['username'] = $row_mod['username'];
					$gateway['pass'] = $row_mod['password'];
					$message['numbers'] = $item['value'];
					$message['content'] = $clientaddtxtclient . $row_mod['businessname'];
					$response = SendS( $gateway, $message );
					mysql_query( 'INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time(  ) . '\', \'' . $item_client['firstname'].$item_client['lastname'] . '\', \'' . $item['value'] . ( '\', \'' . $response . '\', \'' ) . $clientaddtxtclient . $row_mod['businessname'] . '\')' );
				}
			}
=======
			));
		}
>>>>>>> origin/master


			if ($row_mod['payadmin'] == 1) {
			
				$show_invoice = mysql_query( "SELECT * FROM `tblinvoices` WHERE `id`='".$vars['invoiceid']."' " );
				$item_invoice = mysql_fetch_array( $show_invoice );
					
				$show_client = mysql_query( "SELECT * FROM `tblclients` WHERE `id`='".$item_invoice['userid']."' " );
				$item_client = mysql_fetch_array( $show_client );
				
				$clientaddtxtadmin = str_replace( '{invoiceid}', $vars['invoiceid'], $row_mod['adminpaytxtclient'] );
				$clientaddtxtadmin = str_replace( '{amount}', $item_invoice['total'], $clientaddtxtadmin );
				$clientaddtxtadmin = str_replace( '{firstname}', $item_client['firstname'], $clientaddtxtadmin );
				$clientaddtxtadmin = str_replace( '{lastname}', $item_client['lastname'], $clientaddtxtadmin );
				$gateway['number'] = $row_mod['number'];
				$gateway['username'] = $row_mod['username'];
				$gateway['pass'] = $row_mod['password'];
				$message['numbers'] = $row_mod['adminmobile'];
				$message['content'] = $clientaddtxtadmin;
				$response = SendS( $gateway, $message );
				mysql_query( 'INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time(  ) . '\', \'admin\', \'' . $row_mod['adminmobile'] . ( '\', \'' . $response . '\', \'' . $clientaddtxtadmin . '\')' ) );
			}
		}
		
		return true;
	
	}
	
	function client_change_password($vars) {
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod){
			if ($row_mod['changepass'] == 1)
			{
				$row_masteremail   = mysql_fetch_assoc(mysql_query('SELECT email FROM tblclients WHERE id=\'' . $vars['userid'] . '\''));
				$passwordchangetxt = str_replace('{emailaddress}', $row_masteremail['email'], $row_mod['passwordchangetxt']);
				$passwordchangetxt = str_replace('{password}', $vars['password'], $passwordchangetxt);
				$tel           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
				$row_tel       = @mysql_fetch_assoc($tel);
				$report           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
				$row_report       = @mysql_fetch_assoc($report);
				if (($row_tel['id'] != '' && $row_report['id'] != ''))
				{
					$teli     = @mysql_query("SELECT * FROM `tblcustomfieldsvalues` WHERE `fieldid` ='".$row_tel['id']."' AND `relid` ='".$vars['userid']."' ");
					$row_teli = @mysql_fetch_array($teli);
					$reportal     = @mysql_query("SELECT * FROM `tblcustomfieldsvalues` WHERE `fieldid`='".$row_report['id']."' AND `relid` ='".$vars['userid']."' ");
					$row_reportal = @mysql_fetch_assoc($reportal);
					if ($row_teli['value'] == '')
					{
						
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $passwordchangetxt . $row_mod['businessname']) . '\')');
						$error = 1;
					}
					if ($error != 1)
					{
						$row_teli['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_teli['value']);

						if($row_teli['value'][0] != '0') $row_teli['value'] = '0'.$row_teli['value'];
					}
					if ($error != 1)
					{
						if ($row_reportal['value'] == $row_mod['no_area'])
						{
							$error  = 1;
							
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars[0] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $passwordchangetxt . $row_mod['businessname']) . '\')');
						}
					}
					if ($error != 1)
					{
						$gatewa['username']  = $row_mod['username'];
						$gatewa['pass']    = $row_mod['password'];
						$gatewa['number']    = $row_mod['number'];
						$messag['numbers'] = $row_teli['value'];
						$messag['content'] = $passwordchangetxt . $row_mod['businessname'];

<<<<<<< HEAD
						$response = SendS($gatewa, $messag);

						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'' . $row_teli['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $passwordchangetxt . $row_mod['businessname']) . '\')');
=======
						$responseA = SendSMS($gateway, $message);
						$response = $responseA->Status;
						mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $passwordchangetxt . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
					}
				}
				else
				{
					mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
				}
			}
		}
			return true;
		
	}
	
	
	function ticket_open($vars)
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			if ($vars['userid'] != 0)
			{
				if (($row_mod['newticket'] == 1 || $row_mod['newticketadmin'] == 1))
				{
					$row_kullaniciyial   = mysql_fetch_assoc(mysql_query('SELECT firstname, lastname FROM tblclients WHERE id=\'' . $vars['userid'] . '\''));
					$ticketopentxtclient = $row_mod['ticketopentxtclient'] . $row_mod['businessname'];
					$ticketopentxtclient = str_replace('{department}', $vars['deptname'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{subject}', $vars['subject'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{priority}', $vars['priority'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{clientname}', $row_kullaniciyial['firstname'] . ' ' . $row_kullaniciyial['lastname'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{ticketid}', $vars['ticketid'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{departmentid}', $vars['deptid'], $ticketopentxtclient);
					$ticketopentxtclient = str_replace('{message}', $vars['message'], $ticketopentxtclient);
					$ticketopentxtadmin  = $row_mod['ticketopentxtadmin'] . $row_mod['businessname'];
					$ticketopentxtadmin  = str_replace('{department}', $vars['deptname'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{subject}', $vars['subject'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{priority}', $vars['priority'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{clientname}', $row_kullaniciyial['firstname'] . ' ' . $row_kullaniciyial['lastname'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{ticketid}', $vars['ticketid'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{departmentid}', $vars['deptid'], $ticketopentxtadmin);
					$ticketopentxtadmin  = str_replace('{message}', $vars['message'], $ticketopentxtadmin);
					$tel             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
					$row_tel         = @mysql_fetch_assoc($tel);
					$report             = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
					$row_report         = @mysql_fetch_assoc($report);
					if (($row_tel['id'] != '' && $row_report['id'] != ''))
					{
						$tel     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $vars['userid'] . '\'');
						$row_tel = @mysql_fetch_assoc($tel);
						$reportal     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $vars['userid'] . '\'');
						$row_reportal = @mysql_fetch_assoc($reportal);
						if ($row_tel['value'] == '')
						{
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $ticketopentxtclient) . '\')');
							$error = 1;
						}
						if ($error != 1)
						{
							$row_tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_tel['value']);
							
							if($row_tel['value'][0] != '0') $row_tel['value'] = '0'.$row_tel['value'];
						}
						if ($error != 1)
						{
							if ($row_reportal['value'] == $row_mod['no_area'])
							{
								$error  = 1;
								if ($row_mod['newticket'] == 1)
								{
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $ticketopentxtclient) . '\')');
								}
							}
						}
						if ($row_mod['newticket'] == 1)
						{
							if ($error != 1)
							{	
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $row_tel['value'];
								$message['content'] = $ticketopentxtclient;

<<<<<<< HEAD
								$response = SendS($gateway, $message);
								
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketopentxtclient) . '\')');
=======
								$responseA = SendSMS($gateway, $message);
								$response = $responseA->Status;
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketopentxtclient) . '\')');
>>>>>>> origin/master
							}
						}
						if ($row_mod['newticketadmin'] == 1)
						{
							if ($row_mod['urgency1'] == 1)
							{
								$ticketonstring1 = 'Low';
							}
							if ($row_mod['urgency2'] == 1)
							{
								$ticketonstring2 = 'Medium';
							}
							if ($row_mod['urgency3'] == 1)
							{
								$ticketonstring3 = 'High';
							}
							if ((($vars['priority'] == $ticketonstring1 || $vars['priority'] == $ticketonstring2) || $vars['priority'] == $ticketonstring3))
							{
								
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $row_mod['adminmobile'];
								$message['content'] = $ticketopentxtadmin;

								$response = SendS($gateway, $message);
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'admin\', \'' . $row_mod['adminmobile'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketopentxtadmin) . '\')');
							}
						}
					}
					else
					{
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
					}
				}
			}
		}
		return true;
	}
	
	function ticket_admin($vars)
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			$ticketal     = @mysql_query('SELECT userid FROM tbltickets WHERE id=\'' . $vars['ticketid'] . '\'');
			$row_ticketal = @mysql_fetch_assoc($ticketal);
				if ($row_ticketal['userid'] != 0)
				{
					if ($row_mod['ticketreply'] == 1)
					{
						$ticketreplytext = $row_mod['ticketreplytext'] . $row_mod['businessname'];
						$ticketreplytext = str_replace('{ticketid}', $vars['ticketid'], $ticketreplytext);
						$ticketreplytext = str_replace('{replyid}', $vars['replyid'], $ticketreplytext);
						$ticketreplytext = str_replace('{admin}', $vars['admin'], $ticketreplytext);
						$ticketreplytext = str_replace('{departmentid}', $vars['deptid'], $ticketreplytext);
						$ticketreplytext = str_replace('{department}', $vars['deptname'], $ticketreplytext);
						$ticketreplytext = str_replace('{subject}', $vars['subject'], $ticketreplytext);
						$ticketreplytext = str_replace('{message}', $vars['message'], $ticketreplytext);
						$ticketreplytext = str_replace('{priority}', $vars['priority'], $ticketreplytext);
						$ticketreplytext = str_replace('{status}', $vars['status'], $ticketreplytext);
						$tel         = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
						$row_tel     = @mysql_fetch_assoc($tel);
						$report         = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
						$row_report     = @mysql_fetch_assoc($report);
						if (($row_tel['id'] != '' && $row_report['id'] != ''))
						{
							$tel     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $row_ticketal['userid'] . '\'');
							$row_tel = @mysql_fetch_assoc($tel);
							$reportal     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_ticketal['userid'] . '\'');
							$row_reportal = @mysql_fetch_assoc($reportal);
							if ($row_tel['value'] == '')
							{
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_ticketal['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $ticketreplytext) . '\')');
								$error = 1;
							}
							if ($error != 1)
							{
								$row_tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_tel['value']);
								
								if($row_tel['value'][0] != '0') $row_tel['value'] = '0'.$row_tel['value'];
							}
							if ($error != 1)
							{
								if ($row_reportal['value'] == $row_mod['no_area'])
								{
									$error  = 1;
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_ticketal['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $ticketreplytext) . '\')');
								}
							}
							if ($error != 1)
							{
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $row_tel['value'];
								$message['content'] = $ticketreplytext;

								$response = SendS($gateway, $message);
								
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_ticketal['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketreplytext) . '\')');
							}
						}
						else
						{
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_ticketal['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
						}
					}
				}
		}
		return true;
	}
	
	function ticket_client($vars)
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			if ($row_mod['ticketreplyadmin'] == 1)
			{
				$user               = mysql_fetch_assoc(@mysql_query('SELECT firstname, lastname FROM tblclients WHERE id=\'' . $vars['userid'] . '\''));
				$ticketreplytextadmin = $row_mod['ticketreplytextadmin'] . $row_mod['businessname'];
				$ticketreplytextadmin = str_replace('{ticketid}', $vars['ticketid'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{replyid}', $vars['replyid'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{userid}', $vars['userid'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{departmentid}', $vars['deptid'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{department}', $vars['deptname'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{subject}', $vars['subject'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{message}', $vars['message'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{priority}', $vars['priority'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{status}', $vars['status'], $ticketreplytextadmin);
				$ticketreplytextadmin = str_replace('{clientname}', $user['firstname'] . ' ' . $user['lastname'], $ticketreplytextadmin);
				
				$gateway['username']  = $row_mod['username'];
				$gateway['pass']    = $row_mod['password'];
				$gateway['number']    = $row_mod['number'];
				$message['numbers'] = $row_mod['adminmobile'];
				$message['content'] = $ticketreplytextadmin;

<<<<<<< HEAD
				$response = SendS($gateway, $message);
				
				mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'admin\', \'' . $row_mod['adminmobile'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketreplytextadmin) . '\')');
=======
				$responseA = SendSMS($gateway, $message);
				$response = $responseA->Status;
				
				mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'admin\', \'' . $row_mod['adminmobile'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ticketreplytextadmin) . '\')');
>>>>>>> origin/master
			}
		}
		return true;
	}
	
	function after_checkout($vars)
	{
		if (($vars['InvoiceID'] != '' && $vars['InvoiceID'] != 0))
		{
			$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
			$row_mod = @mysql_fetch_assoc($mod);
			if ($mod)
			{
				if (($row_mod['orders'] == 1 || $row_mod['ordersadmin'] == 1))
				{
					$invoice            = @mysql_query('SELECT userid, duedate, total FROM tblinvoices WHERE id=\'' . $vars['InvoiceID'] . '\'');
					$row_invoice        = @mysql_fetch_assoc($invoice);
					$dateformat       = @mysql_query('SELECT value FROM tblconfiguration WHERE setting=\'DateFormat\'');
					$row_dateformat   = @mysql_fetch_assoc($dateformat);
					$date             = explode('-', $row_invoice['duedate']);
					$history         = str_replace(array('YYYY', 'MM', 'DD'), array($date[0], $date[1], $date[2]), $row_dateformat['value']);
					$ordertextclient = str_replace('{amount}', $row_invoice['total'], $row_mod['ordertextclient']);
					$ordertextclient = str_replace('{duedate}', $history, $ordertextclient);
					$ordertextclient = str_replace('{orderid}', $vars['OrderID'], $ordertextclient);
					$ordertextclient = str_replace('{ordernumber}', $vars['OrderNumber'], $ordertextclient);
					$ordertextadmin  = str_replace('{amount}', $row_invoice['total'], $row_mod['ordertextadmin']);
					$ordertextadmin  = str_replace('{duedate}', $history, $ordertextadmin);
					$ordertextadmin  = str_replace('{orderid}', $vars['OrderID'], $ordertextadmin);
					$ordertextadmin  = str_replace('{ordernumber}', $vars['OrderNumber'], $ordertextadmin);
					$tel           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
					$row_tel       = @mysql_fetch_assoc($tel);
					$report           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
					$row_report       = @mysql_fetch_assoc($report);
					if (($row_tel['id'] != '' && $row_report['id'] != ''))
					{
						$tel     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $row_invoice['userid'] . '\'');
						$row_tel = @mysql_fetch_assoc($tel);
						$reportal     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_invoice['userid'] . '\'');
						$row_reportal = @mysql_fetch_assoc($reportal);
						if ($row_tel['value'] == '')
						{
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_invoice['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $ordertextclient . $row_mod['businessname']) . '\')');
							$error = 1;
						}
						if ($error != 1)
						{
							$row_tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_tel['value']);
							
							if($row_tel['value'][0] != '0') $row_tel['value'] = '0'.$row_tel['value'];
						}
						if ($error != 1)
							{
							if ($row_reportal['value'] == $row_mod['no_area'])
								{
								$error  = 1;
								
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_invoice['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $ordertextclient . $row_mod['businessname']) . '\')');
								}
							}
						if ($row_mod['orders'] == 1)
						{
							if ($error != 1)
							{
							
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $row_tel['value'];
								$message['content'] = $ordertextclient . $row_mod['businessname'];

								$response = SendS($gateway, $message);
								
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_invoice['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ordertextclient . $row_mod['businessname']) . '\')');
							}
						}
						if ($row_mod['ordersadmin'] == 1)
						{
							
							$gateway['username']  = $row_mod['username'];
							$gateway['pass']    = $row_mod['password'];
							$gateway['number']    = $row_mod['number'];
							$message['numbers'] = $row_mod['adminmobile'];
							$message['content'] = $ordertextadmin . $row_mod['businessname'];

							$response = SendS($gateway, $message);
							
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'admin\', \'' . $row_mod['adminmobile'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $ordertextadmin . $row_mod['businessname']) . '\')');
						}
					}
					else
					{
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_invoice['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
					}
				}
			}
		}
		return true;
	}
	
	function after_create($vars)
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			if ($row_mod['modulecreate'] == 1)
			{
				$modulecreatetext = str_replace('{domain}', $vars['params']['domain'], $row_mod['modulecreatetext']);
				$modulecreatetext = str_replace('{username}', $vars['params']['username'], $modulecreatetext);
				$modulecreatetext = str_replace('{password}', $vars['params']['password'], $modulecreatetext);
				$tel          = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
				$row_tel      = @mysql_fetch_assoc($tel);
				$report          = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
				$row_report      = @mysql_fetch_assoc($report);
				if (($row_tel['id'] != '' && $row_report['id'] != ''))
				{
					$tel     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $vars['params']['clientsdetails']['userid'] . '\'');
					$row_tel = @mysql_fetch_assoc($tel);
					$reportal     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $vars['params']['clientsdetails']['userid'] . '\'');
					$row_reportal = @mysql_fetch_assoc($reportal);
					if ($row_tel['value'] == '')
					{
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $modulecreatetext . $row_mod['businessname']) . '\')');
						$error = 1;
					}
					if ($error != 1)
					{
						$row_tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_tel['value']);
						
						if($row_tel['value'][0] != '0') $row_tel['value'] = '0'.$row_tel['value'];
					}
					if ($error != 1)
					{
						if ($row_reportal['value'] == $row_mod['no_area'])
						{
							$error  = 1;
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $modulecreatetext . $row_mod['businessname']) . '\')');
						}
					}
					if ($error != 1)
					{
						$gateway['username']  = $row_mod['username'];
						$gateway['pass']    = $row_mod['password'];
						$gateway['number']    = $row_mod['number'];
						$message['numbers'] = $row_tel['value'];
						$message['content'] = $modulecreatetext . $row_mod['businessname'];

						$response = SendS($gateway, $message);
						
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $modulecreatetext . $row_mod['businessname']) . '\')');
					}
				}
				else
				{
					mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
				}
			}
		}
		return true;
	}
	
	function after_suspend($vars)
	{
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			if ($row_mod['modulesuspend'] == 1)
			{
				$modulesuspendtext = str_replace(array('{domain}', '{username}'), array($vars['params']['domain'], $vars['params']['username']), $row_mod['modulesuspendtext']);
				
				$tel           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text')));
				$row_tel       = @mysql_fetch_assoc($tel);
				$report           = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
				$row_report       = @mysql_fetch_assoc($report);
				if (($row_tel['id'] != '' && $row_report['id'] != ''))
				{
					$tel     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $vars['params']['clientsdetails']['userid'] . '\'');
					$row_tel = @mysql_fetch_assoc($tel);
					$reportal     = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $vars['params']['clientsdetails']['userid'] . '\'');
					$row_reportal = @mysql_fetch_assoc($reportal);
					if ($row_tel['value'] == '')
					{
						
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $modulesuspendtext . $row_mod['businessname']) . '\')');
						$error = 1;
					}
					if ($error != 1)
					{
						$row_tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $row_tel['value']);
						
						if($row_tel['value'][0] != '0') $row_tel['value'] = '0'.$row_tel['value'];
					}
					if ($error != 1)
					{
						if ($row_reportal['value'] == $row_mod['no_area'])
						{
							$error  = 1;
							mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $modulesuspendtext . $row_mod['businessname']) . '\')');
						}
					}
					if ($error != 1)
					{
						$gateway['username']  = $row_mod['username'];
						$gateway['pass']    = $row_mod['password'];
						$gateway['number']    = $row_mod['number'];
						$message['numbers'] = $row_tel['value'];
						$message['content'] = $modulesuspendtext . $row_mod['businessname'];

						$response = SendS($gateway, $message);
						
						mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $modulesuspendtext . $row_mod['businessname']) . '\')');
					}
				}
				else
				{
					mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $vars['params']['clientsdetails']['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
				}
			}
		}
		return true;
	}
	
	function daily_cron_job()
	{
<<<<<<< HEAD
		
		$mod     = @mysql_query('SELECT * FROM mod_smsaddon5');
		$row_mod = @mysql_fetch_assoc($mod);
		if ($mod)
		{
			if ($row_mod['new_bill'] == 1)
			{
				$daysbefore                = mysql_query('SELECT value FROM tblconfiguration WHERE setting=\'CreateInvoiceDaysBefore\'');
				$row_daysbefore            = mysql_fetch_assoc($daysbefore);
				$today                = $row_daysbefore['value'];
				$today1               = $today*86400;
				$begin                = time();
				$today2               = $today1 + $begin;
				$today3               = date('Y-m-d', $today2);
				$bill           = @mysql_query('SELECT userid, amount, nextduedate FROM tblhosting WHERE nextduedate=\'' . $today3 . '\' AND domainstatus=\'Active\'');
				$totalRows_bill = @mysql_num_rows($bill);
				$dateformat            = @mysql_query('SELECT value FROM tblconfiguration WHERE setting=\'DateFormat\'');
				$row_dateformat        = @mysql_fetch_assoc($dateformat);
				$row_tel               = @mysql_fetch_assoc(mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text'))));
				$report                = @mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text')));
				$row_report            = @mysql_fetch_assoc($report);

				if ($row_tel['id'] != '' && $row_report['id'] != '')
=======
		$row_mod = @mysql_fetch_assoc(mysql_query('SELECT * FROM mod_smsaddon'));
		if ($row_mod)
		{
			if ($row_mod['new_bill'] == 1)
			{
				$daysbefore         = mysql_query('SELECT value FROM tblconfiguration WHERE setting=\'CreateInvoiceDaysBefore\'');
				$row_daysbefore     = mysql_fetch_assoc($daysbefore);
				$begin              = time();
				$end               	= date('Y-m-d', ($row_daysbefore['value'] * 86400) + $begin );
				$bill           	= @mysql_query('SELECT userid, amount, nextduedate FROM tblhosting WHERE nextduedate=\'' . $end . '\' AND domainstatus=\'Active\'');
				$dateformat         = @mysql_query('SELECT value FROM tblconfiguration WHERE setting=\'DateFormat\'');
				$row_dateformat     = @mysql_fetch_assoc($dateformat);
				$tel_field          = @mysql_fetch_assoc(mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['mobilenumberfield'], 'text'))));
				$row_report         = @mysql_fetch_assoc(mysql_query(@sprintf('SELECT id FROM tblcustomfields WHERE fieldname=%s', @GetSQLValueString($row_mod['notificationfield'], 'text'))));
				if ($tel_field['id'] != '' && $row_report['id'] != '')
>>>>>>> origin/master
				{
					while ($row_bill = mysql_fetch_assoc($bill))
					{
						$date       = explode('-', $row_bill['nextduedate']);
						$history    = str_replace(array('YYYY', 'MM', 'DD'), array($date[0], $date[1], $date[2]), $row_dateformat['value']);
						
						$invoicetextclient = str_replace(array('{amount}', '{duedate}'), array($row_bill['amount'], $history), $row_mod['invoicetextclient']);

						$tel    	= @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $tel_field['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\''));

						$row_reportal    = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\''));
						if ($tel['value'] == '')
						{
							mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
							$error = 1;
						}
						if ($error != 1)
						{
							$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
							
							if($tel['value'][0] != '0') $tel['value'] = '0'. $tel['value'];
						}
						if ($error != 1)
						{
							if ($row_reportal['value'] == $row_mod['no_area'])
							{
								$error  = 1;
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
							}
						}
						if ($error != 1)
						{
							$gateway['number']  = $row_mod['username'];
							$gateway['pass']    = $row_mod['password'];
							$message['numbers'] = $tel['value'];
							$message['content'] = $invoicetextclient . $row_mod['businessname'];

							$responseA = SendSMS($gateway, $message);
							$response = $responseA->Status;
							mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
						}
					}
					
					$domainler           = @mysql_query('SELECT userid, recurringamount, nextduedate FROM tbldomains WHERE status=\'Active\' AND nextduedate=\'' . $today3 . '\'');
					while ($row_domainler = mysql_fetch_assoc($domainler))
					{
						$date            = explode('-', $row_domainler['nextduedate']);
						$history         = str_replace(array('YYYY', 'MM', 'DD'), array($date[0], $date[1], $date[2]), $row_dateformat['value']);
						$invoicetextclient = str_replace(array('{amount}', '{duedate}'), array($row_domainler['recurringamount'], $history), $row_mod['invoicetextclient']);
						$tel        = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $tel_field['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\''));

						$reportal        = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\'');
						$row_reportal    = @mysql_fetch_assoc($reportal);
						if ($tel['value'] == '')
						{
							mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
							$error = 1;
						}
						if ($error != 1)
						{
							$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
							
							if($tel['value'][0] != '0') $tel['value'] = '0'.$tel['value'];
						}
						if ($error != 1)
						{
							if ($row_reportal['value'] == $row_mod['no_area'])
							{
								$error  = 1;
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
							}
						}
						if ($error != 1)
						{
							$gateway['number']  = $row_mod['username'];
							$gateway['pass']    = $row_mod['password'];
							$message['numbers'] = $tel['value'];
							$message['content'] = $invoicetextclient . $row_mod['businessname'];

							$responseA = SendSMS($gateway, $message);
							$response = $responseA->Status;
							mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
						}
					}
					
					if ($row_mod['domainxdays'] > 0)
					{
						$begin               = time();
						$end            	 = date('Y-m-d', ($row_mod['domainxdays'] * 86400) + $begin);
						$domainler           = @mysql_query('SELECT userid, domain, recurringamount, expirydate, nextduedate FROM tbldomains WHERE expirydate=\'' . $end . '\' AND status=\'Active\'');
						while ($row_domainler = mysql_fetch_assoc($domainler))
						{
							$date              = explode('-', $row_domainler['expirydate']);
							$history           = str_replace(array('YYYY', 'MM', 'DD'), array($date[0], $date[1], $date[2]), $row_dateformat['value']);
							$invoicetextclient = str_replace(array('{remainingdays}', '{expirydate}', '{domain}'), array($row_mod['domainxdays'], $history, $row_domainler['domain']), $row_mod['domainxdaystext']);

							$tel    		   = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $tel_field['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\''));
							$reportal          = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\'');
							$row_reportal      = @mysql_fetch_assoc($reportal);
							if ($tel['value'] == '')
							{
<<<<<<< HEAD
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
=======
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
								$error = 1;
							}
							if ($error != 1)
							{
								$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
								
								if($tel['value'][0] != '0') $tel['value'] = '0'. $tel['value'];
							}
							if ($error != 1)
							{
								if ($row_reportal['value'] == $row_mod['no_area'])
								{
									$error  = 1;
<<<<<<< HEAD
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
=======
									mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
								}
							}
							if ($error != 1)
							{
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
<<<<<<< HEAD
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $tel['value'];
								$message['content'] = $invoicetextclient . $row_mod['businessname'];

								$response = SendS($gateway, $message);
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
=======
								$message['numbers'] = $tel['value'];
								$message['content'] = $invoicetextclient . $row_mod['businessname'];

								$responseA = SendSMS($gateway, $message);
								$response = $responseA->Status;
								
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
							}
						}
					}
					
					if ($row_mod['dueinvoice'] == 1)
					{
						$begin          = time();
						$end    	    = date('Y-m-d', $begin - 86400);
						$bill           = @mysql_query('SELECT userid, total, duedate FROM tblinvoices WHERE duedate=\'' . $end . '\' AND status=\'Unpaid\'');
						
						while ($row_bill = mysql_fetch_assoc($bill))
						{
							$date            = explode('-', $row_bill['duedate']);
							$history        = str_replace(array('YYYY', 'MM', 'DD'), array($date[0], $date[1], $date[2]), $row_dateformat['value']);
							$invoicetextclient = str_replace(array('{amount}', '{duedate}'), array($row_bill['total'], $history), $row_mod['dueinvoicetext']);

							$tel    = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $tel_field['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\''));
							$row_reportal    = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\''));
							
							if ($tel['value'] == '')
							{
<<<<<<< HEAD
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
=======
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
								$error = 1;
							}
							if ($error != 1)
							{
								$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
								
								if($tel['value'][0] != '0') $tel['value'] = '0'. $tel['value'];
							}
							if ($error != 1)
							{
								if ($row_reportal['value'] == $row_mod['no_area'])
								{
									$error  = 1;
<<<<<<< HEAD
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $row_tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
=======
									mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
								}
							}
							if ($error != 1)
							{
								$gateway['username']  = $row_mod['username'];
								$gateway['pass']    = $row_mod['password'];
<<<<<<< HEAD
								$gateway['number']    = $row_mod['number'];
								$message['numbers'] = $row_tel['value'];
								$message['content'] = $invoicetextclient . $row_mod['businessname'];

								$response = SendS($gateway, $message);
								mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $row_tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
							}
						}
					}
					
					$today = $row_mod['domainxdays'];
					if ($today > 0)
					{
						$today1            = $today*86400;
						$begin               = time();
						$today2            = $today1 + $begin;
						$today3            = date('Y-m-d', $today2);
						$domainler           = @mysql_query('SELECT userid, domain, recurringamount, expirydate, nextduedate FROM tbldomains WHERE expirydate=\'' . $today3 . '\' AND status=\'Active\'');
						$totalRows_domainler = @mysql_num_rows($domainler);
						if ($totalRows_domainler > 0)
						{
							while ($row_domainler = mysql_fetch_assoc($domainler))
							{
								$date              = explode('-', $row_domainler['expirydate']);
								$history           = str_replace('YYYY', $date[0], $row_dateformat['value']);
								$history		   = str_replace('MM', $date[1], $history);
								$history      	   = str_replace('DD', $date[2], $history);
								$invoicetextclient = str_replace('{remainingdays}', $row_mod['domainxdays'], $row_mod['domainxdaystext']);
								$invoicetextclient = str_replace('{expirydate}', $history, $invoicetextclient);
								$invoicetextclient = str_replace('{domain}', $row_domainler['domain'], $invoicetextclient);
								$tel    		   = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\''));
								$reportal          = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_domainler['userid'] . '\'');
								$row_reportal      = @mysql_fetch_assoc($reportal);
								if ($tel['value'] == '')
								{
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
									$error = 1;
								}
								if ($error != 1)
								{
									$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
									
									if($tel['value'][0] != '0') $tel['value'] = '0'.$tel['value'];
								}
								if ($error != 1)
								{
									if ($row_reportal['value'] == $row_mod['no_area'])
									{
										$error  = 1;
										mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
									}
								}
								if ($error != 1)
								{
									$gateway['username']  = $row_mod['username'];
									$gateway['pass']    = $row_mod['password'];
									$gateway['number']    = $row_mod['number'];
									$message['numbers'] = $tel['value'];
									$message['content'] = $invoicetextclient . $row_mod['businessname'];

									$response = SendS($gateway, $message);
									
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_domainler['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
								}
							}
						}
					}
					
					if ($row_mod['dueinvoice'] == 1)
					{
						
						$begin                  = time();
						$today2               = $begin - 86400;
						$today3               = date('Y-m-d', $today2);
						$bill           = @mysql_query('SELECT userid, total, duedate FROM tblinvoices WHERE duedate=\'' . $today3 . '\' AND status=\'Unpaid\'');
						$totalRows_bill = @mysql_num_rows($bill);
						if ($totalRows_bill > 0)
						{
							
							while ($row_bill = mysql_fetch_assoc($bill))
							{
								$date            = explode('-', $row_bill['duedate']);
								$history        = str_replace('YYYY', $date[0], $row_dateformat['value']);
								$history        = str_replace('MM', $date[1], $history);
								$history        = str_replace('DD', $date[2], $history);
								$invoicetextclient = str_replace('{amount}', $row_bill['total'], $row_mod['dueinvoicetext']);
								$invoicetextclient = str_replace('{duedate}', $history, $invoicetextclient);
								$tel    = @mysql_fetch_assoc(mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_tel['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\''));
								$reportal        = @mysql_query('SELECT value FROM tblcustomfieldsvalues WHERE fieldid=\'' . $row_report['id'] . '\' AND relid=\'' . $row_bill['userid'] . '\'');
								$row_reportal    = @mysql_fetch_assoc($reportal);
								if ($tel['value'] == '')
								{
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'\', \'Empty mobile number\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
									$error = 1;
								}
								if ($error != 1)
								{
									$tel['value'] = str_replace(array(' ', '-', '(', ')', ''), '', $tel['value']);
									
									if($tel['value'][0] != '0') $tel['value'] = '0'.$tel['value'];
								}
								if ($error != 1)
								{
									if ($row_reportal['value'] == $row_mod['no_area'])
									{
										$error  = 1;
										mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'Client doesn\'t want to receive text messages\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
									}
								}
								if ($error != 1)
								{
									$gateway['username']  = $row_mod['username'];
									$gateway['pass']    = $row_mod['password'];
									$gateway['number']    = $row_mod['number'];
									$message['numbers'] = $tel['value'];
									$message['content'] = $invoicetextclient . $row_mod['businessname'];

									$response = SendS($gateway, $message);
									mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
								}
=======
								$message['numbers'] = $tel['value'];
								$message['content'] = $invoicetextclient . $row_mod['businessname'];

								$responseA = SendSMS($gateway, $message);
								$response = $responseA->Status;
								mysql_query('INSERT INTO mod_smsaddon_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_bill['userid'] . '\', \'' . $tel['value'] . '\', \'' . $response . '\', \'' . str_replace('\'', '\'', $invoicetextclient . $row_mod['businessname']) . '\')');
>>>>>>> origin/master
							}
						}
					}
				}
				else
				{
					mysql_query('INSERT INTO mod_smsaddon5_logs(time, client, mobilenumber, result, text) VALUES (\'' . time() . '\', \'' . $row_invoice['userid'] . '\', \'\', \'Invalid module settings\', \'\')');
				}
			}
		}
		return true;
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
	
@add_hook('ClientAdd', 1, 'client_add', '' );
@add_hook('InvoicePaid', 1, 'invoice_paid', '' );
@add_hook('ClientChangePassword', 1, 'client_change_password', '');
@add_hook('TicketOpen', 1, 'ticket_open', '');
@add_hook('TicketAdminReply', 1, 'ticket_admin', '');
@add_hook('TicketUserReply', 1, 'ticket_client', '');
@add_hook('AfterShoppingCartCheckout', 1, 'after_checkout', '');
@add_hook('AfterModuleCreate', 1, 'after_create', '');
@add_hook('AfterModuleSuspend', 1, 'after_suspend', '');
@add_hook('DailyCronJob', 1, 'daily_cron_job', '');
?>
