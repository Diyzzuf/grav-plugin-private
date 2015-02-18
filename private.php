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
            
        $uri = $this->grav['uri'];
        $options = $this->grav['config']->get('plugins.private');
        

        if( $options['enabled'] == true) {
        
            session_start(); 
                
            if($uri->path() == $options['routes']['logout']) {
                session_destroy();
                $this->grav->redirect('/');
            } 
            
            else if($uri->path() == $options['routes']['login']) {
                if (!isset($_SESSION[$options['session_ss']]) || $_SESSION[$options['session_ss']] == false ) {
                    $this->enable([
                        'onTwigTemplatePaths'   => ['onTwigTemplatePaths', 0],
                        'onTwigSiteVariables'   => ['onTwigSiteVariables', 0],
                        'onPageInitialized'     => ['onPageInitialized', 0]
                    ]);
                    return;
                }
                else {
                    $this->grav->redirect('/');
                }
                
            }
            
            else {
                if($options['private_site'] == true && (!isset($_SESSION[$options['session_ss']]) || $_SESSION[$options['session_ss']] == false ) ){
                    $_SESSION['referer_redirect'] = $uri->path();
                        $this->grav->redirect($options['routes']['login']);
                } else {
                    $this->enable([
                        'onPageInitialized'     => ['onPageInitialized', 0]
                    ]);
                    return;
                }
            }

        }
        
    }

    
    public function onTwigTemplatePaths()
    {
        $this->grav['twig']->twig_paths[] = __DIR__ . '/templates';
    }

    public function onTwigSiteVariables()
    {
        if ( $this->grav['config']->get('plugins.private.enabled') ) {
            $this->grav['assets']
                ->add('plugin://private/assets/css/private.css')
                ->add('plugin://private/assets/js/private.js');
        }
    }


    public function onPageInitialized()
    {

        $uri = $this->grav['uri'];
        $options = $this->grav['config']->get('plugins.private');

        if( $options['enabled'] == true) {
            
            if (!isset($_SESSION[$options['session_ss']]) || $_SESSION[$options['session_ss']] == false ) {
                
                if ( $uri->path() == $options['routes']['login']){
                    $this->getLoginPage();
                }

                else {
                    
                    if($options['private_site'] == true ) {
                        $_SESSION['referer_redirect'] = $uri->path();
                        $this->grav->redirect($options['routes']['login']);
                    }

                    else {
                        if (array_key_exists( 'tag', $this->grav['page']->taxonomy() )) {
                            $find_tag = array_search( $options['private_tag'], $this->grav['page']->taxonomy()['tag'] );
                            if( $find_tag ) {
                                $_SESSION['referer_redirect'] = $uri->path();
                                $this->grav->redirect($options['routes']['login']);
                            } else {
                                return;
                            }
                        }
                    }

                }
            }

            else {
                return;
            }
        
        }

    }


    protected function getLoginPage()
    {
       
        $loginpage = new Page;
        $loginpage->init(new \SplFileInfo(__DIR__ . '/pages/login.md'));
        
        unset($this->grav['page']);
        $this->grav['page'] = $loginpage;

        $page   = $this->grav['page'];
        $twig   = $this->grav['twig'];
        $uri    = $this->grav['uri'];
        $options = (array) $this->grav['config']->get('plugins.private');

        if ( $_SERVER['REQUEST_METHOD'] == "POST") {
            if ( false === $this->validateFormData() ) {
                $page->content($twig->twig()->render('login.html.twig', ['private' => $options, 'page' => $page, 'login_error' => 'error']));
            } else {
                if ( false === $this->sendLogin() ) {
                    $page->content($twig->twig()->render('login.html.twig', ['private' => $options, 'page' => $page, 'login_error' => 'fail']));
                } else {
                    $redirect_referer = $_SESSION['referer_redirect'];
                    unset($_SESSION['referer_redirect']); 
                    $this->grav->redirect($redirect_referer);
                }
            }

        } else {
            $page->content($twig->twig()->render('login.html.twig', ['private' => $options, 'page' => $page]));
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
        
        $form   = $this->filterFormData($_POST);
        $options = $this->grav['config']->get('plugins.private');


        if(isset($form['username']) == true && $options['users'][$form['username']] == sha1($form['password'])) {
            $_SESSION[$options['session_ss']] = true;
            $_SESSION['username'] = $form['username'];
            return true;
        } else {
            return false;
        }
    }



}
