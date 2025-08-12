<?php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\Produit;

class ProduitType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom')
            ->add('prix')
            ->add('image')
            //->add('featured')
            ->add('taille', ChoiceType::class, [
                'choices'  => [
                    'XS' => 'XS',
                    'S'  => 'S',
                    'M'  => 'M',
                    'L'  => 'L',
                    'XL' => 'XL',
                ],
                'expanded' => true,   // cases à cocher
                'multiple' => true,   // choix multiple autorisé
                'label' => 'Tailles',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Produit::class,
        ]);
    }
}
