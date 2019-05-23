<?php
error_reporting(0);
class ControllerPaymentMokaPayment extends Controller {

    private $error = array();
    private $base_url = "";
    private $order_prefix = "opencart156_";
    private $module_version = "1.5.0.0";

    public function index() {
        $this->language->load('payment/moka_payment');
        $this->load->model('payment/moka_payment');
        $this->document->setTitle($this->language->get('heading_title'));
        include(DIR_SYSTEM . 'library/mokapayment/mokaconfig.php');
        $this->load->model('setting/setting');
        $this->model_payment_moka_payment->disableErrorSettings();
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->model_setting_setting->editSetting('moka_payment', $this->request->post);
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        }

        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_edit'] = $this->language->get('heading_title');
        $this->data['link_title'] = $this->language->get('text_link');

        $this->data['text_enabled'] = $this->language->get('text_enabled');
        $this->data['text_disabled'] = $this->language->get('text_disabled');

        $this->data['entry_dealercode'] = $this->language->get('entry_dealercode');
        $this->data['entry_username'] = $this->language->get('entry_username');
        $this->data['entry_installement'] = $this->language->get('entry_installement');

        $this->data['entry_password'] = $this->language->get('entry_password');

        $this->data['entry_order_status'] = $this->language->get('entry_order_status');
        $this->data['entry_status'] = $this->language->get('entry_status');
        $this->data['entry_threed'] = $this->language->get('entry_threed');
        $this->data['entry_class_responsive'] = $this->language->get('entry_class_responsive');
        $this->data['entry_class_popup'] = $this->language->get('entry_class_popup');
        $this->data['entry_installment_options'] = $this->language->get('entry_installment_options');
        $this->data['entry_sort_order'] = $this->language->get('entry_sort_order');

        $this->data['button_save'] = $this->language->get('button_save');
        $this->data['button_cancel'] = $this->language->get('button_cancel');

        $this->data['order_status_after_payment_tooltip'] = $this->language->get('order_status_after_payment_tooltip');
        $this->data['order_status_after_cancel_tooltip'] = $this->language->get('order_status_after_cancel_tooltip');
        $this->data['entry_test_tooltip'] = $this->language->get('entry_test_tooltip');
        $this->data['entry_cancel_order_status'] = $this->language->get('entry_cancel_order_status');


        $this->data['message'] = '';
        $this->data['error_warning'] = '';
        $this->data['error_version'] = '';

        $error_data_array_key = array(
            'dealercode',
            'username',
            'password'
        );

        if ($this->config->get('moka_rates') == NULL) {
            $this->config->set('moka_rates', MokaConfig::setRatesDefault());
        }


        if (isset($this->request->get['update_error'])) {
            $this->data['error_version'] = $this->language->get('entry_error_version_updated');
        } else {
            $this->load->model('payment/moka_payment');
            $versionCheck = $this->model_payment_moka_payment->versionCheck(VERSION, $this->module_version);

            if (!empty($versionCheck['version_status']) AND $versionCheck['version_status'] == '1') {
                $this->data['error_version'] = $this->language->get('entry_error_version');
                $this->data['moka_or_text'] = $this->language->get('entry_moka_or_text');
                $this->data['moka_update_button'] = $this->language->get('entry_moka_update_button');
                $version_updatable = $versionCheck['new_version_id'];
                $this->data['version_update_link'] = $this->url->link('payment/moka_payment/update', 'token=' . $this->session->data['token'] . "&version=$version_updatable", true);
            }
        }

        foreach ($error_data_array_key as $key) {
            $this->data["error_{$key}"] = isset($this->error[$key]) ? $this->error[$key] : '';
        }

        $this->data['breadcrumbs'] = array();

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => false
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_payment'),
            'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('payment/moka_payment', 'token=' . $this->session->data['token'], 'SSL'),
            'separator' => ' :: '
        );

        $this->data['action'] = $this->url->link('payment/moka_payment', 'token=' . $this->session->data['token'], 'SSL');

        $this->data['cancel'] = $this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL');

        $merchant_keys_name_array = array(
            'moka_payment_dealercode',
            'moka_payment_username',
            'moka_payment_password',
            'moka_payment_moka_3d_mode',
            'moka_payment_status',
            'moka_payment_order_status_id',
            'moka_payment_sort_order',
            'moka_payment_installement',
            'moka_payment_form_class',
            'moka_payment_cancel_order_status_id'
        );

        foreach ($merchant_keys_name_array as $key) {
            $this->data[$key] = isset($this->request->post[$key]) ? $this->request->post[$key] : $this->config->get($key);
        }

        $this->data['moka_rates_table'] = MokaConfig::createRatesUpdateForm($this->config->get('moka_rates'));
        $this->load->model('localisation/order_status');
        if ($this->data['moka_payment_order_status_id'] == '') {
            $this->data['moka_payment_order_status_id'] = $this->config->get('config_order_status_id');
        }
        $this->data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();

        $this->template = 'payment/moka_payment.tpl';
        $this->children = array(
            'common/header',
            'common/footer'
        );
        $this->response->setOutput($this->render());
    }

    public function install() {
        $this->load->model('payment/moka_payment');
        $this->model_payment_moka_payment->install();
    }

    public function uninstall() {
        $this->load->model('payment/moka_payment');
        $this->model_payment_moka_payment->uninstall();
    }

    public function update() {
        $this->load->model('payment/moka_payment');
        $this->load->language('payment/moka_payment');
        $version_updatable = $this->request->get['version'];
        $updated = $this->model_payment_moka_payment->update($version_updatable);
        if ($updated == 1) {
            $this->session->data['success'] = $this->language->get('text_success');
            $this->redirect($this->url->link('extension/payment', 'token=' . $this->session->data['token'], 'SSL'));
        } else {
            $this->redirect($this->url->link('payment/moka_payment', 'token=' . $this->session->data['token'] . "&update_error=$updated", 'SSL'));
        }
    }

    public function orderAction() {
        $this->language->load('payment/moka_payment');
        $language_id = (int) $this->config->get('config_language_id');
        $this->data = array();
        $order_id = (int) $this->request->get['order_id'];
        $this->data['token'] = $this->request->get['token'];
        $this->data['heading_title'] = $this->language->get('heading_title');
        $this->data['text_DealerPaymentId'] = $this->language->get('text_DealerPaymentId');
        $this->data['text_sepet_total'] = $this->language->get('text_sepet_total');
        $this->data['text_odenen'] = $this->language->get('text_odenen');
        $this->data['text_komisyon'] = $this->language->get('text_komisyon');
        $this->data['text_taksit_sayi'] = $this->language->get("text_taksit_sayi");
        $this->data['text_creditcart'] = $this->language->get('text_creditcart');
        $this->data['text_rescode'] = $this->language->get('text_rescode');

        $moka_order_id = $order_id;

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


        $result = json_decode($this->curlPostExt(json_encode($moka), $url, true));

        $this->data['DealerPaymentId'] = $result->Data->PaymentDetail->DealerPaymentId;
        $this->data['sepet_total'] = $result->Data->PaymentDetail->DealerCommissionAmount + $result->Data->PaymentDetail->Amount;
        $this->data['odenen'] = $result->Data->PaymentDetail->Amount;
        $this->data['komisyon'] = $result->Data->PaymentDetail->DealerCommissionAmount;
        $this->data['taksit_sayi'] = $result->Data->PaymentDetail->InstallmentNumber;
        $this->data['creditcart'] = $result->Data->PaymentDetail->CardNumberFirstSix . 'XXX' . $result->Data->PaymentDetail->CardNumberLastFour . ' - ' . $result->Data->PaymentDetail->CardHolderFullName;
        $this->data['rescode'] = $result->Data->ResultCode." - ".$result->Data->PaymentTrxDetailList[0]->ResultMessage;

        $this->template = 'payment/moka_payment_order.tpl';

        $this->response->setOutput($this->render());
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

    protected function validate() {
        if (!$this->user->hasPermission('modify', 'payment/moka_payment')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        $validation_array = array(
            'dealercode',
            'username',
            'password'
        );

        foreach ($validation_array as $key) {
            if (empty($this->request->post["moka_payment_{$key}"])) {
                $this->error[$key] = $this->language->get("error_$key");
            }
        }

        if (!$this->error) {
            return true;
        } else {
            return false;
        }
    }

    private function _addhistory($order_id, $order_status_id, $comment) {

        $this->load->model('sale/order');
        $this->model_sale_order->addOrderHistory($order_id, array(
            'order_status_id' => $order_status_id,
            'notify' => 1,
            'comment' => $comment
        ));

        return true;
    }

}
