<?php
error_reporting(0);
if (!defined('_MOKA_API_URL_'))
    define('_MOKA_API_URL_', 'https://service.moka.com/PaymentDealer/DoDirectPayment');
if (!defined('_MOKA_3D_URL_'))
    define('_MOKA_3D_URL_', 'https://service.moka.com/PaymentDealer/DoDirectPaymentThreeD');

class ControllerPaymentMokaPayment extends Controller {

    private $order_prefix = "opencart156_";

    public function index() {

        $this->language->load('payment/moka_payment');
        $this->load->model('payment/moka_payment');
        $this->load->model('checkout/order');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $cart_total_amount = round($order_info['total'] * $order_info['currency_value'], 2);

        $this->data['cart_total'] = $cart_total_amount;
        $this->data['code'] = $this->language->get('code');
        $this->data['text_credit_card'] = $this->language->get('text_credit_card');
        $this->data['text_wait'] = $this->language->get('text_wait');
        $this->data['button_confirm'] = $this->language->get('button_confirm');
        $this->data['continue'] = $this->url->link('checkout/success');
        $this->data['error_page'] = $this->url->link('checkout/error');
        $validform = md5($order_info['order_id'] . $order_info['store_url']);

        $this->data['validform'] = $validform;



        $order_id = $this->session->data['order_id'];
        $unique_conversation_id = uniqid($this->order_prefix) . "_" . $order_id;
        if (!isset($this->session->data['order_id']) OR ! $this->session->data['order_id'])
            die('Sipariş ID bulunamadı');
        if (!isset($this->session->data['order_id']) OR ! $this->session->data['order_id']) {
            die('Sipariş ID bulunamadı');
        }
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);

        $this->data['orderid'] = $order_id;
        $record = array('result_code' => false, 'result_message' => false);
        $validform = md5($order_info['order_id'] . $order_info['store_url']);

        if (isset($this->request->post['validform']) && $this->request->post['validform'] == $validform) {

            $record = $this->PostMokaForm();
        }

        if (isset($_POST['isSuccessful']) AND $_POST['isSuccessful']) {
            $record['result_code'] = $_POST['resultCode'];
            $record['result_message'] = $_POST['resultMessage'];
            $record['result'] = $_POST['isSuccessful'] == 'True' ? true : false;
        }


        if (isset($record['result']) AND $record['result']) {
            $record['id_order'] = $order_id;
            $comment = $this->record2Table($this->getRecordById($order_id));
            $this->session->data['payment_method']['code'] = 'moka_payment';
            $this->model_checkout_order->confirm($this->session->data['order_id'], $this->config->get('config_order_status_id'));
            $this->model_checkout_order->update($order_id, $this->config->get('moka_payment_order_status_id'), $comment, false);
            $this->response->redirect($this->url->link('checkout/success', '', 'SSL'));
        }
        if (isset($this->request->post['resultMessage'])) {
            $this->session->data['error'] = $this->request->post['resultMessage'];

            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
        }
        require_once(DIR_SYSTEM . 'library/mokapayment/mokaconfig.php');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);
        $total_cart = round($order_info['total'] * $order_info['currency_value'], 2);
        $moka_rates = MokaConfig::calculatePrices($total_cart, $this->config->get('moka_rates'));

        $this->data['installments_mode'] = $this->config->get('moka_payment_installement');
        $this->data['rates'] = $moka_rates;
        $this->data['showtotal'] = $total_cart . ' ' . $order_info['currency_code'];

        $this->data['action'] = $this->url->link('payment/moka_payment/', '', 'SSL');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/payment/moka_payment.tpl')) {
            $this->template = $this->config->get('config_template') . '/template/payment/moka_payment.tpl';
        } else {
            $this->template = 'default/template/payment/moka_payment.tpl';
        }
        $this->render();
    }

    function PostMokaForm() {
        $this->load->model('checkout/order');
        include(DIR_SYSTEM . 'library/mokapayment/mokaconfig.php');
        $order_info = $this->model_checkout_order->getOrder($this->session->data['order_id']);


        $record = array(
            'result_code' => '0',
            'result_message' => '',
            'result' => false
        );

        $total = $this->request->post['mokatotal'];

        require_once(DIR_SYSTEM . 'library/mokapayment/mokaconfig.php');


        $name = $this->request->post['card-name'];
        $number = $this->request->post['number'];
        $expiry = $this->request->post['expiry'];
        $cvc = $this->request->post['cvc'];
        $total = $_POST['mokatotal'];

        $expiry = explode("/", $expiry);
        $expiryMM = $expiry[0];
        $expiryYY = $expiry[1];
        $expiryMM = $this->replaceSpace($expiryMM);
        $expiryYY = $this->replaceSpace($expiryYY);
        $number = $this->replaceSpace($number);

        if (is_array($total)) {
            $bankalar = MokaConfig::getAvailablePrograms();
            foreach ($bankalar as $key => $value) {

                $isim = $key;
                for ($x = 1; $x <= 12; $x++) {

                    $taksit = $total[$key][$x];
                    if (!empty($taksit)) {
                        $installement = $x;

                        $paid = number_format($taksit, 2, '.', '');
                    }
                }
            }
        }
        if (empty($paid)) {
            $taksit = $total;
            $paid = number_format($taksit, 2, '.', '');
            $installement = 1;
        }


        $orderid = $this->session->data['order_id'];
        $moka_username = $this->config->get('moka_payment_username');
        $moka_password = $this->config->get('moka_payment_password');
        $moka_dealercode = $this->config->get('moka_payment_dealercode');
        $moka_3d_mode = $this->config->get('moka_payment_moka_3d_mode');



        $moka = array();

        $moka['PaymentDealerAuthentication'] = array(
            'DealerCode' => $moka_dealercode,
            'Username' => $moka_username,
            'Password' => $moka_password,
            'CheckKey' => hash('sha256', $moka_dealercode . 'MK' . $moka_username . 'PD' . $moka_password)
        );

        $moka['PaymentDealerRequest'] = array(
            'CardHolderFullName' => $name,
            'CardNumber' => $number,
            'ExpMonth' => $expiryMM,
            'ExpYear' => '20' . $expiryYY,
            'CvcNumber' => $cvc,
            'Amount' => $paid,
            'Currency' => $order_info['currency_code'] == 'TRY' ? 'TL' : $order_info['currency_code'],
            'InstallmentNumber' => (string) $installement,
            'OtherTrxCode' => (string) $orderid,
            'ClientIP' => $order_info['ip'],
            'Software' => 'Opencart-156',
            'RedirectUrl' => $this->url->link('payment/moka_payment/', '', 'SSL')
        );


        if ($moka_3d_mode == 'OFF') {
            $gateway_url = _MOKA_API_URL_;
        } else {

            $gateway_url = _MOKA_3D_URL_;
        }


        $result = json_decode($this->curlPostExt(json_encode($moka), $gateway_url, true));

        if (!$result OR $result == NULL) {
            $record['result_code'] = 'CURL-LOAD_ERROR';
            $record['result_message'] = 'WebServis Error ';
            return $record;
        }

        if (isset($result->ResultCode) AND $result->ResultCode == "Success") {
            if ($moka_3d_mode != 'OFF')
                header("Location:" . $result->Data);
            if (isset($result->Data->IsSuccessful) AND $result->Data->IsSuccessful) {
                $record['result_code'] = '99';
                $record['result_message'] = $result->ResultCode;
                $record['result'] = true;
                return $record;
            }



            $record['result_code'] = isset($result->Data->ResultCode) ? $result->Data->ResultCode : 'UKN-01';
            $record['result_message'] = $errr;
            return $record;
        } else {
            $ResultCode = $result->ResultCode;

            switch ($ResultCode) {
                case "PaymentDealer.CheckPaymentDealerAuthentication.InvalidRequest":
                    $errr = "Hatalı hash bilgisi";
                    break;
                case "PaymentDealer.RequiredFields.AmountRequired":
                    $errr = "Tutar Göndermek Zorunludur.";
                    break;
                case "PaymentDealer.RequiredFields.ExpMonthRequired":
                    $errr = "Son Kullanım Tarihi Gönderme Zorunludur.";
                    break;

                case "PaymentDealer.CheckPaymentDealerAuthentication.InvalidAccount":
                    $errr = "Böyle bir bayi bulunamadı";
                    break;
                case "PaymentDealer.CheckPaymentDealerAuthentication.VirtualPosNotFound":
                    $errr = "Bu bayi için sanal pos tanımı yapılmamış";
                    break;
                case "PaymentDealer.CheckDealerPaymentLimits.DailyDealerLimitExceeded":
                    $errr = "Bayi için tanımlı günlük limitlerden herhangi biri aşıldı";
                    break;
                case "PaymentDealer.CheckDealerPaymentLimits.DailyCardLimitExceeded":
                    $errr = "Gün içinde bu kart kullanılarak daha fazla işlem yapılamaz";

                case "PaymentDealer.CheckCardInfo.InvalidCardInfo":
                    $errr = "Kart bilgilerinde hata var";
                    break;
                case "PaymentDealer.DoDirectPayment3dRequest.InstallmentNotAvailableForForeignCurrencyTransaction":

                    $errr = "Yabancı para ile taksit yapılamaz";
                    break;
                case "PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForDealer":
                    $errr = "Bu taksit sayısı bu bayi için yapılamaz";
                    break;
                case "PaymentDealer.DoDirectPayment3dRequest.InvalidInstallmentNumber":
                    $errr = "Taksit sayısı 2 ile 9 arasıdır";
                    break;
                case "PaymentDealer.DoDirectPayment3dRequest.ThisInstallmentNumberNotAvailableForVirtualPos":
                    $errr = "Sanal Pos bu taksit sayısına izin vermiyor";
                    break;

                default:
                    $errr = "Beklenmeyen Bir hata Oluştu";
            }

            $this->session->data['error'] = $errr;
            $this->response->redirect($this->url->link('checkout/checkout', '', 'SSL'));
            $record['result_code'] = $result->ResultCode;
            $record['result_message'] = $errr . ' (' . $result->ResultCode . ')';
        }

        return $record;
    }

    private function curlPostExt($data, $url, $json = false) {
        $ch = curl_init(); // initialize curl handle
        curl_setopt($ch, CURLOPT_URL, $url); // set url to post to
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return into a variable
        if ($json)
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30); // times out after 4s
        curl_setopt($ch, CURLOPT_POST, 1); // set POST method
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data); // add POST fields
        if ($result = curl_exec($ch)) { // run the whole process
            curl_close($ch);

            return $result;
        }
    }

    public function getSiteUrl() {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $site_url = is_null($this->config->get('config_ssl')) ? HTTPS_SERVER : $this->config->get('config_ssl');
        } else {
            $site_url = is_null($this->config->get('config_url')) ? HTTP_SERVER : $this->config->get('config_url');
        }
        return $site_url;
    }

    public function getServerConnectionSlug() {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) {
            $connection = 'SSL';
        } else {
            $connection = 'NONSSL';
        }

        return $connection;
    }

    private function _getCurrencySymbol($currencyCode) {
        $currencySymbol = $this->currency->getSymbolLeft($currencyCode);
        if ($currencySymbol == '') {
            $currencySymbol = $this->currency->getSymbolRight($currencyCode);
        } else if ($currencySymbol == '') {
            $currencySymbol = $currencyCode;
        }
        return $currencySymbol;
    }

    public function replaceSpace($veri) {
        $veri = str_replace("/s+/", "", $veri);
        $veri = str_replace(" ", "", $veri);
        $veri = str_replace(" ", "", $veri);
        $veri = str_replace(" ", "", $veri);
        $veri = str_replace("/s/g", "", $veri);
        $veri = str_replace("/s+/g", "", $veri);
        $veri = trim($veri);
        return $veri;
    }

    function getRecordById($id_order) {

        $moka_order_id = $id_order;

        $url = 'https://service.moka.com/PaymentDealer/GetDealerPaymentTrxDetailList';
        $moka_username = $this->config->get('moka_payment_username');
        $moka_password = $this->config->get('moka_payment_password');
        $moka_dealercode = $this->config->get('moka_payment_dealercode');

        $moka['PaymentDealerAuthentication'] = array(
            'DealerCode' => $moka_dealercode,
            'Username' => $moka_username,
            'Password' => $moka_password,
            'CheckKey' => hash('sha256', $moka_dealercode . 'MK' . $moka_username . 'PD' . $moka_password)
        );
        $moka['PaymentDealerRequest'] = array(
            'DealerPaymentId' => null,
            'OtherTrxCode' => $moka_order_id
        );

        return json_decode($this->curlPostExt(json_encode($moka), $url, true));
    }

    private function record2Table($record) {


        $r = 'Moka işlem No:' . $record->Data->PaymentDetail->DealerPaymentId . " ";
        $r .= 'Sepet toplamı:' . $record->Data->PaymentDetail->DealerCommissionAmount + $record->Data->PaymentDetail->Amount . " ";
        $r .= 'Ödenen:' . $record->Data->PaymentDetail->Amount . " ";
        $r .= 'Komisyon:' . $record->Data->PaymentDetail->DealerCommissionAmount . " ";
        $r .= 'Taksit:' . $record->Data->PaymentDetail->InstallmentNumber . " ";
        $r .= 'Kart:' . $record->Data->PaymentDetail->CardNumberFirstSix . 'XXX' . $record->Data->PaymentDetail->CardNumberLastFour . ' - ' . $record->Data->PaymentDetail->CardHolderFullName . " ";
        return $r . 'Cevap:' . $record->Data->ResultCode . " ";
    }

}
