<?php

namespace App\Form;

use App\Entity\User;
use App\Model\ChangePassword;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class ResetPasswordType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
                ->add('oldPassword', PasswordType::class,[
                    'label' =>false
                ])
                ->add('password', RepeatedType::class, array(
                    'type' => PasswordType::class,
                    'first_options' => ['label' => false],
                    'second_options' => ['label' => false],
                    'invalid_message' => 'يجب أن تكون كلتا كلمتي المرور متطابقتين',
                    'options' => array(
                        'attr' => array(
                            'class' => 'password-field'
                        )
                    ),
                    'required' => true,
                ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults(array(
            //mettre le nouveau formulaire
            'data_class' => ChangePassword::class,
            'csrf_token_id' => 'change_password',
        ));
    }
}

