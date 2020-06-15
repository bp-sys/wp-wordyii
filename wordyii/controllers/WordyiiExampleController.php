<?php

namespace wordyii\controllers;

use wordyii\base\WordyiiController;
use wordyii\base\WordyiiView;

class WordyiiExampleController extends WordyiiController
{

    public function behaviors()
    {
        return [
        
            'access' => [
                'class' => [
                    'path' => '\wordyii\behaviors',
                    'value' => '\wordyiiaccessbehavior',
                ],
                'rules' => [
                    [
                        'allow' => true,
                        'actions' => ['login'],
                        'roles' => ['administrator'],
                    ],[
                        'allow' => true,
                        'actions' => ['login'],
                    ],[
                        'allow' => false,
                    ]
                ]
            ]
        ];
    }

    public function actionIndex()
    {
        $this->render('index');
    }

}

