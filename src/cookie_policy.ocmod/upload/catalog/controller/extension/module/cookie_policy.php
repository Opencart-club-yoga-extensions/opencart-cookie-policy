<?php
class ControllerExtensionModuleCookiePolicy extends Controller {
    public function index($setting) {
        // Проверяем, включен ли модуль в настройках
        $this->load->model('setting/setting');
        $settings = $this->model_setting_setting->getSetting('module_cookie_policy');
        
        $status = $settings['module_cookie_policy_status'];
        $description = $settings['module_cookie_policy_description'];
        if ($status && $description) {

            // Загружаем файл языка (полезно для заголовков и т.д.)
            $this->load->language('extension/module/cookie_policy');

            // Подключаем кастомные файлы CSS и JS
            // Важно: Путь к CSS указан для темы 'yoga', как вы и просили.
            // Если название папки вашей темы отличается, измените путь.
            // Получаем название директории текущей темы
            $theme_directory = $this->config->get('config_theme_default_directory');

            $custom_css_path = 'catalog/view/theme/' . $theme_directory . '/stylesheet/cookie_policy.css';
            $default_css_path = 'catalog/view/theme/default/stylesheet/cookie_policy.css';

            // Проверяем, есть ли CSS-файл в активной теме
            if (file_exists(DIR_TEMPLATE . $theme_directory . '/stylesheet/cookie_policy.css')) {
                // Если есть, подключаем его
                $this->document->addStyle($custom_css_path);
            } else {
                // Иначе, подключаем файл из темы default
                $this->document->addStyle($default_css_path);
            }
            $this->document->addScript('catalog/view/javascript/cookie_policy.js');

            $data['cookie_description'] = html_entity_decode($description, ENT_QUOTES, 'UTF-8');
            $data['reset_timestamp'] = $settings['module_cookie_policy_reset_timestamp'];
            
            // Загружаем и возвращаем шаблон модуля
            return $this->load->view('extension/module/cookie_policy', $data);
        }else{
            return '';
        }
    }
}