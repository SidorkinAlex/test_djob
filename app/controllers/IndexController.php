<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    /**
     * страница просмотра для работы с контактами через браузер
     */
    public function indexAction()
    {
        $contacts = MyApp\Models\Contacts::find();
        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }

        $this->view->setVar('data',$data);
    }

    /**
     * Поиск на странице контактов
     * @param string $contactSerch фраза для полнотекстного поиска
     */
    public function serchAction($contactSerch=''){
        $contactSerch=urldecode ($contactSerch );
        global $app;
        $contacts = MyApp\Models\Contacts::find(
            [
                "id LIKE '%{$contactSerch}%' OR firstName LIKE '%{$contactSerch}%' OR middleName LIKE '%{$contactSerch}%' OR lastName LIKE '%{$contactSerch}%'",
                'order' => 'lastName',
                'limit' => 100,
            ]
        );
        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }
        $this->view->setVar('data',$data);
    }
}
