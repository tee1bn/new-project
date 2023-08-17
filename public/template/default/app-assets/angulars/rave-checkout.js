function payWithRave($order) {
    var x = getpaidSetup({
        PBFPubKey: $order.api_keys,
        customer_email: $order.email,
        amount: $order.amount,
        customer_phone: $order.phone,
        currency: $order.currency,
        txref: $order.ref,
        meta: $order.custom_fields,
        onclose: function() {},
        callback: function(response) {
            var txref = response.tx.txRef; // collect txRef returned and pass to a          server page to complete status check.

            var verifying = $.get($base_url + "/shop/callback?order_unique_id=" + $order.order_unique_id + "&item_purchased=" + $order.name_in_shop);

            verifying.done(function(data) {

                /* give value saved in data */
                /*   console.log($order);
                  console.log(data);
                  console.log(response); */

                window.notify();

                if (data.order.paid_at != null) {
                    location.href = data.url;
                }
            });

            x.close(); // use this to close the modal immediately after payment.
        }
    });
}