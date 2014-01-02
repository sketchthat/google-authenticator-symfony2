<?php
namespace Sketchthat\TwoFactorBundle\Listener\Security;

use Sketchthat\TwoFactorBundle\Helper\Authentication\Helper;
use Sketchthat\TwoFactorBundle\Entity\Users;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class LoginListener {
    /**
     * @var \Sketchthat\TwoFactorBundle\Helper\Authentication\Helper
     */
    private $helper;

    /**
     * @param Helper $helper
     */
    public function __construct(Helper $helper) {
        $this->helper = $helper;
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event) {
        if(!$event->getAuthenticationToken() instanceof UsernamePasswordToken) {
            return;
        }

        $token = $event->getAuthenticationToken();
        $user = $token->getUser();

        if(!$user instanceof Users) {
            return;
        }
        if(!$user->getGoogleAuthenticatorCode()) {
            return;
        }
        if(!$user->getTwoFactor()) {
            return;
        }

        $event->getRequest()->getSession()->set($this->helper->getSessionKey($token), null);
    }
}
