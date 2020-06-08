<?php

namespace wordyii\controllers;

use wordyii\base\Controller;
use wordyii\base\View;

class ExampleController extends Controller
{

    public function behaviors()
    {
        return [
            'acess' => [
                'class' => [
                    'value' => 'acess',
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

