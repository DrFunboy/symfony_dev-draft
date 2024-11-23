<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityManagerInterface;
// use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class DefaultController extends AbstractController
{
    #[Route('/stats', name: 'route_stats')]
    public function stats(EntityManagerInterface $entityManager): Response
    {
        $json = [];
        $conn = $entityManager->getConnection();
        $sql = '
            SELECT 
                COUNT(developer.id) as cnt_developer,
                AVG(unix_timestamp(developer.birth)) as avg_birth_timestamp
            FROM 
                developer
            WHERE 
                active = 1
        ';
        $resultSet = $conn->executeQuery($sql);
        $json = $resultSet->fetchAllAssociative()[0];

        $sql = '
            SELECT 
                COUNT( depend.developer_id ) / COUNT(DISTINCT draft.id) as avg_depend,
                COUNT(DISTINCT draft.id) as cnt_draft
            FROM 
                draft

            LEFT JOIN developer_draft depend
            ON draft.id = depend.draft_id

            WHERE 
                draft.active = 1
        ';
        $resultSet = $conn->executeQuery($sql);
        $json = array_merge($json, $resultSet->fetchAllAssociative()[0]);

        return new Response(json_encode($json));
    }
}
