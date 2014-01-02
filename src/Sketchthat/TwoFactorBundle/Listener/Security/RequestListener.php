<?php
    namespace Sketchthat\TwoFactorBundle\Listener\Security;

    use Sketchthat\TwoFactorBundle\Helper\Authentication\Helper;

    use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
    use Symfony\Component\HttpFoundation\RedirectResponse;
    use Symfony\Component\Security\Core\SecurityContextInterface;
    use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
    use Symfony\Component\HttpKernel\Event\GetResponseEvent;
    use Symfony\Bundle\FrameworkBundle\Routing\Router;

    class RequestListener {
        /**
         * @var \Sketchthat\TwoFactorBundle\Helper\Authentication\Helper
         */
        protected $helper;

        /**
         * @var \Symfony\Component\Security\Core\SecurityContextInterface
         */
        protected $securityContext;

        /**
         * @var \Symfony\Bundle\FrameworkBundle\Templating\EngineInterface
         */
        protected $templating;

        /**
         * @var \Symfony\Bundle\FrameworkBundle\Routing\Router
         */
        protected $router;

        /**
         * @param Helper $helper
         * @param SecurityContextInterface $securityContext
         * @param EngineInterface $templating
         * @param Router $router
         */
        public function __construct(Helper $helper, SecurityContextInterface $securityContext, EngineInterface $templating, Router $router) {
            $this->helper = $helper;
            $this->securityContext = $securityContext;
            $this->templating = $templating;
            $this->router = $router;
        }

        /**
         * @param GetResponseEvent $event
         */
        public function onCoreRequest(GetResponseEvent $event) {
            $token = $this->securityContext->getToken();

            if(!$token) {
                return;
            }

            if(!$token instanceof UsernamePasswordToken) {
                return;
            }

            $key = $this->helper->getSessionKey($this->securityContext->getToken());
            $request = $event->getRequest();
            $session = $request->getSession();
            $user = $this->securityContext->getToken()->getUser();



            if(!$session->has($key)) {
                return;
            }

            if($session->get($key) === true) {
                return;
            }

            if($request->getMethod() == 'POST') {
                if($this->helper->checkCode($user, $request->get('auth_code')) == true) {
                    $session->set($key, true);

                    $redirect = new RedirectResponse($this->router->generate('dashboard'));
                    $event->setResponse($redirect);

                    return;
                } else {
                    $session->getFlashBag()->set('error', 'The Google Authentication Code is not valid');
                }
            }

            $response = $this->templating->renderResponse('SketchthatTwoFactorBundle:Authentication:two-factor.html.twig');
            $event->setResponse($response);
        }
    }
