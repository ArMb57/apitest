<?php

namespace App\Controller;

use App\Entity\Artist;
use App\Repository\ArtistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/artists')]
class ArtistController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private ArtistRepository $artistRepository;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, ArtistRepository $artistRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->artistRepository = $artistRepository;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'artist_index', methods: ['GET'])]
    public function index(): Response
    {
        $artists = $this->artistRepository->findAll();
        $data = $this->serializer->serialize($artists, 'json', ['groups' => 'artist_read']);
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'artist_show', methods: ['GET'])]
    public function show(Artist $artist): Response
    {
        $data = $this->serializer->serialize($artist, 'json', ['groups' => 'artist_details']);
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/', name: 'artist_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $artist = new Artist();
        $artist->setName($data['name']);

        $this->entityManager->persist($artist);
        $this->entityManager->flush();

        return $this->json($artist, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'artist_edit', methods: ['PUT'])]
    public function edit(Request $request, Artist $artist): Response
    {
        $data = json_decode($request->getContent(), true);
        $artist->setName($data['name']);
        $this->entityManager->flush();

        return $this->json($artist);
    }

    #[Route('/{id}', name: 'artist_delete', methods: ['DELETE'])]
    public function delete(Request $request, Artist $artist): Response
    {
        $this->entityManager->remove($artist);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }

}
