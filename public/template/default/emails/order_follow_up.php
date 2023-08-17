<?php include 'includes/header.php';


?>

</td>
</tr>
</tbody>
</table>
<div style="font-family: Arial, sans-serif; line-height: 20px; color: #444444; font-size: 13px;">
	<b style="color: #777777; text-transform: lowercase;"></b>
	<?php
	$user = $order->user;
	$rollable_body =  $this->buildView('composed/notice_for_rollable_units', compact('user'), true, true);
	$amount = MIS::money_format($order->price);
	$message = <<<EOL
   <p>Hi [FIRSTNAME],</p>

<p>Please complete your order to continue using our service.</p>

<p><span style="color:#e74c3c"><strong>Don't be left behind!</strong></span></p>

<p>Order: <strong>{$order->details['name']} for {$order->details['expires_at']}days @{$order->PaymentDetailsArray['currency']}{$amount} </strong></p>

<h4><a href="$domain/user/pay_selected_plan/{$order->details['id']}#unit_packs"><u>Complete your payment now!</u></a></h4>

<p>&nbsp;</p>

<p><u>Having issues with payment?</u></p>

<p> When checking out, choose the payment method as follow;</p>

<ul>
	<li><b>For Nigerians</b>
	<ul>
		<li>Change the currency at the top-right to <strong>NGN</strong></li>
		<li>Use F<strong>lutterwave</strong> or <strong>Paystack&nbsp;</strong></li>
	</ul>
	</li>
	<li><b>For Ghanaians</b>
	<ul>
		<li><strong>​​​​​​​</strong>Change the currency at the top-right to <strong>GHS</strong></li>
		<li>Use <strong>MobileMoney</strong>(requires confirmation) or <strong>MobileMoney -cashramp</strong>(auto confirmation)</li>
	</ul>
	</li>
	<li><b>For Others</b>
	<ul>
		<li>Use <strong>Flutterwave</strong> Then select <b>GOOGLE PAY</b> works best with <b>USD</b></li>
		<li>Use <strong>Coinbase</strong> if you prefer to pay with crypto</li>
		<li>Use <strong>Paypal (manual) </strong>requires confirmation.</li>
	</ul>
	</li>
	<li>Need further help? 
	<ul>
		<li><a href="https://t.me/convertbetcodes">Talk to the community on telegram</a></li>
	</ul>
	</li>
</ul>

<p>&nbsp;</p>
<h4><a href="$domain/user/pay_selected_plan/{$order->details['id']}#unit_packs"><u>Complete your payment now!</u></a></h4>


<p>&nbsp;</p>

<p>&nbsp;</p>

    $rollable_body

    <p>&nbsp;</p>
    
EOL;

	$message = str_replace("[FIRSTNAME]", "<b>{$user->firstname}</b>", $message);
	$message = str_replace("[INF]", "<b>unlimited</b>", $message);
	echo $message;
	?>
</div>


</td>
</tr>
</tbody>
</table>
</td>
</tr>
</tbody>
</table>







<?php include 'includes/footer.php'; ?>