<?php
namespace Grav\Plugin;

use Grav\Common\Page\Collection;
use Grav\Common\Page\Page;
use Grav\Common\Plugin;
use Grav\Common\Uri;
use Grav\Common\Taxonomy;

class PrivatePlugin extends Plugin
{

    /**
     * @var
     */
    protected $uri;
    protected $homepath;
    protected $privateconf;
    protected $login_error;


    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            'onPluginsInitialized' => ['onPluginsInitialized', 0],
        ];
    }

    /**
     * Activate plugin if path matches to the configured one.
     */
    public function onPluginsInitialized()
    {
        if ($this->isAdmin()) {
            $this->active = false;
            return;
        }

        if (!isset($_SESSION)) {
            session_start();
        }
            
        $this->uri = $this->grav['uri'];
        $this->homepath = $this->grav['config']->get('system.home.alias', null);
        $this->privateconf = $this->grav['config']->get('plugins.private', null);

        if($this->isConnected() == true){

            switch ($this->uri->path()) {
                
                case $this->privateconf['routes']['logout']:
                    $this->logout();
                    return;
                    break;

                case $this->privateconf['routes']['login']:
                    $this->grav->redirect($this->homepath);
                    return;
                    break;
                
                default:
                    return;
                    break;
            }

        } else {

            switch ($this->uri->path()) {
                
                case $this->privateconf['routes']['logout']:
                    $this->grav->redirect($this->homepath);
                    return;
                    break;

                case $this->privateconf['routes']['login']:
                    $this->enable([
                        'onTwigTemplatePaths'   => ['onTwigTemplatePaths', 0],
                        'onTwigSiteVariables'   => ['onTwigSiteVariables', 0],
                        'onPageInitialized'     => ['onPageInitialized', 0]
                    ]);
                    return;
                    break;
                
                default:
                    if($this->privateconf['private_site'] == true) {
                        $this->grav->redirect($this->privateconf['routes']['login']);
                        return;
                    } else {
                        $this->enable([
                            'onPageInitialized' => ['onPageInitialized', 0]
                        ]);
                        return;
                    }
                    break;
            }
        }
        
    }

    
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        $twig = $this->grav['twig'];
        $twig->twig_vars['privateconf'] = $this->privateconf;
        if(!empty($this->login_error)){
            $twig->twig_vars['login_error'] = $this->login_error;
        }

        $this->grav['assets']
            ->add('plugin://private/assets/css/private.css')
            ->add('plugin://private/assets/js/private.js');
    }

    public function onPageInitialized()
    {
      
        if ($this->uri->path() == $this->privateconf['routes']['login']){
            $this->getLoginPage();
            return;
        }

        else if($this->privateconf['private_site'] == true ) {
            $this->grav->redirect($this->privateconf['routes']['login']);
            return;
        }

        else {
            if (array_key_exists( 'tag', $this->grav['page']->taxonomy() )) {
                $find_tag = in_array( $this->privateconf['private_tag'], $this->grav['page']->taxonomy()['tag'] );
                if( $find_tag == true ) {
                    $_SESSION['referer_redirect'] = $this->uri->path();
                    $this->grav->redirect($this->privateconf['routes']['login']);
                    return;
                } else {
                    return;
                }
            }
        }

    }


    protected function getLoginPage()
    {
       
        $loginpage = new Page;
        $loginpage->init(new \SplFileInfo(__DIR__ . '/pages/login.md'));

        
        unset($this->grav['page']);
        $this->grav['page'] = $loginpage;
        $this->grav['page']->header()->slug = substr($this->privateconf['routes']['login'], 1); ;

        if ( $_SERVER['REQUEST_METHOD'] == "POST") {
            if ( $this->validateFormData() === false ) {
                $this->login_error = 'error';
                return;
            }
            if ( $this->sendLogin() === false ) {
                $this->login_error = 'fail';
                return;
            } else {
                if($this->privateconf['private_site'] == true ) {
                     $redirect_referer = $this->homepath;
                }else{
                    $redirect_referer = $_SESSION['referer_redirect'];
                    unset($_SESSION['referer_redirect']); 
                }
                $this->grav->redirect($redirect_referer);
                return;
            }

        }

    }
    
    protected function validateFormData()
    {
        $form_data = $this->filterFormData($_POST);

        $username = $form_data['username'];
        $password = $form_data['password'];
        $antispam = $form_data['antispam'];

        if ( empty($username) || empty($password) || $antispam ) {
            return false;
        } else {
            return true;
        }
    }

    
    protected function filterFormData($form)
    {
        $defaults = [
            'username'  => '',
            'password'  => '',
            'antispam'  => ''
        ];

        $data = array_merge($defaults, $form);

        return [
            'username'  => $data['username'],
            'password'  => $data['password'],
            'antispam'  => $data['antispam']
        ];
    }

    protected function sendLogin()
    {
        
        $form = $this->filterFormData($_POST);


        if(isset($form['username']) == true && $this->privateconf['users'][$form['username']] == sha1($form['password'])) {
            $_SESSION[$this->privateconf['session_ss']] = sha1($form['username']);
            $_SESSION['username'] = $form['username'];
            return true;
        } else {
            return false;
        }
    }

    protected function logout()
    {
        unset($_SESSION[$this->privateconf['session_ss']]);
        unset($_SESSION['username']);
        return $this->grav->redirect($this->homepath);
    }

    protected function isConnected()
    {
        if (isset($_SESSION[$this->privateconf['session_ss']]) && $_SESSION[$this->privateconf['session_ss']] == sha1($_SESSION['username'])){
            return true;
        } else {
            return false;
        }
    }



}
