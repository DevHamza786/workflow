<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Home extends CI_Controller
{
    public function index()
    {
        // Redirect root URL to admin authentication (main portal access)
        redirect(admin_url('authentication'));
    }
}
