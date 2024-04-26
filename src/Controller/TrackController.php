<?php

namespace App\Controller;

use App\Entity\Track;
use App\Repository\TrackRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api/tracks')]
class TrackController extends AbstractController
{
    private EntityManagerInterface $entityManager;
    private TrackRepository $trackRepository;
    private SerializerInterface $serializer;

    public function __construct(EntityManagerInterface $entityManager, TrackRepository $trackRepository, SerializerInterface $serializer)
    {
        $this->entityManager = $entityManager;
        $this->trackRepository = $trackRepository;
        $this->serializer = $serializer;
    }

    #[Route('/', name: 'track_index', methods: ['GET'])]
    public function index(): Response
    {
        $tracks = $this->trackRepository->findAll();
        $data = $this->serializer->serialize($tracks, 'json', ['groups' => 'track_read']);
        return new Response($data, 200, ['Content-Type' => 'application/json']);
    }

    #[Route('/{id}', name: 'track_show', methods: ['GET'])]
    public function show(Track $track): Response
    {
        $data = $this->serializer->serialize($track, 'json', ['groups' => 'track_details']);
        return new Response($data, Response::HTTP_OK, ['Content-Type' => 'application/json']);
    }

    #[Route('/', name: 'track_new', methods: ['POST'])]
    public function new(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);
        $track = new Track();
        $track->setTitle($data['title']);
        $track->setDuration($data['duration']);
        // Set the Album by fetching it from the database
        // $track->setAlbum($someAlbumEntity);

        $this->entityManager->persist($track);
        $this->entityManager->flush();

        return $this->json($track, Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'track_edit', methods: ['PUT'])]
    public function edit(Request $request, Track $track): Response
    {
        $data = json_decode($request->getContent(), true);
        $track->setTitle($data['title']);
        $track->setDuration($data['duration']);
        // Update the Album by fetching it from the database
        // $track->setAlbum($someAlbumEntity);

        $this->entityManager->flush();

        return $this->json($track);
    }

    #[Route('/{id}', name: 'track_delete', methods: ['DELETE'])]
    public function delete(Track $track): Response
    {
        $this->entityManager->remove($track);
        $this->entityManager->flush();

        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
