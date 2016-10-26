<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class User extends CI_Controller
{

    function __construct()
    {
        parent::__construct();

        $this->load->helper('url');
    }


    public function metadata()
    {
        $_SERVER['PATH_INFO'] = '/max';
        $_REQUEST['output'] = 'xhtml';
        include(dirname(dirname(__DIR__)).'/vendor/simplesamlphp/simplesamlphp/modules/saml/www/sp/metadata.php');
    }

    public function login()
    {
        $as = new SimpleSAML_Auth_Simple('max');
        $as->requireAuth();

        $attributes = $as->getAttributes();

        $userdata = array();
        $userdata['username'] = $attributes['maxEmail'][0];
        $userdata['name_full'] = $attributes['maxFirstName'][0].' '.$attributes['maxLastName'][0];
        $userdata['permissions'] = 'user';
        $admin_emails = array(
            'kishore.vuppala@gsa.gov'
        );
        if (in_array($userdata['username'], $admin_emails)) {
            $userdata['permissions'] = 'admin';
        }
        $userdata['provider_url'] = 'max.gov';
        $this->session->set_userdata($userdata);
        $this->session->set_userdata($attributes);

        redirect('offices');
    }

    public function acs()
    {
        $_SERVER['PATH_INFO'] = '/max';
        include(dirname(dirname(__DIR__)) . '/vendor/simplesamlphp/simplesamlphp/modules/saml/www/sp/saml2-acs.php');
    }

    public function logout()
    {

        $this->session->sess_destroy();
        redirect('offices');
    }
}