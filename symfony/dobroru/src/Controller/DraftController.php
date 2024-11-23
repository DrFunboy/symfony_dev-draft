<?php

namespace App\Controller;

use App\Entity\Developer;
use App\Entity\Draft;
use App\Form\DraftType;
use App\Repository\DraftRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use Symfony\Component\PropertyAccess\PropertyAccess;

#[Route('/draft')]
final class DraftController extends AbstractController
{
    #[Route(name: 'app_draft_index', methods: ['GET'])]
    public function index(DraftRepository $draftRepository): Response
    {
        $response = array();
        foreach ($draftRepository->findAll() as $draft) {
            $response[] = array(
                'id' => $draft->getId(),
                'name' => $draft->getName(),
                'clientname' => $draft->getClientname(),
                'active' => $draft->getActive(),
            );
        };

        return new Response(json_encode(['rows' => $response]));
    }

    #[Route('/new', name: 'app_draft_new', methods: ['POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $draft = new Draft();
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($request->request->all() as $fieldName => $fieldValue){
            if ($accessor->isWritable($draft, $fieldName)) {
                $accessor->setValue($draft, $fieldName, $fieldValue);
            }
        }
        $entityManager->persist($draft);
        $entityManager->flush();
        return $this->redirectToRoute('app_draft_show', ['id' => $draft->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'app_draft_show', methods: ['GET'])]
    public function show(Draft $draft): Response
    {
        $response = array(
            'id' => $draft->getId(),
            'name' => $draft->getName(),
            'clientname' => $draft->getClientname(),
            'active' => $draft->getActive(),
        );
        return new Response(json_encode(['rows' => [$response]]));
    }

    #[Route('/{id}/edit', name: 'app_draft_edit', methods: ['POST'])]
    public function edit(Request $request, Draft $draft, EntityManagerInterface $entityManager): Response
    {
        $accessor = PropertyAccess::createPropertyAccessor();
        foreach($request->request->all() as $fieldName => $fieldValue){
            if ($accessor->isWritable($draft, $fieldName)) {
                $accessor->setValue($draft, $fieldName, $fieldValue);
            }
        }
        $entityManager->persist($draft);
        $entityManager->flush();
        return $this->redirectToRoute('app_draft_show', ['id' => $draft->getId()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/delete', name: 'app_draft_delete', methods: ['POST'])]
    public function delete(Request $request, Draft $draft, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($draft);
        $entityManager->flush();
        return new Response(json_encode([]));
    }

    #[Route('/{id}/dependence', name: 'app_draft_developer', methods: ['GET', 'POST'])]
    public function dependence(Request $request, Draft $draft, EntityManagerInterface $entityManager): Response
    {
        $repositoryDeveloper = $entityManager->getRepository(Developer::class);
        $newDep = []; 
        if ($request->isMethod('POST')) {
            foreach($_POST['ids'] as $developer_id){
                $developer_id *=1;
                $newDep[$developer_id] = true;
            }
    
            $haveDep = [];
            foreach($draft->getIddeveloper() as $have_developer){
                if ( empty($newDep[$have_developer->getId()]) ) {
                    $draft->removeIddeveloper($have_developer);
                    continue;
                }
                $haveDep[$have_developer->getId()] = true;
            };
    
            foreach($_POST['ids'] as $developer_id){
                if ( empty($haveDep[$developer_id]) ) {
                    $developer_obj = $repositoryDeveloper->find($developer_id);
                    if (!empty($developer_obj)) {
                        $draft->addIddeveloper($developer_obj);
                    }
                }
            }

            $entityManager->persist($draft);
            $entityManager->flush();
        }
        
        
        $response = [];
        foreach($draft->getIddeveloper() as $developer){
            $response[] = $developer->getId();
        };
        return new Response(json_encode(['rows' => $response]));
    }
}
