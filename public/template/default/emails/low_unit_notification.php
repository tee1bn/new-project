<?php include 'includes/header.php';


?>

</td>
</tr>
</tbody>
</table>
<div style="font-family: Arial, sans-serif; line-height: 20px; color: #444444; font-size: 13px;">
    <b style="color: #777777; text-transform: lowercase;"></b>
    <?php

    $rollable_body =  $this->buildView('composed/notice_for_rollable_units', compact('user'), true, true);
    $message = <<<EOL
                <p>Hi [FIRSTNAME],</p>
                <p>[NOTICE_MESSAGE]</p>
                <p>Kindly renew your subscription or buy more unit to avoid interrupted service.</p>

                <p> 
                $rollable_body
                </p>
                
            
                <a href="$domain/pg/pricing">Renew Now</a>


                <br>
                <br>
                <p>Thank you for choosing us.</p>

            EOL;
    $message = str_replace("[FIRSTNAME]", "<b>{$user->fullname}</b>", $message);
    $message = str_replace("[NOTICE_MESSAGE]", $notice_message, $message);
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