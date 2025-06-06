<?php

namespace App\Form;

use App\Entity\Article;
use App\Enum\CategoryEnum;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ArticleCategoryForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'label' => 'Titre',
            ])

            ->add('content', null, [
                'label' => 'Contenu',
            ])
            ->add('imageUrl', null, [
                'label' => 'Image',
            ])
            ->add('isPublished', null, [
                'label' => false,
            ])
            ->add('category', ChoiceType::class, [
                'choices' => [
                    'Assemblée' => CategoryEnum::ASSEMBLEE,
                    'Circonscription' => CategoryEnum::CIRCONSCRIPTION,
                ],
                'label' => 'Publier dans la catégorie',
                'placeholder' => 'Sélectionner une catégorie',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
