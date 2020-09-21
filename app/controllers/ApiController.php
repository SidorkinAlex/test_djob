<?php
/**
 * Created by PhpStorm.
 * User: seedteam
 * Date: 21.09.20
 * Time: 16:24
 */
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ApiController extends Controller
{

    public function initialize(){
        $this->view->disable();
    }

    /**
     * @param null $id
     * @return bool
     */
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

    public function contactaddAction()
    {
        // Если POST запрос сохраняем запись
        $response = new Response();
        if ($this->request->isPost()) {
            $data = json_decode($this->request->getPost('data'), 1);
            if (!empty($data)) {
                $contact = new MyApp\Models\Contacts();
                $contact->lastName = $data['lastName'];
                $contact->firstName = $data['firstName'];
                $contact->middleName = $data['middleName'];
                $result = $contact->save();
                if ($result) {
                    $response->setStatusCode(201, 'Created');

                    $response->setJsonContent(
                        [
                            'status' => 'OK',
                            'data' => $contact->id,
                        ]
                    );
                    return $response;

                } else {
                    //Если данные не сохранены
                    $error = "ERROR from Saving";
                }
            }
            // Если пустой массив данных
            if (!isset($error)) {
                $error = "ERROR data is empty";
            }
        }
        // Если метод не POST
        if (!isset($error)) {
            $error = "ERROR data is empty";
        }
        $response->setStatusCode(409, 'Conflict');
        $response->setJsonContent(
            [
                'status' => 'ERROR',
                'messages' => $error,
            ]
        );
        return $response;
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