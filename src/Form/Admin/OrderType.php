<?php

namespace App\Form\Admin;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('createdAt', DateTimeType::class, [
                'label' => 'Date de création',
                'input' => 'datetime_immutable',
            ])
            ->add('carrierName')
            ->add('delivery')
            ->add('isPaid')
            ->add('reference')
            ->add('stripeSessionId')
            ->add('user')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
