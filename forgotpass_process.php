<?php
include('include/user_inc.php');

$faq = new user;
$obj_sendmail = new user;

	$loginid = $_POST["email_cell"];
	if($loginid==""){
		$_SESSION['login_msg1'] = "Please insert your email address!";
		//header("Location:".$obj_base_path->base_path()."/forgot_password.php");	
	}
			

 	$faq->checkAdminUser($loginid) ;
	if($faq->num_rows() > 0 ) 
	{	
		$faq->next_record();						
		$rem_password = $faq->f('rem_password');
		$email = $faq->f('email');
		
		$status = $faq->f('activate_status');
		
		//echo "hi ".$status; exit;
		//send password email
		//$obj_sendmail->forgot_pass($rem_password,$email);			

		if($status=='0')
		{
			if($_SESSION['langSessId']=='eng')
			{
				$_SESSION['for_pass_msg'] =  '<p style="color:red;">Account pending validation.<br />This account has been signed up but is waiting for validation. Please check your messages.<br />Do you want to resend the validation instructions?<br /><span onclick=showLink("'.$email.'")>Please <span style="font-size:14px; font-weight:bold;cursor: pointer;">click here </span> to  resend validation instructions</span>
				<span id="linkid" style="font-weight:bold;display:none">Message Send</span>
				</p>';
			}
			elseif($_SESSION['langSessId']=='spn')
			{
				$_SESSION['for_pass_msg'] = '<p style="color:red;">Cuenta en espera de validaci&oacute;n.<br />Esta cuenta ha sido registrado, pero est&aacute; esperando para su validaci&oacute;n. Favor de consultar sus mensajes.<br />&iquest;Quieres volver a enviar las instrucciones de validaci&oacute;n?<br /><span onclick=showLink("'.$email.'")>Haga <span style="font-size:14px; font-weight:bold;cursor: pointer;">clic aqu&#237; </span> para reenviar instrucciones de validaci&oacute;n.</span>
				<span id="linkid" style="font-weight:bold;display:none">Mensaje enviado</span>
				</p>'; 
			}
		}
		else
		{
			//redirect
			if($_SESSION['langSessId'] == 'eng')
			{
				//mail('unified.durba@gmail.com','Hello User','Hi Hi Hi Hi','brishti88@gmail.com');
				
				$output='<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
							 <tbody>
								 <tr>
									<td valign="top" align="center">
										<table width="100%" cellspacing="0" cellpadding="0">                  
											<tbody>
												<tr>
													<td style="background-color:#1a1c35;border-top:1px solid #57658e;border-bottom:1px solid #262f47">
														<center>
															<a target="_blank" style="color:#4c5a81;color:#4c5a81;color:#4c5a81" href="'.$obj_base_path->base_path().'">
															<img border="0" align="middle" alt="KPasapp" title="KPasapp" src="'.$obj_base_path->base_path().'/images/KPasapp_logo.png"></a>
														</center>
													</td>
												</tr>
											</tbody>
										</table>
										<table width="550" cellspacing="0" cellpadding="20" bgcolor="#FFFFFF">
											<tbody>
												<tr>
													<td valign="top" bgcolor="#FFFFFF" style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms;border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0;border-bottom:1px solid #e0e0e0">
														<p style="margin-top:0px"></p>
														<div style="color:#f3164f;font-family:arial;line-height:100%;padding:20px 0px">
															<p>You have requested to reset your KPasapp password. Please confirm this request by clicking on the link: 
															  <a target="_blank" href="'.$obj_base_path->base_path().'/resetpassword.php?user='.$faq->f('admin_id').'" style="font-weight:bold;"> '.$obj_base_path->base_path().'/resetpassword.php?user='.$faq->f('admin_id').' </a>
														    </p>
															<p><span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"><i>If you are unable to open the hyperlink above, copy and paste the URL into your internet browser.</i></span></p>
														</div>
													  <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">
															This request was made on '.date("F d, Y,  h:i a").'<br>	
															If you didn\'t make this request, let us know right away for your own security. It\'s important because it helps us make sure no one is getting into your account without your knowledge.
													</p>
														<p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"> Sincerely,<br>
														  The KPasapp Team	<br />
														  Do not reply to this email.<br />
														  Email us at info@kpasapp.com if you require additional assistance.
													</p>
<p></p>
													</td>
												</tr>
												<tr>
													<td valign="top" style="background-color:#e9e9e9;border-top:22px solid #f4f4f4;padding:10px 10px">
														<div style="font-size:10px;color:#666666;line-height:100%;font-family:verdana">
															<div style="float:right">
																<a target="_blank" href="https://twitter.com/KPasapp"><img width="" border="0" height="" src="'.$obj_base_path->base_path().'/images/twitter_icon.png"></a>
																<a target="_blank" href="https://www.facebook.com/master.kpasapp"><img width="" border="0" height="" src="'.$obj_base_path->base_path().'/images/facebook_icon.png"></a>
																<a target="_blank" href="#"><img width="43" border="0" src="'.$obj_base_path->base_path().'/images/youtube_icon.png"></a>
															</div>
															<div>
																Copyright &copy; 2013 <span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">Kpasapp</span>, Inc. All rights reserved.
															</div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								 </tr>
							 </tbody>
						 </table>';
				
				$obj_sendmail->forget_password_mail($email,$subject,$output,$from);
				$obj_sendmail->forget_password_mail('kpasapp@gmail.com',$subject,$output,$from);
				$obj_sendmail->forget_password_mail('unified.subhrajyoti@gmail.com',$subject,$output,$from);
				//$headers  = 'MIME-Version: 1.0' . "\r\n";
				//$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				//$headers .= 'From: info@kcpasa.com' . "\r\n";
				//@mail('kpasapp@gmail.com', $subject, $output, $headers);
				//@mail('unified.subhrajyoti@gmail.com', $subject, $body, $headers);
				//mail($to,$subject,$output,$from);
				
				$_SESSION['for_pass_msg'] =  "We've sent a message with information about resetting your password to Email Address ".$email.". <br />Check the message and follow instructions to reset your password.";
			}
			else
			{
				$output='<table width="100%" cellspacing="0" cellpadding="0" bgcolor="#f4f4f4">
							 <tbody>
								 <tr>
									<td valign="top" align="center">
										<table width="100%" cellspacing="0" cellpadding="0">                  
											<tbody>
												<tr>
													<td style="background-color:#1a1c35;border-top:1px solid #57658e;border-bottom:1px solid #262f47">
														<center>
															<a target="_blank" style="color:#4c5a81;color:#4c5a81;color:#4c5a81" href="'.$obj_base_path->base_path().'">
															<img border="0" align="middle" alt="KPasapp" title="KPasapp" src="'.$obj_base_path->base_path().'/images/KPasapp_logo.png"></a>
														</center>
													</td>
												</tr>
											</tbody>
										</table>
										<table width="550" cellspacing="0" cellpadding="20" bgcolor="#FFFFFF">
											<tbody>
												<tr>
													<td valign="top" bgcolor="#FFFFFF" style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms;border-left:1px solid #e0e0e0;border-right:1px solid #e0e0e0;border-bottom:1px solid #e0e0e0">
														<p style="margin-top:0px"></p>
														<div style="color:#f3164f;font-family:arial;line-height:100%;padding:20px 0px">
															<p>Han pedido reinicializar su contrase�a KPasapp. Por favor, confirme esta solicitud haciendo clic en el enlace 
															  <a target="_blank" href="'.$obj_base_path->base_path().'/resetpassword.php?user='.$faq->f('admin_id').'" style="font-weight:bold;"> '.$obj_base_path->base_path().'/resetpassword.php?user='.$faq->f('admin_id').' </a>
														    </p>
															<p><span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"><i>Si no puede abrir el hiperv�nculo anterior, copie y pegue el URL en su navegador de Internet.</i></span></p>
														</div>
													  <p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">
															Esta solicitud se hizo en '.date("F d, Y,  h:i a").'<br>
															Si no hiciste esta solicitud, h�ganoslo saber de inmediato para su propia seguridad. Es importante porque nos ayuda a asegurarnos de que nadie se est� metiendo en su cuenta sin su conocimiento.														</p>
														<p style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms"> Atentamente,<br>
														  El Equipo KPasapp<br />
														  No responda a este correo electr�nico.<br />
														  Env�anos un email a info@kpasapp.com si necesita ayuda adicional.</p>
<p></p>
													</td>
												</tr>
												<tr>
													<td valign="top" style="background-color:#e9e9e9;border-top:22px solid #f4f4f4;padding:10px 10px">
														<div style="font-size:10px;color:#666666;line-height:100%;font-family:verdana">
															<div style="float:right">
																<a target="_blank" href="https://twitter.com/KPasapp"><img width="" border="0" height="" src="'.$obj_base_path->base_path().'/images/twitter_icon.png"></a>
																<a target="_blank" href="https://www.facebook.com/master.kpasapp"><img width="" border="0" height="" src="'.$obj_base_path->base_path().'/images/facebook_icon.png"></a>
																<a target="_blank" href="#"><img width="43" border="0" src="'.$obj_base_path->base_path().'/images/youtube_icon.png"></a>
															</div>
															<div>
																Copyright &copy; 2013 <span style="font-size:12px;color:#444444;line-height:150%;font-family:trebuchet ms">Kpasapp</span>, Inc. All rights reserved.
															</div>
														</div>
													</td>
												</tr>
											</tbody>
										</table>
									</td>
								 </tr>
							 </tbody>
						 </table>';
				
				$obj_sendmail->forget_password_mail($email,$subject,$output,$from);
				$obj_sendmail->forget_password_mail('kpasapp@gmail.com',$subject,$output,$from);
				$obj_sendmail->forget_password_mail('unified.subhrajyoti@gmail.com',$subject,$output,$from);	
				//mail($to,$subject,$output,$from);
				
				$_SESSION['for_pass_msg'] =  "Hemos enviado un mensaje con la informaci&oacute;n sobre c&oacute;mo reinicializar la contrase&oacute;a de  ".$email.". <br />Compruebe el mensaje y siga las instrucciones para restablecer tu contrase&oacute;a.
";
			
			
			}
			
		}
		echo 1;
	}
	else{
		echo 0;
		
	}

?>		
  

	  
		  