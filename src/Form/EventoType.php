<?php
namespace App\Form;

use App\Entity\Eventos;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EventoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nombre', TextType::class, ['label' => 'Nombre', 'attr' => ['class' => 'form-control form-control-lg']])
            ->add('fecha', DateType::class, [
                'label' => 'Fecha',
                'attr' => ['class' => 'form-control form-control-lg'],
                'years' => range(19, 30),
                'widget' => 'single_text',
            ])
            ->add('descripcion', TextType::class, ['label' => 'Descipción', 'attr' => ['class' => 'form-control form-control-lg']])
            ->add('duracionInicio', TimeType::class, [
                'label' => 'Hora de inicio de evento',
                'attr' => ['class' => 'form-control form-control-lg'],
            ])
            ->add('duracionFin', TimeType::class, [
                'label' => 'Hora final del evento',
                'attr' => ['class' => 'form-control form-control-lg'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Eventos::class,
        ));
    }
}
