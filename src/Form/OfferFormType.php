<?php

namespace App\Form;

use App\Entity\ContractType;
use App\Entity\Offer;
use App\Form\ContractTypeFormType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class OfferFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('description',TextareaType::class, array('attr' => array('rows'=>'8')))
            ->add('city')
            ->add('salary')
            ->add('workingTime')
            ->add('remote')
            // ->add('contractType', CollectionType::class, [
            //     'entry_type' => ContractTypeFormType::class,
            //     'entry_options' => ['name' => false],
            // ])
            ->add('contractType', EntityType::class, [
                'class' => ContractType::class, 
                'choice_label'=>'name'
                ])
            // ->add('contractType', EntityType::class, [
            //     'class' => ContractType::class,
            //     'choice_label' => 'name',
            //     'multiple' => true,
            //     'placeholder' => 'Choisir un ou plusieurs type de contrat',
            //     'required' => true,
            //     'expanded'=> true])
            ->add('Envoyer',SubmitType::class, array('attr' => array('class'=>'button'))) 
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Offer::class,
        ]);
    }
}
