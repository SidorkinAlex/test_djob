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
     * Получение списка Контактов метод GET curl --location --request GET 'http://0.0.0.0/api/contacts/'
     * Получение записи Контакта по id метод GET curl --location --request GET 'http://0.0.0.0/api/contacts/cedec9fc-f8a3-11ea-acd6-0242ac110002'
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

    /**
     * Добавление записи метод POST
     * curl --location --request POST 'http://0.0.0.0/api/contactadd' --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'data={"lastName":"Sysykin11","firstName":"Sysyck","middleName":"Sysykovich"}'
     * @return Response
     */
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
            $error = "ERROR method is not POST";
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

    /**
     * Обновление контактов метод PUT
     * curl --location --request PUT 'http://0.0.0.0/api/contact/ef54e12a-f796-11ea-8fca-0242ac110002'  --header 'Content-Type: application/x-www-form-urlencoded' --data-urlencode 'data={"lastName":"Sysykin11","firstName":"Sysyck","middleName":"Sysykovich"}'
     * Удаление данных метод DELETE
     * curl --location --request DELETE 'http://0.0.0.0/api/contact/ef54e12a-f796-11ea-8fca-0242ac110002'
     *
     * @param $id
     * @return Response
     */
    public function contactAction($id)
    {
        // Если PUT запрос Обновляем запись
        $response = new Response();
        if ($this->request->isPut()) {
            $data = json_decode($this->request->getPut('data'), 1);
            $response=$this->contactUpdate($id,$data,$response);
            return $response;
        }

        if($this->request->isDelete()){
            $response=$this->contactDelete($id,$response);
            return $response;
        }
        // Если метод не PUT или DELETE
        if (!isset($error)) {
            $error = "ERROR method is not PUT or DELETE";
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

    /**
     * Обновление данных по id
     * @param $id
     * @param array $data
     * @param Response $response
     * @return Response
     */
    private function contactUpdate($id,$data,Response $response){
        $response->setStatusCode(409, 'Conflict');
        if (!empty($data)) {
            $contact =  MyApp\Models\Contacts::findFirst("id = '{$id}'");
            if(!empty($data['lastName'])) {
                $contact->lastName = $data['lastName'];
            }
            if(!empty($data['firstName'])) {
                $contact->firstName = $data['firstName'];
            }
            if(!empty($data['middleName'])) {
                $contact->middleName = $data['middleName'];
            }
            $result = $contact->save();
            if ($result) {
                $response->setStatusCode(200, 'Updated');

                $response->setJsonContent(
                    [
                        'status' => 'OK',
                        'data' => $contact->id,
                    ]
                );
                return $response;

            } else {
                //Если данные не сохранены
                $response->setJsonContent(
                    [
                        'status' => 'ERROR',
                        'messages' => 'ERROR from Saving',
                    ]);
                return $response;
            }
        }
        // Если пустой массив данных
        $response->setJsonContent(
            [
                'status' => 'ERROR',
                'messages' => 'ERROR data is empty',
            ]);

        return $response;
    }

    /**
     * удаление записи по id
     * @param $id
     * @param Response $response
     * @return Response
     */
    private function contactDelete($id, Response $response)
    {

        $contact = MyApp\Models\Contacts::findFirst("id = '{$id}'");

        $result = $contact->delete();
        if ($result) {
            $response->setStatusCode(200, 'Deleted');

            $response->setJsonContent(
                [
                    'status' => 'OK',
                    'data' => $contact->id,
                ]
            );
            return $response;

        } else {
            //Если данные не далены
            $response->setJsonContent(
                [
                    'status' => 'ERROR',
                    'messages' => 'ERROR from Deleting',
                ]);
            return $response;
        }
    }

}