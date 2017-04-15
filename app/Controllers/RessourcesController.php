<?php

namespace App\Controllers;

use App\Config\DbConnect;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class RessourcesController
{

    private $container;

    public function __construct($container)
    {
        $this->container = $container;
    }

    public function index(RequestInterface $request, ResponseInterface $response)
    {
        $dbconnect = new DbConnect();
        $db = $dbconnect->getConnection();
        $sth = $db->prepare( 'SELECT * FROM Categories;' );
        $sth->execute();
        $result = $sth->fetchAll();
        $this->container->view->render($response, 'pages/ressources.twig', ["categories" => $result]);
    }



    public function store(RequestInterface $request, ResponseInterface $response)
    {
        $dbconnect = new DbConnect();
        $sql = "INSERT INTO Ressources (link, description, Categories_id, name) VALUES (:link, :description, :Categories_id, :nom)";
        //echo $sql;die();
        try {
            $db = $dbconnect->getConnection();
            $stmt = $db->prepare($sql);

            $stmt->bindParam("link", $_POST['link']);
            $stmt->bindParam("description", $_POST['description']);
            $stmt->bindParam("Categories_id", $_POST['Categories_id']);
            $stmt->bindParam("nom", $_POST['name']);

            $stmt->execute();
            //return $response->withRedirect('/rest-of-web');
            //$ressource->id = $db->lastInsertId();
            //$db = null;
            //echo "succes";
        } catch(PDOException $e) {
            echo '{"error":{"text":'. $e->getMessage() .'}}';
        }

    }

    public function getRessourcesListOfCategory(RequestInterface $request, ResponseInterface $response){
        $route = $request->getAttribute('route');
        $category_id = $route->getArgument('id');
        $category_name = $route->getArgument('name');

        $dbconnect = new DbConnect();
        $db = $dbconnect->getConnection();
        $sth = $db->prepare( 'SELECT * FROM Ressources WHERE Categories_id = '.$category_id);
        $sth->execute();
        $result = $sth->fetchAll();

        /* categorie */
        $cat = $db->prepare( 'SELECT * FROM Categories;' );
        $cat->execute();
        $category = $cat->fetchAll();

        $this->container->view->render($response, 'pages/list.twig', ["ressources" => $result, "name" => $category_name, "categories" => $category]);

    }

    public function getRessourceDetail(RequestInterface $request, ResponseInterface $response){

        $route = $request->getAttribute('route');
        $resource_id = $route->getArgument('id');

        $dbconnect = new DbConnect();
        $db = $dbconnect->getConnection();
        $sql = $db->prepare('SELECT * FORM Ressources WHERE id = '.$resource_id);
        $sql->execute();
        $result = $sql->fetchAll();
        $this->container->view->render($response, 'partial/modal_detail.twig', ["ressources" => $result]);

    }
}