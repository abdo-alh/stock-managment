<?php

namespace App\Form;

use App\Entity\Purshase;
use App\Entity\Supplier;
use DateTime;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Date;

class PurshaseType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('supplier', EntityType::class, [
                'label' => false,
                'required' => true,
                'class' => Supplier::class,
                'choice_label' => 'name'
            ])
            ->add('note', TextareaType::class, [
                'label' => false,
                'required' => false
            ])
            ->add('purshase_date', DateType::class, [
                'label' => false,
                'required' => true,
                'widget' => 'single_text',
                'format' => 'dd/MM/yyyy',
                'data'=> new DateTime()
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Purshase::class,
        ]);
    }
}
