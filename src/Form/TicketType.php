<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Responsible;
use App\Entity\Status;
use App\Entity\Ticket;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('authorEmail', EmailType::class, [
                'label' => 'Adresse e-mail',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer votre adresse e-mail',
                    ]),
                ],
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez entrer une description',
                    ]),
                    new Length([
                        'min' => 20,
                        'max' => 250,
                        'minMessage' => 'La description doit faire au moins {{ limit }} caractères',
                        'maxMessage' => 'La description ne peut pas dépasser {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'label' => 'Catégorie',
                'constraints' => [
                    new NotBlank([
                        'message' => 'Veuillez sélectionner une catégorie',
                    ]),
                ],
            ]);

        // Ajout des champs supplémentaires pour l'administrateur
        if ($options['is_admin']) {
            $builder
                ->add('status', EntityType::class, [
                    'class' => Status::class,
                    'choice_label' => 'name',
                    'label' => 'Statut',
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Veuillez sélectionner un statut',
                        ]),
                    ],
                ])
                ->add('responsible', EntityType::class, [
                    'class' => Responsible::class,
                    'choice_label' => 'name',
                    'label' => 'Responsable',
                    'required' => false,
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
            'is_admin' => false,
        ]);
    }
} 