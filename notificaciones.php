<?php
/* PARA VER QUÉ INFORMACIÓN RECIBO DE MP */
    echo '<pre>';
    print_r($_GET);
    echo '</pre>';
/**/
    MercadoPago\SDK::setAccessToken("APP_USR-6317427424180639-042414-47e969706991d3a442922b0702a0da44-469485398");

    $merchant_order = null;

    switch($_GET["topic"]) {
        case "payment":
            $payment = MercadoPago\Payment::find_by_id($_GET["id"]);
            // Obtenga el pago y el correspondiente merchant_order informado por el IPN.
            $merchant_order = MercadoPago\MerchantOrder::find_by_id($payment->order->id);
            break;
        case "merchant_order":
            $merchant_order = MercadoPago\MerchantOrder::find_by_id($_GET["id"]);
            break;
    }

    $paid_amount = 0;
    foreach ($merchant_order->payments as $payment) {
        if ($payment['status'] == 'approved'){
            $paid_amount += $payment['transaction_amount'];
        }
    }

    // Si el monto de la transacción del pago es igual (o mayor) que el monto de la orden comerciante, puede liberar sus artículos
    if($paid_amount >= $merchant_order->total_amount){
        // El merchant_order tiene envíos
        if (count($merchant_order->shipments) > 0) {
            if($merchant_order->shipments[0]->status == "ready_to_ship") {
                print_r("Totalmente pagado. Imprima la etiqueta y retire su artículo.");
            }
        } else { // El merchant_order no tiene ningún envío
            print_r("Totalmente pagado. Libera tu artículo.");
        }
    } else {
        print_r("Aún no pagado. No libere su artículo.");
    }
?>
