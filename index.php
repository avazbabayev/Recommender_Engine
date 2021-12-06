<?php
    require_once 'app/Router.php';
    error_reporting(0);
    class App
    {
        protected $app_config;

        /**
         * Run the app
         */

        public function run() {
            try {
                $router = new Router;
                $router->run();
            } catch (Exception $exception) {
                var_dump($exception->getMessage());
            }
        }

    }
    $app = new App();
    $app->run();