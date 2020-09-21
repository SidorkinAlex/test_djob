<?php
/**
 * Created by PhpStorm.
 * User: seedteam
 * Date: 21.09.20
 * Time: 16:24
 */
use Phalcon\Mvc\Controller;

class ApiController extends Controller
{

    public function initialize(){
        $this->view->disable();
    }

    public function contactsAction($id=null)
    {
        // Если GET запросы выводим
        if($this->request->isGet()){
            if(empty($id)){
                // если нет id то выводим весь список
                $this->showAllList();
                return true;
            } else {
            //если указан параметр id
                $this->showFromId($id);
                return true;
            }
        }
    }

    private function showAllList(){
        $contacts = MyApp\Models\Contacts::get_all();
        echo json_encode($contacts);
    }

    private function showFromId($id){
        $contact=MyApp\Models\Contacts::get_from_id($id);
        echo json_encode($contact);
    }


}