<?php

namespace App\Form;

use App\Entity\Annonce;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AnnonceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, ["label" => "Quel est le titre de l’annonce ?"])
            ->add('category', null,  ["label" => "Choisissez une catégorie suggerée."])
            ->add('state', null, ["label" => "Etat"])
            ->add('content', null, ["label" => "Contenu de votre annonce."])
            ->add('location', null, ["label" => "Département"])
            ->add('price', null, ["label" => "Votre prix."])
            ->add('image');
                      
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Annonce::class,
        ]);
    }
}
