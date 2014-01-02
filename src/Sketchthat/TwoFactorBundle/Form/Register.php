<?php

namespace Sketchthat\TwoFactorBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class Register extends AbstractType {
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder->add('username', 'email',
            array(
                'label' => 'Email Address (Username)',
                'required' => true,
                'attr'  => array(
                    'class' => 'form-control'
                )
            )
        );

        $builder->add('password', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'options' => array(
                'attr' => array(
                    'class' => 'form-control'
                )
            ),
            'required' => true,
            'first_options'  => array(
                'label' => 'Password'
            ),
            'second_options' => array(
                'label' => 'Repeat Password'
            )
        ));

        $builder->add('save', 'submit', array(
            'label' => 'Register',
            'attr'  => array(
                'class' => 'btn btn-primary'
            )
        ));
    }

    public function getName() {
        return 'register';
    }
}