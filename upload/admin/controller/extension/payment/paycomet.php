<?php
class ControllerExtensionPaymentPaycomet extends Controller {
	private $error = array();
	public $url_paycomet = "https://api.paycomet.com/gateway/bnkgateway.php";
	private $_client = null;

	public function index() {

		$this->load->language('extension/payment/paycomet');
		
		$this->document->setTitle($this->language->get('heading_title'));
		
		$this->load->model('setting/setting');


		/* END ERRORS */
		

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('payment_paycomet', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true));
		}

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_extension'),
            'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=payment', true)
        );

   		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/payment/paycomet', 'user_token=' . $this->session->data['user_token'], true)
   		);

   		
   		$data['action'] = $this->url->link('extension/payment/paycomet', 'user_token=' . $this->session->data['user_token'], 'SSL');
		$data['cancel'] = $this->url->link('extension/extension/payment', 'user_token=' . $this->session->data['user_token'], 'SSL');
		
		$this->id       = 'content';
		

        if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		/* END COMMON STUFF */


		/* START FIELDS */
		$data['extension_class'] = 'payment';
		$data['tab_class'] = 'htabs';

		if (isset($this->request->post['payment_paycomet_status'])) {
			$data['payment_paycomet_status'] = $this->request->post['payment_paycomet_status'];
		} else {
			$data['payment_paycomet_status'] = $this->config->get('payment_paycomet_status');
		}

		if (isset($this->request->post['payment_paycomet_client'])) {
			$data['payment_paycomet_client'] = $this->request->post['payment_paycomet_client'];
		} else {
			$data['payment_paycomet_client'] = $this->config->get('payment_paycomet_client');
		}

		if (isset($this->request->post['payment_paycomet_terminal'])) {
			$data['payment_paycomet_terminal'] = $this->request->post['payment_paycomet_terminal'];
		} else {
			$data['payment_paycomet_terminal'] = $this->config->get('payment_paycomet_terminal');
		}

		if (isset($this->request->post['payment_paycomet_password'])) {
			$data['payment_paycomet_password'] = $this->request->post['payment_paycomet_password'];
		} else {
			$data['payment_paycomet_password'] = $this->config->get('payment_paycomet_password');
		}

		if (isset($this->request->post['payment_paycomet_terminales'])) {
			$data['payment_paycomet_terminales'] = $this->request->post['payment_paycomet_terminales'];
		} else {
			$data['payment_paycomet_terminales'] = $this->config->get('payment_paycomet_terminales');
		}

		if (isset($this->request->post['payment_paycomet_tdfirst'])) {
			$data['payment_paycomet_tdfirst'] = $this->request->post['payment_paycomet_tdfirst'];
		} else {
			$data['payment_paycomet_tdfirst'] = $this->config->get('payment_paycomet_tdfirst');
		}

		if (isset($this->request->post['payment_paycomet_tdmin'])) {
			$data['payment_paycomet_tdmin'] = $this->request->post['payment_paycomet_tdmin'];
		} else {
			$data['payment_paycomet_tdmin'] = $this->config->get('payment_paycomet_tdmin');
		}

		if (isset($this->request->post['payment_paycomet'])) {
			$data['payment_paycomet'] = $this->request->post['payment_paycomet'];
		} else {
			$data['payment_paycomet'] = $this->config->get('payment_paycomet');
		}

		$this->load->model('localisation/order_status');
		
		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		if (isset($this->request->post['payment_paycomet_order_status_id'])) {
			$data['payment_paycomet_order_status_id'] = $this->request->post['payment_paycomet_order_status_id'];
		} else {
			$data['payment_paycomet_order_status_id'] = $this->config->get('payment_paycomet_order_status_id');
		}

		if (isset($this->request->post['payment_paycomet_sort_order'])) {
			$data['payment_paycomet_sort_order'] = $this->request->post['payment_paycomet_sort_order'];
		} else {
			$data['payment_paycomet_sort_order'] = $this->config->get('payment_paycomet_sort_order');
		}

		if (isset($this->request->post['payment_paycomet_commerce_password'])) {
			$data['payment_paycomet_commerce_password'] = $this->request->post['payment_paycomet_commerce_password'];
		} else {
			$data['payment_paycomet_commerce_password'] = $this->config->get('payment_paycomet_commerce_password');
		}

		if (isset($this->error['client'])) {
			$data['error_client'] = $this->error['client'];
		} else {
			$data['error_client'] = '';
		}

		if (isset($this->error['terminal'])) {
			$data['error_terminal'] = $this->error['terminal'];
		} else {
			$data['error_terminal'] = '';
		}

		if (isset($this->error['password'])) {
			$data['error_password'] = $this->error['password'];
		} else {
			$data['error_password'] = '';
		}

		if (isset($this->error['tdmin'])) {
			$data['error_tdmin'] = $this->error['tdmin'];
		} else {
			$data['error_tdmin'] = '';
		}

		$data['button_save'] = $this->language->get('button_save');
		$data['button_cancel'] = $this->language->get('button_cancel');

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/payment/paycomet', $data));
	}

	private function validate($errors = array()) {
		if (!$this->user->hasPermission('modify', 'extension/payment/paycomet')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['payment_paycomet_client']) {
			$this->error['client'] = $this->language->get('error_client');
		}

		if (!$this->request->post['payment_paycomet_terminal']) {
			$this->error['terminal'] = $this->language->get('error_terminal');
		}

		if (!$this->request->post['payment_paycomet_password']) {
			$this->error['password'] = $this->language->get('error_password');
		}

		if ($this->request->post['payment_paycomet_tdmin']) {
			if (!is_numeric($this->request->post['payment_paycomet_tdmin']))
			$this->error['tdmin'] = $this->language->get('error_tdmin');
		}


		foreach ($errors as $error) {
			if (isset($this->request->post[$this->name . '_' . $error]) && !$this->request->post[$this->name . '_' . $error]) {
				$this->error[$error] = $this->language->get('error_' . $error);
			}
		}

		if (!$this->error) {
			return TRUE;
		} else {
			return FALSE;
		}
	}

	public function install() {
		$this->load->model('extension/payment/paycomet');
		$this->model_extension_payment_paycomet->install();
	}

	public function uninstall(){
		$this->load->model('extension/payment/paycomet');
		$this->model_extension_payment_paycomet->uninstall();
	}

	public function order() {
		
		if ($this->config->get('payment_paycomet_status')) {
			$this->load->language('extension/payment/paycomet_order');
			

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('extension/payment/paycomet');

			$paycomet_info = $this->model_extension_payment_paycomet->getPaycometOrder($order_id);

			if ($paycomet_info) {

				$data['user_token'] = $this->session->data['user_token'];

				$data['order_id'] = $this->request->get['order_id'];

				$data['capture_status'] = ($paycomet_info['result'])?"OK":"KO";

				$data['total'] = $paycomet_info['amount'];

				$captured = number_format($this->model_extension_payment_paycomet->totalCaptured($this->request->get['order_id']), 2);

				$data['captured'] = $captured;

				$data['capture_remaining'] = number_format($paycomet_info['amount'] - $captured, 2);

				$refunded = number_format($this->model_extension_payment_paycomet->totalRefundedOrder($this->request->get['order_id']), 2);

				$data['refunded'] = $refunded;

				return $this->load->view('extension/payment/paycomet_order', $data);
			}
		}
	}

	public function transaction() {

					
		$this->load->language('extension/payment/paycomet_order');

		$data['transactions'] = array();

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		$this->load->model('extension/payment/paycomet');

		$paycomet_info = $this->model_extension_payment_paycomet->getOrder($order_id);

		if ($paycomet_info){

			$results = $this->model_extension_payment_paycomet->getPaycometOrderRefund($order_id);
			$total_refunded = 0;
			$data2 = array();
			if ($results){
				
				foreach ($results as $result) {

					$data2[] = array(
						'transaction_id' => $result['authcode'],
						'amount'         => $result['amount'],
						'date'           => $result['date'],
						'type'           => "refund",
						'type_text'           => $this->language->get('txt_refund'),
						'refund'         => ""
					);
					$total_refunded += $result['amount'];
				}
			}


			$data['transactions'][] = array(
					'total_refunded'  => $total_refunded,
					'transaction_id' => $paycomet_info['authcode'],
					'amount'         => $paycomet_info['amount'],
					'date'           => $paycomet_info['date'],
					'type'           => "capture",
					'type_text'           => $this->language->get('txt_payment'),
					'refund'         => $this->url->link('extension/payment/paycomet/refund', 'token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true),
					
				);


			$data['transactions'] = array_merge($data['transactions'],$data2);

		}
		//return $this->load->view('extension/payment/paycomet_transaction', $data);
		$this->response->setOutput($this->load->view('extension/payment/paycomet_transaction', $data));
	}

	public function refund(){
		
		$this->load->model('extension/payment/paycomet');
		$this->load->model('sale/order');
		$this->load->language('extension/payment/paycomet');

		$transaction = $this->model_extension_payment_paycomet->getOrder($this->request->get['order_id']);

		if ($transaction) {
			$this->document->setTitle($this->language->get('heading_refund'));

			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/home', 'token=' . $this->session->data['user_token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_payment'),
				'href' => $this->url->link('extension/payment', 'token=' . $this->session->data['user_token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('extension/payment/paycomet', 'token=' . $this->session->data['user_token'], 'SSL')
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_refund'),
				'href' => $this->url->link('extension/payment/paycomet/refund', 'order_id=' . $this->request->get['order_id'] . '&token=' . $this->session->data['user_token'], 'SSL')
			);

			$data['transaction_reference'] = $transaction['order_id'];
			$data['transaction_amount'] = number_format($transaction['amount'], 2);
			$data['cancel'] = $this->url->link('sale/order/info', 'token=' . $this->session->data['user_token'] . '&order_id=' . $transaction['order_id'], 'SSL');

			$data['user_token'] = $this->session->data['user_token'];

			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			return $this->load->view('extension/payment/paycomet_refund', $data);
		} else {
			return $this->forward('error/not_found');
		}
	}

	public function doRefund(){
		$this->load->model('extension/payment/paycomet');
		$this->load->language('extension/payment/paycomet');
		$this->load->model('sale/order');


		$json = array();

		if (isset($this->request->post['order_id']) && isset($this->request->post['amount'])) {

			$order_id = $this->request->post['order_id'];
			$totalRefund = $this->request->post['totalRefund'];
			
			$order_info = $this->model_sale_order->getOrder($order_id);
			$paycomet_order_info = $this->model_extension_payment_paycomet->getOrderInfo($order_id);

			if ($order_info) {

				$total_refunded = 0;
				$results = $this->model_extension_payment_paycomet->getPaycometOrderRefund($order_id);
				if ($results){
					
					foreach ($results as $result) {
						$total_refunded += $result['amount'];
					}
				}

				// Partial Refund
				if ($totalRefund==0)
					$amount = $this->request->post['amount'];
				// Total Refund
				else{
					$amount = $paycomet_order_info["amount"] - $total_refunded;
				}

				if ($amount>0){

	    			$currency_iso_code = $order_info['currency_code'];

					$DS_MERCHANT_MERCHANTCODE = $this->config->get('payment_paycomet_client');
			        $DS_IDUSER = $paycomet_order_info["paycomet_iduser"];
			        $DS_TOKEN_USER = $paycomet_order_info["paycomet_tokenuser"];
			        $DS_MERCHANT_ORDER = trim($order_id);
			        $DS_MERCHANT_CURRENCY = $currency_iso_code;
			        $DS_MERCHANT_TERMINAL = $this->config->get('payment_paycomet_terminal');
			        $DS_MERCHANT_AUTHCODE = $paycomet_order_info["authcode"];
			        $DS_MERCHANT_MERCHANTSIGNATURE = hash('sha512', $DS_MERCHANT_MERCHANTCODE . $DS_IDUSER . $DS_TOKEN_USER . $DS_MERCHANT_TERMINAL . $DS_MERCHANT_AUTHCODE . $DS_MERCHANT_ORDER . $this->config->get('payment_paycomet_password'));
			        $DS_ORIGINAL_IP = $_SERVER['REMOTE_ADDR'];
					$DS_MERCHANT_AMOUNT = round($amount * 100);
					
					$result = $this->getClient()->execute_refund(
			            $DS_MERCHANT_MERCHANTCODE,
			            $DS_MERCHANT_TERMINAL,
			            $DS_IDUSER,
			            $DS_TOKEN_USER,
			            $DS_MERCHANT_AUTHCODE,
			            $DS_MERCHANT_ORDER,
			            $DS_MERCHANT_CURRENCY,
			            $DS_MERCHANT_MERCHANTSIGNATURE,
			            $DS_ORIGINAL_IP,
			            $DS_MERCHANT_AMOUNT
					);
					
					
					if ($result[ 'DS_RESPONSE']==1){
						$json['success'] = $this->language->get('text_refund_issued');
						$total_refunded += $amount;

						$json['total_refunded'] = number_format($total_refunded,2,".","");

						$data = array(
							'order_id' => $order_id,
							'authcode' => $result['DS_MERCHANT_AUTHCODE'],
							'result' => $result[ 'DS_RESPONSE'],
							'amount' => $amount,
						);

						$this->model_extension_payment_paycomet->addRefund($data);
					} else {
						$json['error'] = "Error (".$result['DS_ERROR_ID'].")";
					}
				}else{
					$json['error'] = $this->language->get('error_min_amount_refund');
				}
			} else {
				$json['error'] = $this->language->get('error_missing_order');
			}
		} else {
			$json['error'] = $this->language->get('error_missing_data');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	

	public function orderAction() {
		if ($this->config->get('payment_paycomet_status')) {
			
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}

			$this->load->model('extension/payment/paycomet');

			$paycomet_info = $this->model_extension_payment_paycomet->getPaycometOrder($order_id);

			if ($paycomet_info) {

				$data['user_token'] = $this->session->data['user_token'];

				$data['order_id'] = $this->request->get['order_id'];

				$data['capture_status'] = ($paycomet_info['result'])?"OK":"KO";

				$data['total'] = $paycomet_info['amount'];

				$captured = number_format($this->model_extension_payment_paycomet->totalCaptured($this->request->get['order_id']), 2);

				$data['captured'] = $captured;

				$data['capture_remaining'] = number_format($paycomet_info['amount'] - $captured, 2);

				$refunded = number_format($this->model_extension_payment_paycomet->totalRefundedOrder($this->request->get['order_id']), 2);

				$data['refunded'] = $refunded;

				return $this->load->view('extension/payment/paycomet_order', $data);

			}
		}
	}

	private function getClient()
    {
		// Para entornos de desarrollo con certificados propios
		$context = stream_context_create(array(
			'ssl' => array(
				'verify_peer' => true,
				'verify_peer_name' => false,
				'allow_self_signed' => true
			)
		));

        if (null == $this->_client)
            $this->_client = new SoapClient('https://api.paycomet.com/gateway/xml_bankstore.php?wsdl', array('stream_context' => $context));
 
        return $this->_client;
    }
}
