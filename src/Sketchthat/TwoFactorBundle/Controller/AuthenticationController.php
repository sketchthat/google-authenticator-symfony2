<?php

namespace Sketchthat\TwoFactorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContext;

use Sketchthat\TwoFactorBundle\Entity\Users;
use Sketchthat\TwoFactorBundle\Form\Register;

use Sketchthat\TwoFactorBundle\Helper\Authentication\Helper as AuthenticationHelper;
use Google\Authenticator\GoogleAuthenticator;

class AuthenticationController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(
                SecurityContext::AUTHENTICATION_ERROR
            );
        } else {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        }

        $securityContext = $this->container->get('security.context');
        if( $securityContext->isGranted('IS_AUTHENTICATED_FULLY') ){
            return $this->redirect($this->generateUrl('dashboard'));
        }

        return $this->render(
            'SketchthatTwoFactorBundle:Authentication:login.html.twig',
            array(
                'last_username' => $session->get(SecurityContext::LAST_USERNAME),
                'error'         => $error,
            )
        );
    }

    public function registerAction(Request $request) {
        $user = new Users();

        $register = $this->createForm(new Register(), $user);

        $register->handleRequest($request);

        if($request->getMethod() == 'POST') {
            if($register->isValid()) {
                $factory = $this->get('security.encoder_factory');
                $encoder = $factory->getEncoder($user);

                $helper = new AuthenticationHelper($this->container->getParameter('server_name'), new GoogleAuthenticator());
                $user->setGoogleAuthenticatorCode($helper->getSecret());

                $user->setPassword($encoder->encodePassword($user->getPassword(), $user->getSalt()));

                $em = $this->getDoctrine()->getManager();
                $em->persist($user);
                $em->flush();

                $this->get('session')->getFlashBag()->add('notice', 'Account created. Please login');

                return $this->redirect($this->generateUrl('login'));
            }
        }

        return $this->render(
            'SketchthatTwoFactorBundle:Authentication:register.html.twig',
            array(
                'register' => $register->createView()
            )
        );
    }
}
