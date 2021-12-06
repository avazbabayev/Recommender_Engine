<?php

class Router
{
    /**
     * The current requested URI
     */
    protected $currentUri;
    /**
     * Indication of whether current request is POST or not
     */
    protected $isPostRequest;
    protected $dataController;

    /**
     * Run the router
     */
    public function run()
    {
        $this->loadAll();
        $this->currentUri = $this->getCurrentUri();
        $this->isPostRequest = $_SERVER['REQUEST_METHOD'] == 'POST';
        $this->mapRouteToController();
    }

    /**
     * Get the current URI user has browsed to
     */
    public function getCurrentUri()
    {
        $basepath = implode('/', array_slice(explode('/', $_SERVER['SCRIPT_NAME']), 0, -1)) . '/';
        $uri = substr($_SERVER['REQUEST_URI'], strlen($basepath));
        if (strstr($uri, '?')) $uri = substr($uri, 0, strpos($uri, '?'));
        $uri = '/' . trim($uri, '/');
        return $uri;
    }

    /**
     * Load and register all the required files and setting
     *
     * @return void
     */
    public function loadAll()
    {
        // Load all php files from the controllers directory whose filename end with Controller.php
        require_once 'app/conf/db_conf.php';
        require_once 'app/controllers/DataController.php';
        foreach (glob("app/algorithms/*.php") as $file) {
            require_once $file;
        }
        foreach (glob("app/lib/*.php") as $file) {
            require_once $file;
        }
        ORM::configure('mysql:host=' . $dbhost . ';dbname=' . $dbname);
        ORM::configure('username', $dbuser);
        ORM::configure('password', $dbpass);

        $this->dataController = new DataController();
    }

    /**
     * Run the appropriate method based on the current uri
     * @return void
     */
    public function mapRouteToController()
    {
        $this->handleRequests();
    }


    /**
     * Handle Post Requests
     */
    public function handleRequests()
    {
        switch ($this->currentUri) {
            case '/pearson':
                if ($this->isPostRequest and isset($_POST['songid'])) {
                    $id = $_POST['songid'];
                    $arr = $this->dataController->getSongData();
                    $pearson = new Pearson($arr);
                    $rec = $pearson->recommend($id);
                    echo json_encode($rec);
                }
                die;
            case '/ranking':
                if ($this->isPostRequest and isset($_POST['userid'])) {
                    $id = $_POST['userid'];
                    $arr = $this->dataController->getUserData();
                    $ranking = new Ranking($arr);
                    $rec = $ranking->recommend($id);
                    if (!empty($rec)) {
                        $returnarr = [];
                        $i = 0;
                        foreach ($rec as $key => $value) {
                            $i++;
                            $returnarr[] = $key;
                            if ($i == 3) break;
                        }
                        echo json_encode($returnarr);
                    }
                }
                die;
            case '/qfd':
                $data =  $this->dataController->get_Song_with_Rating();
                $songdata =  $this->dataController->getSongText();
                $qfd = new QFD($data,$songdata);
                $rec = $qfd->recommend();
                echo json_encode($rec);
                die();
        }
    }


}
