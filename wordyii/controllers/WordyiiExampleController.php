<?php

namespace wordyii\controllers;

use wordyii\base\WordyiiController;
use wordyii\base\WordyiiView;

class WordyiiExampleController extends WordyiiController
{

    public function behaviors()
    {
        return [
            'acess' => [
                'class' => [
                    'value' => 'wordyiiaccess',
                    'path' => '\wordyii\behaviors\\',
                ],
                'allow' => true,
                'roles' => ['admin'],
            ]
        ];
    }

    public function actionIndex()
    {
        $this->render('index');
    }

}

