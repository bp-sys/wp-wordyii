<?php

namespace wordyii\models;

use wordyii\base\WordyiiModel;

class WordyiiExampleModel extends WordyiiModel {

    public static function tableName()
    {
        return 'wp_options';
    }

}