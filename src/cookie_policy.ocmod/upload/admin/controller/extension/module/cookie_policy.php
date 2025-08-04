<?php 

class ControllerExtensionModuleCookiePolicy extends Controller { 
    
    private $error = array(); 

    public function index() { 
        $this->load->language('extension/module/cookie_policy'); 
        $this->document->setTitle($this->language->get('heading_title')); 
        $this->load->model('setting/setting'); 

		$this->document->setTitle($this->language->get('heading_title'));


		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {

            $settings = $this->model_setting_setting->getSetting('module_cookie_policy');
            $settings = is_array($settings)?$settings:[];
            if(isset($this->request->post['module_cookie_policy_reset_timestamp'])){
                $settings['module_cookie_policy_reset_timestamp'] = $this->request->post['module_cookie_policy_reset_timestamp'];
            }

            if(isset($this->request->post['module_cookie_policy_description'])){
                $settings['module_cookie_policy_description'] = $this->request->post['module_cookie_policy_description'];
            }

            if(isset($this->request->post['module_cookie_policy_status'])){
                $settings['module_cookie_policy_status'] = $this->request->post['module_cookie_policy_status'];
            }

            if(!empty($settings)){
                $this->model_setting_setting->editSetting('module_cookie_policy', $settings);
            }

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		// Ошибка для нового поля
        if (isset($this->error['description'])) {
            $data['error_description'] = $this->error['description'];
        } else {
            $data['error_description'] = '';
        }

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/module/cookie_policy', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['action'] = $this->url->link('extension/module/cookie_policy', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);

              
        $data['reset_action'] = $this->url->link('extension/module/cookie_policy/resetCookie', 'user_token=' . $this->session->data['user_token'], true);

    

		if (isset($this->request->post['module_cookie_policy_status'])) {
			$data['module_cookie_policy_status'] = $this->request->post['module_cookie_policy_status'];
		} else {
			$data['module_cookie_policy_status'] = $this->config->get('module_cookie_policy_status');
		}

		// Данные для поля Summernote
        if (isset($this->request->post['module_cookie_policy_description'])) {
            $data['module_cookie_policy_description'] = $this->request->post['module_cookie_policy_description'];
        } else {
            $data['module_cookie_policy_description'] = $this->config->get('module_cookie_policy_description');
        }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('extension/module/cookie_policy', $data));
    } 

    protected function validate() { 
        if (!$this->user->hasPermission('modify', 'extension/module/cookie_policy')) {
            $this->error['warning'] = $this->language->get('error_permission');
        }

        // Проверка, что поле не пустое
        if (!$this->request->post['module_cookie_policy_description']) {
            $this->error['description'] = $this->language->get('error_description');
        }


        return !$this->error;
    } 

    public function resetCookie() {
        $this->load->language('extension/module/cookie_policy');
        $json = array();

        if (!$this->user->hasPermission('modify', 'extension/module/cookie_policy')) {
            $json['error'] = $this->language->get('error_permission');
        } else {
            $this->load->model('setting/setting');

            // Получаем текущие настройки, чтобы не затереть их
            $settings = $this->model_setting_setting->getSetting('module_cookie_policy');
            
            // Добавляем/обновляем временную метку сброса
            $settings['module_cookie_policy_reset_timestamp'] = time();

            // Сохраняем все настройки обратно
            $this->model_setting_setting->editSetting('module_cookie_policy', $settings);

            $json['success'] = $this->language->get('text_reset_success');
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function install() {
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_cookie_policy');
        $settings = is_array($settings)?$settings:[];
        if(!isset($settings['module_cookie_policy_reset_timestamp'])){
            $settings['module_cookie_policy_reset_timestamp'] = time();
        }

        if(!isset($settings['module_cookie_policy_description'])){
            $settings['module_cookie_policy_description'] = 'Наживая на кнопку вы соглашаетесь с нашей политикой куки';
        }

        if(!isset($settings['module_cookie_policy_status'])){
            $settings['module_cookie_policy_status'] = 0;
        }

        if(!empty($settings)){
            $this->model_setting_setting->editSetting('module_cookie_policy', $settings);
        }
    }

    public function upgrade(){
        $this->install();
    }
}

?>