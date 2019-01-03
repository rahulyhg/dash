<?php
namespace dash\utility\payment\pay;


class parsian
{


    /**
     * pay by parsian payment
     * check config of parsian
     * save transaction by conditon request
     * redirect to payment url
     * @param      <type>  $_user_id  The user identifier
     * @param      <type>  $_amount   The amount
     * @param      array   $_options  The options
     */
    public static function parsian($_user_id, $_amount, $_options = [])
    {
        $log_meta =
        [
            'data' => \dash\utility\payment\pay::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
            ]
        ];


        if(!\dash\option::config('parsian', 'status'))
        {
            \dash\db\logs::set('pay:parsian:status:false', $_user_id, $log_meta);
            \dash\notif::error(T_("The parsian payment on this service is locked"));
            return false;
        }

        if(!\dash\option::config('parsian', 'LoginAccount'))
        {
            \dash\db\logs::set('pay:parsian:LoginAccount:not:set', $_user_id, $log_meta);
            \dash\notif::error(T_("The parsian payment LoginAccount not set"));
            return false;
        }

        $parsian = [];
        $parsian['LoginAccount'] = \dash\option::config('parsian', 'LoginAccount');

        if(\dash\option::config('parsian', 'CallBackUrl'))
        {
            $parsian['CallBackUrl'] = \dash\option::config('parsian', 'CallBackUrl');
        }
        else
        {
            $parsian['CallBackUrl'] = \dash\utility\payment\pay::get_callbck_url('parsian');
        }

        // change rial to toman
        // but the plus is toman
        // need less to *10 the plus
        $parsian['Amount'] = floatval($_amount) * 10;

        $transaction_start =
        [
            'caller'         => 'payment:parsian',
            'title'          => T_("Pay by parsian payment"),
            'user_id'        => $_user_id,
            'plus'           => $_amount,
            'payment'        => 'parsian',
            'type'           => 'money',
            'unit'           => 'toman',
            'date'           => date("Y-m-d H:i:s"),
            'amount_request' => $_amount,
        ];

        if(isset($_options['other_field']) && is_array($_options['other_field']))
        {
            $transaction_start['other_field'] = $_options['other_field'];
        }

        //START TRANSACTION BY CONDITION REQUEST
        $transaction_id = \dash\utility\payment\transactions::start($transaction_start);

        $log_meta['data'] = \dash\utility\payment\pay::$log_data = $transaction_id;

        if(!\dash\engine\process::status() || !$transaction_id)
        {
            return false;
        }

        if(isset($_options['turn_back']))
        {
            // save turn back url to redirect user to this url after coplete pay
            $_SESSION['turn_back'][$transaction_id] = $_options['turn_back'];
        }

        $parsian['OrderId'] = $transaction_id;

        \dash\utility\payment\payment\parsian::$user_id = $_user_id;
        \dash\utility\payment\payment\parsian::$log_data = \dash\utility\payment\pay::$log_data;

        $redirect = \dash\utility\payment\payment\parsian::pay($parsian);

        $payment_response = \dash\utility\payment\payment\parsian::$payment_response;
        if($redirect)
        {
            if(isset($payment_response->SalePaymentRequestResult->Token))
            {
                // save amount and autority in session to get when verifying
                $_SESSION['amount']['parsian'][$payment_response->SalePaymentRequestResult->Token]                   = [];
                $_SESSION['amount']['parsian'][$payment_response->SalePaymentRequestResult->Token]['amount']         = floatval($_amount) * 10;
                $_SESSION['amount']['parsian'][$payment_response->SalePaymentRequestResult->Token]['transaction_id'] = $transaction_id;

                $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);
                \dash\utility\payment\transactions::update(['condition' => 'redirect', 'payment_response' => $payment_response], $transaction_id);
                \dash\redirect::to($redirect);
                return true;
            }
            else
            {
                \dash\db\logs::set('pay:parsian:Token:not:set', $_user_id, $log_meta);
                \dash\notif::error(T_("The parsian payment Token not set"));
                return false;
            }
        }
        else
        {
            return false;
        }
    }

}
?>