<?php

namespace Sketchthat\TwoFactorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sketchthat\TwoFactorBundle\Helper\Authentication\Helper as AuthenticationHelper;
use Google\Authenticator\GoogleAuthenticator;

class DefaultController extends Controller
{
    public function dashboardAction()
    {
        $helper = new AuthenticationHelper($this->container->getParameter('server_name'), new GoogleAuthenticator());

        return $this->render(
            'SketchthatTwoFactorBundle:Default:dashboard.html.twig',
            array(
                'status' => $this->getUser()->getTwoFactor(),
                'qr' => $helper->getQr($this->getUser())
            )
        );
    }

    public function toggleAction() {
        $user = $this->getUser();

        if($user->getTwoFactor() == 0) {
            $user->setTwoFactor(1);

            $this->get('session')->getFlashBag()->add('notice', 'Two factor authentication enabled');
        } else {
            $user->setTwoFactor(0);

            $this->get('session')->getFlashBag()->add('notice', 'Two factor authentication disabled');
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($user);
        $em->flush();

        return $this->redirect($this->generateUrl('logout'));
    }
}
