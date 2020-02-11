<?php
namespace App\Form;

use App\Entity\Moneda;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MonedaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, ['label' => 'Nombre', 'attr' => ['class' => 'form-control form-control-lg']])
            ->add('vecesRecibidas', NumberType::class, ['label' => 'Nombre', 'attr' => ['class' => 'form-control form-control-lg']])
            ->add('rango', NumberType::class, ['label' => 'Nombre', 'attr' => ['class' => 'form-control form-control-lg']])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Moneda::class,
        ));
    }
}
