<?php

namespace app\controllers;

use app\models\Integration;
use app\models\Uploader;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\Reports;
use app\models\Loader;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionGetCode()
    {
        $model = new Integration();
        $arResult['client_id'] = $model->arSettings['client_id'];

        return $this->render('get-code', [
            'arResult' => $arResult,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Displays reports page.
     *
     * @return string
     */
    public function actionReports()
    {
        $model = new Reports();
        $arResult = $model->getList();

        return $this->render('reports', [
            'arResult' => $arResult,
        ]);
    }

    /**
     * Get actual portal info
     *
     * @return string
     */
    public function actionLoader()
    {
        $model = new Loader();
        $arPost = Yii::$app->request->post();

        if( $arPost['ACTION'] == 'getGroups' ){
            $arGroups = $model->getGroups();

            return json_encode($arGroups);
        }

        if( $arPost['ACTION'] == 'getTasks' ){
            $arTasks = $model->getTasks($arPost['PARAMS']['PAGE']);
            foreach($arTasks['ITEMS'] as $arTask){
                if( empty($arTask['DURATION_FACT']) ){
                    continue;
                }

                $arTasks['TASKS_WITH_DURATIONS'][$arTask['ID']] = $arTask;
            }

            return json_encode($arTasks);
        }
        

        if( $arPost['ACTION'] == 'getTime' && !empty($arPost['PARAMS']['TASK']) ){
            $arUIds = [];
            if( !empty($arPost['PARAMS']['TASK']['DURATION_FACT']) ){
                $arTaskTime = $model->getTime($arPost['PARAMS']['TASK']['ID']);
                foreach($arTaskTime['ITEMS'] as $arTime){
                    $arUIds[$arTime['USER_ID']] = array();
                }
            }

            $arTaskTime['USERS'] = $arUIds;
            $arTaskTime['TASK'] = $arPost['PARAMS']['TASK']['ID'];

            return json_encode($arTaskTime);
        }

        if( !empty($arPost['PARAMS']['USERS']) ) {
            $arUsers = $model->getUsers($arPost['PARAMS']['USERS']);

            return json_encode($arUsers);
        }

        if( empty($arPost['ACTION']) ){
            return $this->render('loader');
        }
    }


    public function actionUploader()
    {
        $model = new Uploader();
        /*$arPost = Yii::$app->request->post();

        $arGroups = $model->addGroups();

        $arTasks = $model->addTasks();*/

        $arTimes = $model->addTime();
        return $this->render('uploader', [

        ]);
    }
}
