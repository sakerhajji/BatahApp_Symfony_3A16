<?php

namespace App\Controller\serviceControllers;

use App\Entity\Forum;
use App\Form\ForumType;
use App\Repository\ForumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/forum')]
class ForumController extends AbstractController
{
    private $session;
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }
    #[Route('/', name: 'app_forum_index', methods: ['GET'])]
    public function index(ForumRepository $forumRepository, PaginatorInterface $paginator, Request $request): Response
    {
        // Retrieve all forums from the repository
        $query = $forumRepository->createQueryBuilder('f')
            ->getQuery();

        // Paginate the query results
        $forums = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // Get the page parameter from the URL, default to 1 if not set
            3      // Items per page
        );

        // Retrieve best time to buy data
        $bestTimeToBuy = $forumRepository->findBestTimeToBuy();

        // Render the template with the paginated forums and other data
        return $this->render('forum/index.html.twig', [
            'forums' => $forums,
            'lowestPrice' => $bestTimeToBuy['lowest_price'],
            'bestTimeToBuy' => $bestTimeToBuy['best_time_to_buy'],
            'user'=>$this->session->get('user'),
        ]);
    }


    #[Route('/new', name: 'app_forum_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager,FlashyNotifier $flashy): Response
    {
        $forum = new Forum();
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $forum->setCreatedAt(new \DateTime());
            $entityManager->persist($forum);
            $entityManager->flush();
            $flashy->success(' thank you for sharing your feedback!', 'http://your-awesome-link.com');

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum/new.html.twig', [
            'forum' => $forum,
            'form' => $form,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{id}', name: 'app_forum_show', methods: ['GET'])]
    public function show(Forum $forum): Response
    {
        return $this->render('forum/show.html.twig', [
            'forum' => $forum,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_forum_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Forum $forum, EntityManagerInterface $entityManager,FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(ForumType::class, $forum);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $flashy->success(' thank you for sharing your feedback!', 'http://your-awesome-link.com');

            return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('forum/edit.html.twig', [
            'forum' => $forum,
            'form' => $form,
            'user'=>$this->session->get('user'),
        ]);
    }

    #[Route('/{id}', name: 'app_forum_delete', methods: ['POST'])]
    public function delete(Request $request, Forum $forum, EntityManagerInterface $entityManager,FlashyNotifier $flashy): Response
    {
        if ($this->isCsrfTokenValid('delete'.$forum->getId(), $request->request->get('_token'))) {
            $entityManager->remove($forum);
            $entityManager->flush();
        }
        $flashy->error('  forum deleted succesfully!', 'http://your-awesome-link.com');

        return $this->redirectToRoute('app_forum_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/search/aaa', name: 'app_forum_search', methods: ['GET'])]
    public function search(Request $request, ForumRepository $forumRepository,FlashyNotifier $flashy): Response
    {
        $keyword = $request->query->get('keyword');

        if (!$keyword) {
            return $this->redirectToRoute('app_forum_index');
        }

        $forums = $forumRepository->search($keyword);
        $flashy->message(' search result are ready!', 'http://your-awesome-link.com');

        return $this->render('forum/search.html.twig', [
            'forums' => $forums,
            'user'=>$this->session->get('user'),
        ]);
    }
    #[Route('/ffffff/best-prices', name: 'app_forum_best_prices', methods: ['GET'])]
    public function bestPrices(ForumRepository $forumRepository): Response
    {
        $bestPrices = $forumRepository->findBestPrices(3); // Fetch best prices data

        $prices = [];
        $dates = [];

        foreach ($bestPrices as $priceData) {
            $prices[] = $priceData['prix'];
            $dates[] = $priceData['createdAt']->format('Y-m-d'); // Format the date as needed
        }

        return $this->render('forum/best_prices.html.twig', [
            'prices' => $prices,
            'dates' => $dates,
            'user'=>$this->session->get('user'),
        ]);
    }
    #[Route('/latest/latest', name: 'app_forum_latest', methods: ['GET'])]
    public function latestForums(ForumRepository $forumRepository): Response
    {
        $latestForums = $forumRepository->findLatestForums();

        return $this->render('forum/latest.html.twig', [
            'latestForums' => $latestForums,
            'user'=>$this->session->get('user'),
        ]);
    }


    #[Route('/best-prices', name: 'best_prices', methods: ['GET'])]
    public function bestPricesnotif(FlashyNotifier $flashy, ForumRepository $forumRepository): JsonResponse
    {
        $bestPrices = $forumRepository->bestPrices(1); // Assuming 3 is the number of best prices you want to fetch

        $message = "Best Prices: ";
        foreach ($bestPrices as $priceData) {
            $message .= "$" . $priceData['prix'] . " on " . $priceData['createdAt']->format('Y-m-d') . ", ";
        }
        $message = rtrim($message, ', ');
        $flashy->info($message);

        return new JsonResponse($bestPrices);
    }

}
