<?php

namespace App\Controller;

use App\Application\Command\CreateSolanaContractCommand;
use App\Application\Command\ValidateSolanaContractCommand;
use App\Application\Query\GetSolanaContractByIdQuery;
use App\Application\Query\GetSolanaContractsByUserQuery;
use App\Contract\Bus\CommandBusInterface;
use App\Contract\Bus\QueryBusInterface;
use App\Entity\SolanaContract;
use App\Form\SolanaContractType;
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
    private CommandBusInterface $commandBus;
    private QueryBusInterface $queryBus;

    public function __construct(CommandBusInterface $commandBus, QueryBusInterface $queryBus)
    {
        $this->commandBus = $commandBus;
        $this->queryBus = $queryBus;
    }

    #[Route('/', name: 'app_solana_contract_index', methods: ['GET'])]
    public function index(): Response
    {
        $contracts = $this->queryBus->ask(new GetSolanaContractsByUserQuery($this->getUser()));

        return $this->render('solana_contract/index.html.twig', [
            'contracts' => $contracts,
        ]);
    }

    #[Route('/new', name: 'app_solana_contract_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request): Response
    {
        $contract = new SolanaContract();
        $form = $this->createForm(SolanaContractType::class, $contract);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->commandBus->dispatch(new CreateSolanaContractCommand($contract, $this->getUser()));

            $this->addFlash('success', 'Nuevo contrato creado con éxito.');

            return $this->redirectToRoute('app_solana_contract_index');
        }

        return $this->render('solana_contract/new.html.twig', [
            'contract' => $contract,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_solana_contract_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $contract = $this->queryBus->ask(new GetSolanaContractByIdQuery($id));

        if (!$contract) {
            throw $this->createNotFoundException('Contrato no encontrado.');
        }

        $this->denyAccessUnlessGranted('VIEW', $contract);

        return $this->render('solana_contract/show.html.twig', [
            'contract' => $contract,
            'solana_network' => $_ENV['SOLANA_NETWORK'] ?? 'devnet',
        ]);
    }

    #[Route('/{id}/validate', name: 'app_solana_contract_validate', methods: ['POST'])]
    #[IsGranted('VALIDATE', subject: 'contract')]
    public function validate(SolanaContract $contract, Request $request, CsrfTokenManagerInterface $csrfTokenManager): Response
    {
        $token = new CsrfToken('validate' . $contract->getId(), $request->request->get('_token'));
        if (!$csrfTokenManager->isTokenValid($token)) {
            throw $this->createAccessDeniedException('Token CSRF inválido.');
        }

        try {
            $this->commandBus->dispatch(new ValidateSolanaContractCommand($contract, $this->getUser()));

            // Re-fetch status or just trust the entity is updated
            $newStatus = $contract->getStatus();
            $this->addFlash('success', 'Contrato validado. Nuevo estado: ' . $newStatus);
        } catch (\DomainException $e) {
            $this->addFlash('warning', 'No se ha podido validar el contrato en este estado o no tienes permisos.');
        }

        return $this->redirectToRoute('app_solana_contract_show', ['id' => $contract->getId()]);
    }
}
