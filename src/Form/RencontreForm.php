<?php

namespace App\Form;

use App\Entity\Rencontre;
use App\Enum\CommuneEnum;
use App\Enum\RencontreEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\EnumType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class RencontreForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre', TextType::class, [
                'label' => 'Titre de la rencontre',
                'constraints' => [
                    new NotBlank(['message' => 'Le titre est obligatoire'])
                ],
                'attr' => [
                    'placeholder' => 'Ex: Permanence parlementaire, Rencontre citoyenne...'
                ]
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Décrivez l\'objet de cette rencontre...'
                ]
            ])
            ->add('date', DateTimeType::class, [
                'label' => 'Date et heure',
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(['message' => 'La date est obligatoire'])
                ],
                'attr' => [
                    'min' => (new \DateTime())->format('Y-m-d\TH:i')
                ]
            ])
            ->add('lieu', TextType::class, [
                'label' => 'Lieu précis',
                'constraints' => [
                    new NotBlank(['message' => 'Le lieu est obligatoire'])
                ],
                'attr' => [
                    'placeholder' => 'Ex: Mairie, Salle des fêtes, Place du marché...'
                ]
            ])
            ->add('commune', EnumType::class, [
                'class' => CommuneEnum::class,
                'label' => 'Commune',
                'choice_label' => fn(CommuneEnum $commune) => $commune->getLabel(),
                'constraints' => [
                    new NotBlank(['message' => 'La commune est obligatoire'])
                ],
                'placeholder' => 'Choisissez une commune'
            ])
            ->add('type', EnumType::class, [
                'class' => RencontreEnum::class,
                'label' => 'Type de rencontre',
                'choice_label' => fn(RencontreEnum $type) => $type->getLabel(),
                'constraints' => [
                    new NotBlank(['message' => 'Le type de rencontre est obligatoire'])
                ],
                'placeholder' => 'Choisissez un type'
            ])
            ->add('visible', CheckboxType::class, [
                'label' => 'Visible sur le site public',
                'required' => false,
                'data' => true, // Par défaut, la rencontre est visible
                'help' => 'Décochez pour masquer cette rencontre du site public'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rencontre::class,
        ]);
    }
}
