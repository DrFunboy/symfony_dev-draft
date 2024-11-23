<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Draft;
use App\Form\Developer1Type;
use App\Repository\DeveloperRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\PropertyAccess\PropertyAccess;

#[Route('/developer')]
final class DeveloperController extends AbstractController
{
    #[Route(name: 'app_developer_index', methods: ['GET'])]
    public function index(DeveloperRepository $developerRepository): Response
    {
        $response = array();
        foreach ($developerRepository->findAll() as $developer) {
            $response[] = array(
                'id' => $developer->getId(),
                'name' => $developer->getName(),
                'birth' => $developer->getBirth()->format('Y-m-d'),
                'email' => $developer->getEmail(),
                'tel' => $developer->getTel(),
                'role' => $developer->getRole(),
                'active' => $developer->getActive(),
            );
        };

        return new Response(json_encode(['rows' => $response]));
    }

    #[Route('/new', name: 'app_developer_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $developer = new Developer();
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($request->request->all() as $fieldName => $fieldValue){
            if ($fieldName == 'birth') $fieldValue = new \DateTime($fieldValue);
            if ($accessor->isWritable($developer, $fieldName)) {
                $accessor->setValue($developer, $fieldName, $fieldValue);
            }
        }
        $entityManager->persist($developer);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_developer_show', ['id' => $developer->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_developer_show', methods: ['GET'])]
    public function show(Developer $developer): Response
    {
        $response = array(
            'id' => $developer->getId(),
            'name' => $developer->getName(),
            'birth' => $developer->getBirth()->format('Y-m-d'),
            'email' => $developer->getEmail(),
            'tel' => $developer->getTel(),
            'role' => $developer->getRole(),
            'active' => $developer->getActive(),
        );
        return new Response(json_encode(['rows' => [$response]]));
    }

    #[Route('/{id}/edit', name: 'app_developer_edit', methods: ['POST'])]
    public function edit(Request $request, Developer $developer, EntityManagerInterface $entityManager, $id): Response
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($request->request->all() as $fieldName => $fieldValue){
            if ($fieldName == 'birth') $fieldValue = new \DateTime($fieldValue);
            if ($accessor->isWritable($developer, $fieldName)) {
                $accessor->setValue($developer, $fieldName, $fieldValue);
            }
        }
        $entityManager->persist($developer);
        $entityManager->flush();
        
        return $this->redirectToRoute('app_developer_show', ['id' => $developer->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_developer_delete', methods: ['POST'])]
    public function delete(Request $request, Developer $developer, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($developer);
        $entityManager->flush();
        return new Response(json_encode([]));
    }

    #[Route('/{id}/dependence', name: 'app_developer_draft', methods: ['GET', 'POST'])]
    public function dependence(Request $request, Developer $developer, EntityManagerInterface $entityManager): Response
    {
        $repositoryDraft = $entityManager->getRepository(Draft::class);
        $newDep = []; 
        if ($request->isMethod('POST')) {
            foreach($_POST['ids'] as $draft_id){
                $draft_id *=1;
                $newDep[$draft_id] = true;
            }
    
            $haveDep = [];
            foreach($developer->getIddraft() as $have_draft){
                if ( empty($newDep[$have_draft->getId()]) ) {
                    $developer->removeIddraft($have_draft);
                    continue;
                }
                $haveDep[$have_draft->getId()] = true;
            };
    
            foreach($_POST['ids'] as $draft_id){
                if ( empty($haveDep[$draft_id]) ) {
                    $draft_obj = $repositoryDraft->find($draft_id);
                    if (!empty($draft_obj)) {
                        $developer->addIddraft($draft_obj);
                    }
                }
            }

            $entityManager->persist($developer);
            $entityManager->flush();
        }
        
        
        $response = [];
        foreach($developer->getIddraft() as $draft){
            $response[] = $draft->getId();
        };
        return new Response(json_encode(['rows' => $response]));
    }
}
