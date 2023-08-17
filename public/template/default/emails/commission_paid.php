<?php include 'includes/header.php';


$involved_accounts = $commission->involved_accounts;
$line = $involved_accounts->where('credit', '>', 0)->first();
$account = $line->chart_of_account;

?>

</td>
</tr>
</tbody>
</table>


<div style="font-family: Arial, sans-serif; line-height: 20px; color: #444444; font-size: 13px;">
    <b style="color: #777777; text-transform: lowercase;"></b>

    <?php
    $message = <<<EOL

        <p>Hi [FIRSTNAME],</p>

        <p>You just received a commission of [CURRENCY][AMOUNT] on a sale.</p>
        <p>Your balance is [CURRENCY][BALANCE].</p>

        <p> Show off and drive more sales.</p>

        <br>
        <br>
        <p>Regards,<br>
            Convertbetcodes Team <br>
            
        </p>
    
EOL;

    $message = str_replace("[AMOUNT]", "<b>$commission->c_amount</b>", $message);
    $message = str_replace("[FIRSTNAME]", "<b>{$account->owner->fullname}</b>", $message);
    $message = str_replace("[CURRENCY]", "<b>$commission->currency</b>", $message);
    $message = str_replace("[BALANCE]", "<b>$line->a_post_available_balance</b>", $message);

    echo $message; ?>
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