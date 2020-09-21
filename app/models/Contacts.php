<?php

namespace MyApp\Models;

use Phalcon\Mvc\Model;
use Phalcon\Messages\Message;
use Phalcon\Validation;
use Phalcon\Validation\Validator\Uniqueness;
use Phalcon\Validation\Validator\InclusionIn;
use \Phalcon\Security\Random;

class Contacts extends Model
{
    public function initialize()
    {
        $random = new \Phalcon\Security\Random();
        $this->id=$random->uuid();
    }

    /**
     * get_all_contacts вывод всех контактов
     * @return array
     */
    static function get_all(){
        $contacts = self::find();
        $data = [];

        foreach ($contacts as $contact) {
            $data[] = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }
        return $data;
    }

    /**
     * @param $id
     * @return array
     */
    static function get_from_id($id){
        $contacts = self::find(
            [
                "id = '{$id}'",
                'limit' => 1,
            ]
        );
        $data = [];

        foreach ($contacts as $contact) {
            $data = [
                'id'   => $contact->id,
                'lastName' => $contact->lastName,
                'firstName' => $contact->firstName,
                'middleName' => $contact->middleName,
            ];
        }
        return $data;
    }
}