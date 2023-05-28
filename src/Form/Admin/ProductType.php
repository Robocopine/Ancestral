<?php

namespace App\Form\Admin;

use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['label' => 'product.name.label', 'attr' => [
                'placeholder' => 'product.name.placeholder'
            ]])
            ->add('illustration', FileType::class, [
                'label' => 'Illustration (jpg, png)',
                'attr' => ['placeholder' => 'Téléchargez la couverture de votre article'],

                // unmapped means that this field is not associated to any entity property
                'mapped' => false,

                // make it optional so you don't have to re-upload the PDF file
                // everytime you edit the Product details
                'required' => false,

                 // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format jpg, jpeg ou png',
                    ])
                ],
            ])
            ->add('subtitle', TextType::class, ['label' => 'product.subtitle.label', 'attr' => [
                'placeholder' => 'product.subtitle.placeholder'
            ]])
            ->add('description', TextareaType::class, ['label' => 'product.description.label', 'attr' => [
                'placeholder' => 'product.description.placeholder'
            ]])
            ->add('price', MoneyType::class, ['label' => 'product.price.label', 'attr' => [
                'placeholder' => 'product.price.placeholder'
            ]])
            ->add('category');
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
