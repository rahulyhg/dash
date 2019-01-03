<?php
namespace dash\utility\payment\verify;


class irkish
{

    /**
     * { function_description }
     *
     * @param      <type>  $_args  The arguments
     */
    public static function irkish($_args)
    {
        \dash\utility\payment\verify::config();

        $log_meta =
        [
            'data' => \dash\utility\payment\verify::$log_data,
            'meta' =>
            [
                'input'   => func_get_args(),
            ]
        ];

        if(!\dash\option::config('irkish', 'status'))
        {
            \dash\db\logs::set('pay:irkish:status:false', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The irkish payment on this service is locked"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(!\dash\option::config('irkish', 'merchantId'))
        {
            \dash\db\logs::set('pay:irkish:merchantId:not:set', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The irkish payment merchantId not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        $token         = isset($_REQUEST['token'])          ? (string) $_REQUEST['token']           : null;
        $merchantId    = isset($_REQUEST['merchantId'])     ? (string) $_REQUEST['merchantId']      : null;
        $resultCode    = isset($_REQUEST['resultCode'])     ? (string) $_REQUEST['resultCode']      : null;
        $paymentId     = isset($_REQUEST['paymentId'])      ? (string) $_REQUEST['paymentId']       : null;
        $InvoiceNumber = isset($_REQUEST['InvoiceNumber'])  ? (string) $_REQUEST['InvoiceNumber']   : null;
        $referenceId   = isset($_REQUEST['referenceId'])    ? (string) $_REQUEST['referenceId']     : null;
        $amount        = isset($_REQUEST['amount'])         ? (string) $_REQUEST['amount']          : null;
        $amount        = str_replace(',', '', $amount);

        if(!$token)
        {
            \dash\db\logs::set('pay:irkish:token:verify:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The irkish payment token not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(!$resultCode)
        {
            \dash\db\logs::set('pay:irkish:resultCode:verify:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("The irkish payment resultCode not set"));
            return \dash\utility\payment\verify::turn_back();
        }

        if(isset($_SESSION['amount']['irkish'][$token]['transaction_id']))
        {
            $transaction_id  = $_SESSION['amount']['irkish'][$token]['transaction_id'];
        }
        else
        {
            \dash\db\logs::set('pay:irkish:SESSION:transaction_id:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find your transaction"));
            return \dash\utility\payment\verify::turn_back();
        }

        $log_meta['data'] = \dash\utility\payment\verify::$log_data = $transaction_id;

        $irkish                    = [];
        $irkish['merchantId']      = \dash\option::config('irkish', 'merchantId');
        $irkish['token']           = $token;
        $irkish['amount']          = $amount;
        $irkish['referenceNumber'] = (string) $referenceId;
        $irkish['sha1Key']         = \dash\option::config('irkish', 'sha1');

        if(isset($_SESSION['amount']['irkish'][$token]['amount']))
        {
            $amount_SESSION  = floatval($_SESSION['amount']['irkish'][$token]['amount']);
        }
        else
        {
            \dash\db\logs::set('pay:irkish:SESSION:amount:not:found', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        if($amount_SESSION != $amount)
        {
            \dash\db\logs::set('pay:irkish:amount_SESSION:amount:is:not:equals', \dash\utility\payment\verify::$user_id, $log_meta);
            \dash\notif::error(T_("Your session is lost! We can not find amount"));
            return \dash\utility\payment\verify::turn_back();
        }

        $update =
        [
            'amount_end'       => $amount / 10,
            'condition'        => 'pending',
            'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
        ];

        \dash\utility\payment\transactions::update($update, $transaction_id);

        \dash\db\logs::set('pay:irkish:pending:request', \dash\utility\payment\verify::$user_id, $log_meta);

        // $msg = \dash\utility\payment\payment\irkish::msg($resultCode);

        if(intval($resultCode) === 100)
        {
            \dash\utility\payment\payment\irkish::$user_id = \dash\utility\payment\verify::$user_id;

            \dash\utility\payment\payment\irkish::$log_data = \dash\utility\payment\verify::$log_data;

            $is_ok = \dash\utility\payment\payment\irkish::verify($irkish);

            $payment_response = \dash\utility\payment\payment\irkish::$payment_response;

            $log_meta['meta']['payment_response'] = (array) $payment_response;

            $payment_response = json_encode((array) $payment_response, JSON_UNESCAPED_UNICODE);

            if($is_ok)
            {
                $update =
                [
                    'amount_end'       => $amount_SESSION / 10,
                    'condition'        => 'ok',
                    'verify'           => 1,
                    'payment_response' => $payment_response,
                ];

                \dash\utility\payment\verify::$final_verify         = true;
                \dash\utility\payment\verify::$final_transaction_id = $transaction_id;


                \dash\utility\payment\transactions::calc_budget($transaction_id, $amount_SESSION / 10, 0, $update);

                \dash\db\logs::set('pay:irkish:ok:request', \dash\utility\payment\verify::$user_id, $log_meta);

                \dash\session::set('payment_verify_status', 'ok');
                \dash\session::set('payment_verify_amount', $amount_SESSION / 10);

                unset($_SESSION['amount']['irkish'][$token]);

                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
            else
            {
                $update =
                [
                    'amount_end'       => $amount_SESSION / 10,
                    'condition'        => 'verify_error',
                    'payment_response' => $payment_response,
                ];

                \dash\session::set('payment_verify_status', 'verify_error');

                \dash\utility\payment\transactions::update($update, $transaction_id);
                \dash\db\logs::set('pay:irkish:verify_error:request', \dash\utility\payment\verify::$user_id, $log_meta);
                return \dash\utility\payment\verify::turn_back($transaction_id);
            }
        }
        else
        {
            $update =
            [
                'amount_end'       => $amount_SESSION / 10,
                'condition'        => 'error',
                'payment_response' => json_encode((array) $_args, JSON_UNESCAPED_UNICODE),
            ];

            \dash\session::set('payment_verify_status', 'error');

            \dash\utility\payment\transactions::update($update, $transaction_id);
            \dash\db\logs::set('pay:irkish:error:request', \dash\utility\payment\verify::$user_id, $log_meta);
            return \dash\utility\payment\verify::turn_back($transaction_id);
        }
    }
}
?>