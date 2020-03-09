<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ApiGenreController extends AbstractController
{
    /**
     * @Route("/api/genres", name="api_genres", methods={"GET"})
     */
    public function list(GenreRepository $repo, SerializerInterface $serializer)
    {
        $genres=$repo->findAll();
        $resultat=$serializer->serialize(
            $genres,
            'json',
            [
                'groups'=>['listGenreFull']
            ]
            
        );
        return new JsonResponse($resultat,200,[],true);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_show", methods={"GET"})
     */
    public function show(Genre $genre, SerializerInterface $serializer)
    {
        $resultat=$serializer->serialize(
            $genre,
            'json',
            [
                'groups'=>['listGenreSimple']
            ]
            
        );
       
        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }
}
