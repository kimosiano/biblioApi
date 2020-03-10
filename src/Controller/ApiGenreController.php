<?php

namespace App\Controller;

use App\Entity\Genre;
use App\Repository\GenreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function show(Genre $genre, SerializerInterface $serializer, GenreRepository $repo, $id)
    {
        $genreX=$repo->findById($id);

        //dump($genreX);dump($genre);die;
        $resultat=$serializer->serialize(
            $genreX[0],
            'json',
            [
                'groups'=>['listGenreSimple']  
            ]
            
        );
       
        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }
    /**
     * @Route("/api/genres", name="api_genres_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        //$genre=new Genre();
        //$serializer->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);
        $genre=$serializer->deserialize($data, Genre::class, 'json');
        
        //gestion
        //$validator=Validation::createValidator();
        $errors=$validator->validate($genre);
        //dump(count($errors));die;
        if(count($errors)){
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST,[],true);
        }

        $manager->persist($genre);
        $manager->flush();
        //dump($genreX);dump($genre);die;
       
        return new JsonResponse(
            "le genre a bien été créé",
            Response::HTTP_CREATED,
            ["location"=>"api/genres/".$genre->getId()],
            true
        );
        //["location"=>$this->generateUrl('api_genres_show', ["id"=>$genre->getId(), UrlGeneratorInterface::ABSOLUTE_PATH])]
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_update", methods={"PUT"})
     */
    public function edit(Request $request, SerializerInterface $serializer, GenreRepository $repo, $id, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $genreX=$repo->findById($id);
        $genre = $genreX[0];
        $data = $request->getContent();
        $serializer->deserialize($data, Genre::class, 'json', ['object_to_populate'=>$genre]);
       
       // $validator=Validation::createValidator();
        $errors=$validator->validate($genre);
       
        if(count($errors)){
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST,[],true);
        }

        $manager->persist($genre);
        $manager->flush();
        return new JsonResponse("le genre a bien été modifié",Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/genres/{id}", name="api_genres_delete", methods={"DELETE"})
     */
    public function delete(GenreRepository $repo, $id, EntityManagerInterface $manager)
    {
        $genreX=$repo->findById($id);
        $genre = $genreX[0];
        //dump($genre);die;
        $manager->remove($genre);
        $manager->flush();
        return new JsonResponse("le genre a bien été supprimé",Response::HTTP_OK,[]);
    }
}
