<?php

namespace App\Form\Admin;

use App\Entity\Section;
use App\Entity\ClientEdit;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\ChoiceList\ChoiceList;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ClientEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $sectionSelected = $options['sectionSelected'];

        $builder
            ->add('file', FileType::class, [
                'label' => 'Fichier image',
                'attr' => ['placeholder' => 'Téléchargez l\'image de l\'élément'],

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
                            'image/ico',
                        ],
                        'mimeTypesMessage' => 'Le fichier doit être au format jpg, jpeg ou png',
                    ])
                ],
            ])
            ->add('string', TextareaType::class, [
                'label' => 'Contenu de l\'élément', 
                'attr' => [
                    'placeholder' => 'Entrez le texte de l\'élément'
                ],
                'mapped' => false,
                'required' => false,
            ])
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'required' => false,
                'mapped' => false,
                'placeholder' => 'Choisissez un élément',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => ClientEdit::class,
            'sectionSelected' => null,
        ]);
    }
}
