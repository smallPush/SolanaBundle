<?php

namespace App\Controller;

use App\Application\User\Query\SearchUsersQuery;
use App\Contract\Bus\QueryBusInterface;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/api/users')]
class UserSearchController extends AbstractController
{
    #[Route('/search', name: 'api_users_search', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function search(Request $request, QueryBusInterface $queryBus): JsonResponse
    {
        $q = $request->query->get('q', '');

        if (strlen($q) < 2) {
            return new JsonResponse([], 200);
        }

        $users = $queryBus->ask(new SearchUsersQuery($q));

        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'email' => $user->getEmail(),
            ];
        }, $users);

        return new JsonResponse($data);
    }
}
