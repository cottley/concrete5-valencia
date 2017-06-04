<?php
namespace Concrete\Package\Valencia\Authentication\Valencia;

use Concrete\Core\Authentication\AuthenticationTypeController;
use Concrete\Core\Support\Facade\Application;
use Config;
use Exception;
use Loader;
use User;
use UserInfo;
use View;
use Core;

define('VROOT_PATH', realpath(__DIR__.'/../../'));
$adLDAPVersion = \Config::get('valencia.auth.adldapversion');
if ($adLDAPVersion != '') {
  include_once(VROOT_PATH.'/lib/adLDAP/'.$adLDAPVersion.'/src/adLDAP.php');
}
class Controller extends AuthenticationTypeController
{
    
    public function getHandle() {
        return 'valencia';
    }

	public function adLDAPAuthenticate($username, $password) {
	  $result = false;
	  try {
		$options01 = array('base_dn'=>\Config::get('valencia.auth.ldap.ldapdn01'), 
		                   'account_suffix'=>'@'.\Config::get('valencia.auth.ldap.ldapdomain01'),
						   'domain_controllers'=>array(\Config::get('valencia.auth.ldap.ldaphost01')),
						   'ad_port'=>\Config::get('valencia.auth.ldap.ldapport01'),
						   'use_ssl'=>\Config::get('valencia.auth.ldap.ldapssl01'),
						   'use_tls'=>\Config::get('valencia.auth.ldap.ldaptls01'));
		
		$options02 = array('base_dn'=>\Config::get('valencia.auth.ldap.ldapdn02'), 
		                   'account_suffix'=>'@'.\Config::get('valencia.auth.ldap.ldapdomain02'),
						   'domain_controllers'=>array(\Config::get('valencia.auth.ldap.ldaphost02')),
						   'ad_port'=>\Config::get('valencia.auth.ldap.ldapport02'),
						   'use_ssl'=>\Config::get('valencia.auth.ldap.ldapssl02'),
						   'use_tls'=>\Config::get('valencia.auth.ldap.ldaptls02'));
						   
		$adldap01 = new \adLDAP($options01);
		$adldap02 = new \adLDAP($options02);		
		
		$result = $adldap01->authenticate($username, $password);
		if (!$result) {
		  $result = $adldap02->authenticate($username, $password);
		}
		
		$adldap01->close();
		$adldap02->close();

      }
      catch (adLDAPException $e) {
        echo $e;
      }
	  
	  return $result;
	}
	
    public function completeAuthentication(\Concrete\Core\User\User $u)
	{
		
	}
	
    private function concrete5IsPasswordReset()
    {
        $app = Application::getFacadeApplication();
        $db = $app['database']->connection();

        return $db->GetOne('select uIsPasswordReset from Users where uName = ?', array($this->post('uName')));
    }
	
    public function concrete5Authenticate($username, $password)
    {
		$result = false;
		
        /** @var \Concrete\Core\Permission\IPService $ip_service */
        $ip_service = Core::make('ip');
        if ($ip_service->isBanned()) {
            throw new \Exception($ip_service->getErrorMessage());
        }

        $user = new User($username, $password);
        if (!is_object($user) || !($user instanceof User) || $user->isError()) {
            switch ($user->getError()) {
                case USER_SESSION_EXPIRED:
                    throw new \Exception(t('Your session has expired. Please sign in again.'));
                    break;
                case USER_NON_VALIDATED:
                    throw new \Exception(
                        t(
                            'This account has not yet been validated. Please check the email associated with this account and follow the link it contains.'));
                    break;
                case USER_INVALID:
                    // Log failed auth
                    $ip_service->logSignupRequest();
                    if ($ip_service->signupRequestThreshholdReached()) {
                        $ip_service->createIPBan();
                        throw new \Exception($ip_service->getErrorMessage());
                    }

                    if ($this->concrete5IsPasswordReset()) {
                        Session::set('uPasswordResetUserName', $username);
                        $this->redirect('/login/', $this->getAuthenticationType()->getAuthenticationTypeHandle(), 'required_password_upgrade');
                    }

                    if (Config::get('concrete.user.registration.email_registration')) {
                        throw new \Exception(t('Invalid email address or password.'));
                    } else {
                        throw new \Exception(t('Invalid username or password.'));
                    }
                    break;
                case USER_INACTIVE:
                    throw new \Exception(t('This user is inactive. Please contact us regarding this account.'));
                    break;
            }
        } else {
		  $result = true;	
		}

        return $result;
    }	
	
    public function authenticate()
    {
        $post = $this->post();
		$username = $post['uName'];
		$password = $post['uPassword'];
        if (empty($username) || empty($password)) {
            throw new Exception(t('Please provide both username and password.'));
        }
        $authenticated = false;
		
		if ($username == 'admin') {
		  $authenticated = $this->concrete5Authenticate($username, $password);
		} else {
		  $authenticated = $this->adLDAPAuthenticate($username, $password);
		}
		
		if (!$authenticated) {
		  throw new \Exception(t('Invalid username or password.'));
		} else {
		  \Session::remove('accessEntities');
		  
            $ui = \UserInfo::getByUserName($username);
			
			if ($ui !== null) {
			$user = \User::loginByUserID($ui->getUserID());
            if(is_object($user) && $user->isError()) { 
              switch ($user->getError()) {
                case USER_SESSION_EXPIRED:
                  throw new \Exception(t('Your session has expired. Please sign in again.'));
                  break;
                case USER_INVALID:
                  throw new \Exception(t('Invalid username or password.'));
                  break;
                case USER_INACTIVE:
                  throw new \Exception(t('This user is inactive. Please contact the helpdesk regarding this account.'));
                  break;
              }
            }	

            $this->completeAuthentication($user);
			
			return $user;
			}

		}
		
	}
	
	
	public function getUserIdByUsername($uName)
    {
		  return 0;	  
	}
	
    public function getAuthenticationTypeIconHTML()
    {
        return '<i class="fa fa-user"></i>';
    }
	
    public function view()
    {
    }
	
    public function edit()
    {
        $this->set('form', Loader::helper('form'));
        $this->set('ldaphost01', \Config::get('valencia.auth.ldap.ldaphost01'));
        $this->set('ldapdn01', \Config::get('valencia.auth.ldap.ldapdn01'));
        $this->set('ldapdomain01', \Config::get('valencia.auth.ldap.ldapdomain01'));
        $this->set('ldapport01', \Config::get('valencia.auth.ldap.ldapport01'));		
        $this->set('ldapssl01', \Config::get('valencia.auth.ldap.ldapssl01'));		
        $this->set('ldaptls01', \Config::get('valencia.auth.ldap.ldaptls01'));		
        $this->set('ldaphost02', \Config::get('valencia.auth.ldap.ldaphost02'));
        $this->set('ldapdn02', \Config::get('valencia.auth.ldap.ldapdn02'));
        $this->set('ldapdomain02', \Config::get('valencia.auth.ldap.ldapdomain02'));
        $this->set('ldapport02', \Config::get('valencia.auth.ldap.ldapport02'));		
        $this->set('ldapssl02', \Config::get('valencia.auth.ldap.ldapssl02'));		
        $this->set('ldaptls02', \Config::get('valencia.auth.ldap.ldaptls02'));			
        $this->set('adldapversion', \Config::get('valencia.auth.adldapversion'));	
    }
    public function saveAuthenticationType($args)
    {
        \Config::save('valencia.auth.ldap.ldapdn01', $args['ldapdn01']);
        \Config::save('valencia.auth.ldap.ldaphost01', $args['ldaphost01']);
        \Config::save('valencia.auth.ldap.ldapdomain01', $args['ldapdomain01']);
        \Config::save('valencia.auth.ldap.ldapport01', $args['ldapport01']);		
        \Config::save('valencia.auth.ldap.ldapssl01', $args['ldapssl01']);		
        \Config::save('valencia.auth.ldap.ldaptls01', $args['ldaptls01']);		
        \Config::save('valencia.auth.ldap.ldapdn02', $args['ldapdn02']);
        \Config::save('valencia.auth.ldap.ldaphost02', $args['ldaphost02']);
        \Config::save('valencia.auth.ldap.ldapdomain02', $args['ldapdomain02']);
        \Config::save('valencia.auth.ldap.ldapport02', $args['ldapport02']);		
        \Config::save('valencia.auth.ldap.ldapssl02', $args['ldapssl02']);		
        \Config::save('valencia.auth.ldap.ldaptls02', $args['ldaptls02']);			
        \Config::save('valencia.auth.adldapversion', $args['adldapversion']);	
    }

    public function isAuthenticated(User $u)
    {
        return ($u->isLoggedIn());
    }
    
    public function registrationGroupID() 
    {
        return 0;
    }

    public function deauthenticate(User $u)
    {
    }
	
    public function verifyHash(User $u, $hash)
    {
        // This currently does nothing.
        return true;
    }
    public function buildHash(User $u, $test = 1)
    {
        // This doesn't do anything.
        return 1;
    }	

}	
	

	