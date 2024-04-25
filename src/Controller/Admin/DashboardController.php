<?php

namespace App\Controller\Admin;

use App\Entity\Reclamations;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;



#[IsGranted('ROLE_USER')]
class DashboardController extends AbstractDashboardController
{
    
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {

        $adminUrlGenerator = $this->container->get(AdminUrlGenerator::class);
        return $this->redirect($adminUrlGenerator->setController(ReclamationsCrudController::class)->generateUrl());

    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Reclamation App')
            ->renderSidebarMinimized();

    }

    public function configureCrud(): Crud
    {
        return Crud::new()
          ->setDefaultSort(['id' => 'DESC']);

    }

    public function configureMenuItems(): iterable
    {

        yield MenuItem::linkToCrud('Reclamation', 'fas fa-list', Reclamations::class);

        yield MenuItem::linkToCrud('Client', 'fas fa-user', User::class)->setPermission('ROLE_ADMIN');

        yield MenuItem::linkToCrud('Profile', 'fa fa-id-card',User::class)->setEntityId(
            $this->getUser()->getId()
        )->setAction(Action::EDIT);
        yield MenuItem::linkToLogout('Se déconnecter', 'fa fa-fw fa-sign-out');

        
    }


    public function configureUserMenu(UserInterface $user): UserMenu
    {

        // dd($user);
        return parent::configureUserMenu($user)
            ->setName($user->getFirstName())
            ->addMenuItems([
                MenuItem::linkToCrud('Profile', 'fa fa-id-card',User::class)->setEntityId(
                    $user->getId()
                )->setAction(Action::EDIT)
            ]);
    }
}

