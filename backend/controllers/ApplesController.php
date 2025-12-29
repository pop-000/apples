<?php

namespace backend\controllers;

use Yii;
use backend\models\Apple;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * ApplesController implements the CRUD actions for Apple model.
 */
class ApplesController extends Controller {

    /**
     * Lists all Apple models.
     *
     * @return string
     */
    public function actionIndex()
    {
        Apple::fillTree();

        $onTree = Apple::find()
            ->where(['status' => Apple::STATUS_TREE])
            ->all();
        
        $onGround = Apple::find()
            ->where(['status' => [Apple::STATUS_GROUND, Apple::STATUS_BAD]])
            ->all();

        $session = Yii::$app->session;
        $start = $session->get("start") ?? time();
        $now = $session->get("now") ?? $start;    
        $diff = $now - $start;
        $day = floor($diff / 60 * 60 * 24);
        $hour = floor($diff / 60 * 60);

        return $this->render('index', [
            'onTree' => $onTree,
            'onGround' => $onGround,
            'now' => $now,
            'hour' => $hour,
            'day' => $day,
        ]);
    }

    /**
     * Очищает таблицу и
     */
    public function actionReload(){
        Yii::$app->db->createCommand()->truncateTable(Apple::tableName())->execute();
        return $this->redirect(['index']);
    }

    /**
     * Creates a new Apple model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return string|\yii\web\Response
     */
    public function actionCreate()
    {
        $model = new Apple();

        if ($this->request->isPost) {
            if ($model->load($this->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }
        } else {
            $model->loadDefaultValues();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Apple model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param int $id ID
     * @return \yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->setDeleted()) {
            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Apple model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param int $id ID
     * @return Apple the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Apple::findOne(['id' => $id])) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    /**
     * Следующий временной шаг.
     * @return Response
     */
    public function nextTime() {
        $session = Yii::$app->session;
        $now = $session->get('now') ?? time(); 
        $next = $now + Apple::HOURS_INCREMENT * 60 * 60;
        $session->set('now', $next);
        return $this->redirect(['index']);
    }
    
    /**
     * Следующий день.
     * @return Response
     */
    public function nextDay() {
        $session = Yii::$app->session;
        $now = $session->get('now') ?? time(); 
        $tomorrow = strtotime('+' . Apple::DAY_INCREMENT . ' days', $now);
        $session->set('now', $tomorrow);
        return $this->redirect(['index']);
    }

    /**
     * Поедание яблока
     * @param int $id ID
     * @return \yii\web\Response
     */
    public function actionEat($id) {
        $model = $this->findModel($id);
        $model->eat();
        return $this->renderPartial("_apple", ['model' => $model]);
    }
}
