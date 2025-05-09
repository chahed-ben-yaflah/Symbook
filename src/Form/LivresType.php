<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Livres;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LivresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titre')
            ->add('slug')
            ->add('resume')
            ->add('isbn')
            ->add('image')
            ->add('editeur')
            ->add('dateEdition', null, [
                'widget' => 'single_text',
            ])
            ->add('prix')
           ->add('categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'libelle', // Affiche le libellé dans les options
                'choice_value' => 'libelle', // Utilise le libellé comme valeur soumise
                'placeholder' => 'Choisir une catégorie',
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Livres::class,
        ]);
    }
}
