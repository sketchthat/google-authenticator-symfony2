<?php
    namespace Sketchthat\TwoFactorBundle\Helper\Authentication;

    use Google\Authenticator\GoogleAuthenticator;
    use Sketchthat\TwoFactorBundle\Entity\Users;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


    class Helper {
        private $server;
        private $google;

        /**
         * @param string $server
         * @param GoogleAuthenticator $google
         */
        public function __construct($server, GoogleAuthenticator $google) {
            $this->server = $server;
            $this->google = $google;
        }

        /**
         * @param Users $user
         */
        public function getQr(Users $user) {
            return $this->google->getUrl($user->getUsername(), $this->server, $user->getGoogleAuthenticatorCode());
        }

        /**
         * @param Users $user
         * @param $code
         * @return string
         */
        public function checkCode(Users $user, $code)
        {
            return $this->google->checkCode($user->getGoogleAuthenticatorCode(), $code);
        }

        /**
         * @return string
         */
        public function getSecret()
        {
            return $this->google->generateSecret();
        }

        /**
         * @param UsernamePasswordToken $token
         * @return string
         */
        public function getSessionKey(UsernamePasswordToken $token)
        {
            return sprintf('sketchthat_google_authenticator_%s_%s', $token->getProviderKey(), $token->getUsername());
        }
    }