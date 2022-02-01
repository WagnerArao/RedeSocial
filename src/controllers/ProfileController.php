<?php
namespace src\controllers;

use \core\Controller;
use \src\handlers\UserHandler;
use \src\handlers\PostHandler;

class ProfileController extends Controller {

    private $loggedUser;

    public function __construct(){
        $this->loggedUser = UserHandler::checkLogin();
        if ($this->loggedUser === false){

            $this->redirect('/login');

        }
        

    }

    public function index($atts = []) {
        $page = intval(filter_input(INPUT_GET, 'page'));

        // Detectando o usuário acessado:
        $id = $this->loggedUser->id;

        if(!empty($atts['id'])){
            $id = $atts['id'];
        }

        //Puxando informações do usuário:
        $user = UserHandler::getUser($id, true);

        if(!$user){
            $this->redirect('/');
        }

        $dateFrom = new \DateTime($user->birthdate);
        $dateTo = new \DateTime('today');
        $user->ageYears = $dateFrom->diff($dateTo)->y;

        //Puxando o feed do usuário:
        $feed = PostHandler::getUserFeed($id, $page, $this->loggedUser->id);

        //Verificar se EU sigo o usuário:
        $isFollowing = false;

        if ($user->id != $this->loggedUser->id){
            $isFollowing = Userhandler::isFollowing($this->loggedUser->id, $user->id);

        }
      
        $this->render('profile', [
            'loggedUser' => $this->loggedUser, 
            'user' => $user,
            'feed'=> $feed,
            'isFollowing' => $isFollowing
        ]);
       
    }

}