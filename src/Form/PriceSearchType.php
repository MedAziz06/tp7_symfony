<?php
namespace App\Form;

use App\Entity\PriceSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class PriceSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('minPrice', NumberType::class, [
                'label' => 'Prix min',
                'attr' => [
                    'placeholder' => 'Prix minimum'
                ]
            ])
            ->add('maxPrice', NumberType::class, [
                'label' => 'Prix max',
                'attr' => [
                    'placeholder' => 'Prix maximum'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PriceSearch::class,
        ]);
    }
}