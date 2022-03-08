<?php

namespace App\Form;

use App\Entity\User;
use App\Entity\Produit;
use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;


class CommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder

            ->add('user', Entitytype::class, [
                'class' => User::class,
                'choice_label' => 'id',

            ])
            // ->add('items')
            // ->add('produits', Entitytype::class, [
            //     'class' => Produit::class,
            //     'choice_label' => 'id',
            //     'multiple' => true, //ca doit OBLIGATOIREMENT etre multiple car en retour ca atttend un tableau
            // ])
            ->add('etat')
            ->add('prixTotal');
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }
}