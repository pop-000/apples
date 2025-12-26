<?php

namespace backend\controllers;

use Yii;
use backend\models\Apple;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

/**
 * AppleController implements the CRUD actions for Apple model.
 */
class AppleController extends Controller {

    /**
     * Lists all Apple models.
     *
     * @return string
     */
    public function actionIndex()
    {
        if (Apple::isTreeEmpty()) {
            Apple::cultivate();
        } 
        Apple::checkReady();
        Apple::checkIsBad();
        Apple::checkSize();

        $onTree = Apple::find()
            ->where(['status' => Apple::STATUS_TREE])
            ->all();
        
        $onGround = Apple::find()
            ->where(['status' => Apple::STATUS_GROUND])
            ->all();

        return $this->render('index', [
            'onTree' => $onTree,
            'onGround' => $onGround,
        ]);
    }

    /**
     * Displays a single Apple model.
     * @param int $id ID
     * @return string
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
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
     * Updates an existing Apple model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param int $id ID
     * @return string|\yii\web\Response
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($this->request->isPost && $model->load($this->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
     * Выращивание яблок.
     * @return Response
     */
    public function actionCultivate() {
        Apple::cultivate();
        return $this->redirect(['index']);
    }

    /**
     * Следующий день.
     * @return Response
     */
    public function nextDay() {
        $session = Yii::$app->session();
        $session->day += 1;
        return $this->redirect(['index']);
    }
}
