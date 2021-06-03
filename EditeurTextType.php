<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class EditeurTextType extends ApplicationType
{

    /**
     * @inheritDoc
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
       // $text = "faafafa";
        $builder
         /*  ->add('text', CKEditorType::class, [
                'data' => $options['textEditor'],
                'config' => ['toolbar' => 'full',
                    'required' => true],
            ])*/
            ->add('content', HiddenType::class)
            ->add('DOCX', SubmitType::class, ['label' => 'DOCX'], [
            'attr' => ['class' => 'Button'],
                ])
            ->add('PDF', SubmitType::class, ['label' => 'PDF'], [
            'attr' => ['class' => 'Button'],
                ])
            ->add('JPG', SubmitType::class, ['label' => 'JPG'], [
            'attr' => ['class' => 'Button'],
                ])
            ->add("nom_document",TextType::class,[
                'attr' => ['value' => $options['text']]
            ]
            )



        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
           'text' => null,
            // Configure your form options here
        ]);
    }

}
