<?php

namespace App\Form\Security;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class AccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'user.email.label', 'attr' => [
                'placeholder' => 'user.email.placeholder'
            ]])

            ->add('oldEmail', HiddenType::class, ['label' => 'user.email.label', 'required' => false, 'mapped' => false, 'property_path' => 'user.email'])

            ->add('lastName', TextType::class, ['label' => 'user.lastName.label', 'attr' => [
                'placeholder' => 'user.lastName.placeholder'
            ]])

            ->add('firstName', TextType::class, ['label' => 'user.firstName.label', 'attr' => [
                'placeholder' => 'user.firstName.placeholder'
            ]])

            ->add('oldPassword', PasswordType::class, [
                'label' => 'user.currentPassword.label', 
                'attr' => [
                    'placeholder' => 'user.currentPassword.placeholder'
                ],
                'mapped' => false,
                'required' => false,
            ])

            ->add('newPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'user.password.invalidMessage',
                'mapped' => false,
                'required' => false,
                'first_options' => ['label' => 'user.newPassword.label', 'attr' => ['placeholder' => 'user.newPassword.placeholder', 'autocomplete' => 'new-password' ]],
                'second_options' => ['label' => 'user.newPasswordConfirm.label', 'attr' => ['placeholder' => 'user.newPasswordConfirm.placeholder', 'autocomplete' => 'new-password']],
                'constraints' => [
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
