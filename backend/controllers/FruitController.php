<?php

namespace app\controllers;

use Yii;

use yii\data\Pagination;
use yii\web\Response;
use app\models\Fruit;

class FruitController extends \yii\rest\Controller
{    
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
        ];
    }

    public function actions()
    {
        $actions = parent::actions();
        unset($actions['index']);
        unset($actions['view']);
        
        return $actions;
    }
    
    public function actionIndex($name = null, $family = null, $favorite = null, $page = null, $size = null)
    {
        $query = Fruit::find();
        if (!empty($name)) {
            $query->andWhere(['like', 'name', $name]);
        }
        
        if (!empty($family)) {
            $query->andWhere(['like', 'family', $family]);
        }
        
        if (!empty($favorite)) {
            $query->andWhere(['like', 'b_favorite', $favorite]);
        }
        
        $countQuery = clone $query;
        $pages = new Pagination(['totalCount' => $countQuery->count()]);

        if (!empty($page)) {
            $pages->setPage($page);
        }
        
        if (!empty($size)) {
            $pages->setPageSize($size);
        }

        $models = $query->all();
        

        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;
        $response->data = [
            'rows' => $models,
            'pages' => $pages
        ];

        return $response;
    }

    
    public function actionFavorite($fruit = null)
    {
        $response = Yii::$app->getResponse();
        $response->format = Response::FORMAT_JSON;

        $record = Fruit::findOne($fruit);
        
        $favoritesCnt = Fruit::find()->where(['b_favorite' => 1])->count();
        if ($favoritesCnt >= 10 && !$record->b_favorite) {
            $response->data = [
                'result' => false,
                'msg' => 'You can only add up to 10 favorites.'
            ];
            return $response;
        }
        $record->b_favorite = !$record->b_favorite;
        $response->data = [
            'result' => $record->save(),
        ];

        return $response;
    }
}
