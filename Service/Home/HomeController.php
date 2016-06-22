<?php
namespace Service\Home;

class HomeController extends Controller {
    function indexAction() {
        
        $this->show(array(
            'title' => '首页'
        ));
    }
}