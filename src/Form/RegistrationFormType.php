<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, ['label' => 'user.email.label', 'attr' => [
                'placeholder' => 'user.email.placeholder'
            ]])

            ->add('lastName', TextType::class, ['label' => 'user.lastName.label', 'attr' => [
                'placeholder' => 'user.lastName.placeholder'
            ]])

            ->add('firstName', TextType::class, ['label' => 'user.firstName.label', 'attr' => [
                'placeholder' => 'user.firstName.placeholder'
            ]])
            
            ->add('plainPassword', RepeatedType::class, [
                // instead of being set onto the object directly,
                // this is read and encoded in the controller
                'type' => PasswordType::class,
                'invalid_message' => 'user.password.invalidMessage',
                'mapped' => false,
                'required' => true,
                'first_options' => ['label' => 'user.password.label1', 'attr' => ['placeholder' => 'user.password.placeholder1', 'autocomplete' => 'new-password' ]],
                'second_options' => ['label' => 'user.password.label2', 'attr' => ['placeholder' => 'user.password.placeholder2', 'autocomplete' => 'new-password']],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a password',
                    ]),
                    new Length([
                        'min' => 6,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        // max length allowed by Symfony for security reasons
                        'max' => 4096,
                    ]),
                ],
            ])

            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
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
