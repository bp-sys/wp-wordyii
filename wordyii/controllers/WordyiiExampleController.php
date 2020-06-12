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
                        'roles' => ['administrator'],
                    ],[
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['gabriel'],
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

