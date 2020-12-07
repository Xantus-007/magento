<?php

namespace Dbm\Model;
use Symfony\Component\Validator\Constraints as Assert;

class Form
{
    public static function getForm($app)
    {
        $form = $app['form.factory']->createBuilder('form')
            ->add('name', 'text', array(
                'label' => false,
                'constraints' => array(
                    new Assert\NotBlank(),
                    new Assert\Length(array('min' => 2))
                ),
                'attr'=> array(
                    'placeholder'=>'Votre prénom',
                    'class'=>'input-name form-field',
                    'data--ganame' => 'Prenom'
                )
            ))
            ->add('email', 'text', array(
                'label' => false,
                'constraints' => array(
                    new Assert\Email()
                ),
                'attr'=> array(
                    'placeholder'=>'Votre e-mail',
                    'class'=>'input-email form-field',
                    'data--ganame' => 'Email'
                )
            ))
            ->getForm();
        
        return $form;
    }
}