<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        
        // Load required models and libraries
        $this->load->model('Authentication_model');
        $this->load->library('form_validation');
        
        // Check if already logged in as staff
        if (is_staff_logged_in()) {
            redirect(admin_url());
        }
        
        load_admin_language();
        $this->form_validation->set_message('required', _l('form_validation_required'));
        $this->form_validation->set_message('valid_email', _l('form_validation_valid_email'));
        $this->form_validation->set_message('matches', _l('form_validation_matches'));
    }
    
    public function recaptcha($str = '')
    {
        return do_recaptcha_validation($str);
    }

    public function index()
    {
        // Show admin authentication page directly
        $this->form_validation->set_rules('password', _l('admin_auth_login_password'), 'required');
        $this->form_validation->set_rules('email', _l('admin_auth_login_email'), 'trim|required|valid_email');
        
        if (show_recaptcha()) {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }
        
        if ($this->input->post()) {
            if ($this->form_validation->run() !== false) {
                $email    = $this->input->post('email');
                $password = $this->input->post('password', false);
                $remember = $this->input->post('remember');

                $data = $this->Authentication_model->login($email, $password, $remember, true);

                if (is_array($data) && isset($data['memberinactive'])) {
                    set_alert('danger', _l('admin_auth_inactive_account'));
                    redirect(site_url());
                } elseif (is_array($data) && isset($data['two_factor_auth'])) {
                    $this->session->set_userdata('_two_factor_auth_established', true);
                    if ($data['user']->two_factor_auth_enabled == 1) {
                        $this->Authentication_model->set_two_factor_auth_code($data['user']->staffid);
                        $sent = send_mail_template('staff_two_factor_auth_key', $data['user']);

                        if (!$sent) {
                            set_alert('danger', _l('two_factor_auth_failed_to_send_code'));
                            redirect(site_url());
                        } else {
                            $this->session->set_userdata('_two_factor_auth_staff_email', $email);
                            set_alert('success', _l('two_factor_auth_code_sent_successfully', $email));
                            redirect(site_url('admin/authentication/two_factor'));
                        }
                    } else {
                        set_alert('success', _l('enter_two_factor_auth_code_from_mobile'));
                        redirect(site_url('admin/authentication/two_factor/app'));
                    }
                } elseif ($data == false) {
                    set_alert('danger', _l('admin_auth_invalid_email_or_password'));
                    redirect(site_url());
                }

                $this->load->model('announcements_model');
                $this->announcements_model->set_announcements_as_read_except_last_one(get_staff_user_id(), true);

                // is logged in
                maybe_redirect_to_previous_url();

                hooks()->do_action('after_staff_login');
                redirect(admin_url());
            }
        }

        $data['title'] = _l('admin_auth_login_heading');
        $this->load->view('authentication/login_admin', $data);
    }
}

