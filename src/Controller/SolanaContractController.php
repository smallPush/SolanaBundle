<?php

namespace App\Controller;

use App\DTO\SolanaContractSummary;
use App\Entity\SolanaContract;
use App\Form\SolanaContractType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/solana/contract')]
#[IsGranted('ROLE_USER')]
class SolanaContractController extends AbstractController
{
    #[Route('/', name: 'app_solana_contract_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $contracts = $entityManager
            ->getRepository(SolanaContract::class)
            ->createQueryBuilder('s')
            ->select(sprintf('NEW %s(s.id, s.title, s.status)', SolanaContractSummary::class))
            ->where('s.author = :user OR s.donor = :user OR s.volunteer = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();

        return $this->render('solana_contract/index.html.twig', [
            'contracts' => $contracts,
        ]);
    }

    #[Route('/new', name: 'app_solana_contract_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contract = new SolanaContract();
        $form = $this->createForm(SolanaContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $contract->setAuthor($this->getUser());
            $contract->setStatus('pending'); // Set default status

            $entityManager->persist($contract);
            $entityManager->flush();

            $this->addFlash('success', 'Nuevo contrato creado con éxito.');

            return $this->redirectToRoute('app_solana_contract_index');
        }

        return $this->render('solana_contract/new.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_solana_contract_show', methods: ['GET'])]
    #[IsGranted('VIEW', subject: 'contract')]
    public function show(SolanaContract $contract): Response
    {
        return $this->render('solana_contract/show.html.twig', [
            'contract' => $contract,
        ]);
    }

    #[Route('/{id}/validate', name: 'app_solana_contract_validate', methods: ['POST'])]
    #[IsGranted('VALIDATE', subject: 'contract')]
    public function validate(SolanaContract $contract, EntityManagerInterface $entityManager, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('validate' . $contract->getId(), $request->request->get('_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        $user = $this->getUser();
        $status = $contract->getStatus();
        $isDonor = ($user === $contract->getDonor());
        $isVolunteer = ($user === $contract->getVolunteer());

        $newStatus = null;

        if ($isDonor && $status === 'pending') {
            $newStatus = 'validated_donor';
        } elseif ($isVolunteer && $status === 'pending') {
            $newStatus = 'validated_volunteer';
        } elseif ($isDonor && $status === 'validated_volunteer') {
            $newStatus = 'ready_for_signature';
        } elseif ($isVolunteer && $status === 'validated_donor') {
            $newStatus = 'ready_for_signature';
        }

        if ($newStatus) {
            $contract->setStatus($newStatus);
            $entityManager->flush();
            $this->addFlash('success', 'Contrato validado. Nuevo estado: ' . $newStatus);
        } else {
            $this->addFlash('warning', 'No se ha podido validar el contrato en este estado o no tienes permisos.');
        }

        return $this->redirectToRoute('app_solana_contract_show', ['id' => $contract->getId()]);
    }
}
