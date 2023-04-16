<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Controller\Admin\CategoryCrudController;
use App\Entity\Board;
use App\Entity\Subject;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    private AdminUrlGenerator $adminUrlGenerator;

    public function __construct(AdminUrlGenerator $adminUrlGenerator)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
    }
    #[Route('/admin', name: 'admin')]
    public function index(): Response
    {


        // Option 1. You can make your dashboard redirect to some common page of your backend


        return $this->redirect($this->adminUrlGenerator->setController(CategoryCrudController::class)->generateUrl());

        // Option 2. You can make your dashboard redirect to different pages depending on the user
        //
        // if ('jane' === $this->getUser()->getUsername()) {
        //     return $this->redirect('...');
        // }

        // Option 3. You can render some custom template to display a proper dashboard with widgets, etc.
        // (tip: it's easier if your template extends from @EasyAdmin/page/content.html.twig)
        //
        // return $this->render('some/path/my-dashboard.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('EnterCorp');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('Dashboard', 'fa fa-home');


        yield MenuItem::subMenu('Catégorie')->setSubItems([
            MenuItem::linkToCrud('Voir les Catégories', 'fa fa-eye', Category::class),
            MenuItem::linkToCrud('Ajouter une Categorie ', 'fa fa-plus', Category::class)->setAction(Crud::PAGE_NEW)
        ]);
        yield MenuItem::subMenu('Utilisateurs')->setSubItems([
            MenuItem::linkToCrud('Voir les Utilisateurs', 'fa fa-eye', User::class),
            MenuItem::linkToCrud('Ajouter un Utilisateur ', 'fa fa-plus', User::class)->setAction(Crud::PAGE_NEW)
        ]);
        yield MenuItem::subMenu('Board')->setSubItems([
            MenuItem::linkToCrud('Voir les Boards', 'fa fa-eye', Board::class),
            MenuItem::linkToCrud('Ajouter un Board ', 'fa fa-plus', Board::class)->setAction(Crud::PAGE_NEW)
        ]);
        yield MenuItem::subMenu('Sujets')->setSubItems([
            MenuItem::linkToCrud('Voir les Sujets', 'fa fa-eye', Subject::class),
            MenuItem::linkToCrud('Ajouter un Sujet ', 'fa fa-plus', Subject::class)->setAction(Crud::PAGE_NEW)
        ]);
        yield MenuItem::subMenu('Messages')->setSubItems([
            MenuItem::linkToCrud('Voir les Messages', 'fa fa-eye', Subject::class),
            MenuItem::linkToCrud('Ajouter un Message ', 'fa fa-plus', Subject::class)->setAction(Crud::PAGE_NEW)
        ]);

        // yield MenuItem::linkToCrud('The Label', 'fas fa-list', EntityClass::class);
    }
}
