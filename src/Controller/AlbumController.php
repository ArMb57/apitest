<?php

namespace App\Controller;

use App\Entity\Album;
use App\Repository\AlbumRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/albums')]
class AlbumController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private AlbumRepository $albumRepository;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, AlbumRepository $albumRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->albumRepository = $albumRepository;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'album_index', methods: ['GET'])]
    public function index(): Response
    {
        $albums = $this->albumRepository->findAll();
        $data = $this->serializer->serialize($albums, 'json', ['groups' => 'album_read']);
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'album_show', methods: ['GET'])]
    public function show(Album $album): Response
    {
        $data = $this->serializer->serialize($album, 'json', ['groups' => 'album_details']);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/', name: 'album_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $album = new Album();
        $album->setName($data['name']);
        $album->setYear($data['year']);
        // Ici, vous devrez gérer la relation avec l'artiste si nécessaire

        $this->entityManager->persist($album);
        $this->entityManager->flush();

        return $this->json($album, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'album_edit', methods: ['PUT'])]
    public function edit(Request $request, Album $album): Response
    {
        $data = json_decode($request->getContent(), true);
        $album->setName($data['name']);
        $album->setYear($data['year']);
        // Ici, vous devrez gérer la relation avec l'artiste si nécessaire

        $this->entityManager->flush();

        return $this->json($album);
    }

    #[Route('/{id}', name: 'album_delete', methods: ['DELETE'])]
    public function delete(Album $album): Response
    {
        $this->entityManager->remove($album);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
