<?php

namespace App\Controller\Admin;

use App\Entity\Reclamations;
use App\Repository\ReclamationsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query\FilterCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FieldCollection;
use EasyCorp\Bundle\EasyAdminBundle\Collection\FilterCollection as CollectionFilterCollection;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Dto\SearchDto;
use EasyCorp\Bundle\EasyAdminBundle\Dto\EntityDto;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Date;

class ReclamationsCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Reclamations::class;
    }



    
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('status')->setChoices([
                Reclamations::STATUS_OPENED => 'Ouvert',
                    Reclamations::STATUS_CLOSED => 'Clôturé',
                    Reclamations::STATUS_PROGRESS => 'En cours',
            ]))
            ->add('createdAt')
            
            ;
    }


    public function configureFields(string $pageName): iterable
    {


        if ($this->isGranted('ROLE_ADMIN')) {
            return [
                IdField::new('id')->hideOnForm(),
                ChoiceField::new('type')->setChoices([
                    'Financière' => 'Financière',
                    'Technique' => 'Technique',
                    'Commerciale' => 'Commerciale',
                    'Autre' => 'Autre',
                ])->setDisabled(), 
                TextareaField::new('description')->setDisabled(),
                ChoiceField::new('status')->setChoices(
                    [
                        Reclamations::STATUS_OPENED => Reclamations::STATUS_OPENED,
                        Reclamations::STATUS_CLOSED => Reclamations::STATUS_CLOSED,
                        Reclamations::STATUS_PROGRESS => Reclamations::STATUS_PROGRESS,
                    ]
                )
                ->renderAsBadges([
                    Reclamations::STATUS_OPENED => 'warning',
                    Reclamations::STATUS_CLOSED => 'success',
                    Reclamations::STATUS_PROGRESS => 'info',

                   
                ]),
                TextareaField::new('response'),
                TextField::new('user.firstName')->hideOnForm()->setLabel('Prénom')->setPermission('ROLE_ADMIN'),
                TextField::new('user.lastName ')->hideOnForm()->setLabel('Nom')->setPermission('ROLE_ADMIN'),
                TextField::new('user.phoneNumber')->hideOnForm()->setLabel('Téléphone')->setPermission('ROLE_ADMIN'),
                TextField::new('user.email')->hideOnForm()->setLabel('Email')->setPermission('ROLE_ADMIN'),
                DateField::new('createdAt')->hideOnForm()->setLabel('Date de création'),
                DateField::new('updatedAt')->hideOnForm()->setLabel('Dernière mise à jour'),


            ];

        }else{
            return [
                IdField::new('id')->hideOnForm(),
                TextField::new('trackingCode')->hideOnForm(),
                ChoiceField::new('type')->setChoices([
                    'Financière' => 'Financière',
                    'Technique' => 'Technique',
                    'Commerciale' => 'Commerciale',
                    'Autre' => 'Autre',
                ]),
                TextareaField::new('description'),
                TextareaField::new('response')->hideOnForm(),
                ChoiceField::new('status')->setChoices(
                    [
                        Reclamations::STATUS_OPENED => Reclamations::STATUS_OPENED,
                        Reclamations::STATUS_CLOSED => Reclamations::STATUS_CLOSED,
                        Reclamations::STATUS_PROGRESS => Reclamations::STATUS_PROGRESS,
                    ]
                )
                ->hideOnForm()->renderAsBadges([
                    Reclamations::STATUS_OPENED => 'warning',
                    Reclamations::STATUS_CLOSED => 'success',
                    Reclamations::STATUS_PROGRESS => 'info',

                   
                ]),
                DateField::new('createdAt')->hideOnForm()->setLabel('Date de création'),
                DateField::new('updatedAt')->hideOnForm()->setLabel('Dernière mise à jour'),
            ];
        }
    }


    public function createIndexQueryBuilder(SearchDto $searchDto, EntityDto $entityDto, FieldCollection $fields, CollectionFilterCollection $filters): QueryBuilder
    {
        $queryBuilder = parent::createIndexQueryBuilder($searchDto, $entityDto, $fields, $filters);
        
        if ($this->isGranted('ROLE_ADMIN')) {
            $queryBuilder->join('entity.user', 'u')
                ->addSelect('u');
        } else {
            $queryBuilder->andWhere('entity.user = :user')
                ->setParameter('user', $this->getUser());
        }

        return $queryBuilder;
    
}

public function updateEntity(EntityManagerInterface $entityManager, $entityInstance): void
{
    if ($this->isGranted('ROLE_ADMIN')) {
        $entityInstance->setUpdatedAt(new \DateTimeImmutable());
        $entityManager->persist($entityInstance);
        $entityManager->flush();


}

}


public function configureActions(Actions $actions): Actions
{
    return $actions
        // ...
        ->add(Crud::PAGE_INDEX, Action::DETAIL)
        ->add(Crud::PAGE_EDIT, Action::INDEX)


        ;
}



}