<?php
namespace frontend\controllers;

use Yii;
use common\models\LoginForm;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\SignupForm;
use frontend\models\ContactForm;
use yii\base\InvalidParamException;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\filters\AccessControl;

/**
 * Site controller
 */
class SiteController extends Controller
{
    /**
     * Method for controlling access to the site
     * The ‘?’ is guest and ‘@’ is logged in
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(), // what class to apply
                'only' => ['logout', 'signup'], // rules below apply only to logout and signup actions
                'rules' => [
                    [
                        'actions' => ['signup'],
                        'allow' => true,
                        'roles' => ['?'], // guests are allowed to signup (access the signup action)
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'], // logged in users are allowed to access the logout action
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
     * The configuration in actions makes this configuration available to each action.
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction', // which class to use for error
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction', // which class to use for captcha
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Default action, if you type in the domain, that is the route you'll get
     * The route looks like this: /index.php?r=site/index
     */
    public function actionIndex()
    {
        return $this->render('index'); 
    }

    public function actionLogin()
    {
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome(); // if not a guest, you are already logged in and you go to the home page
        }
        
        // if guest ...
        $model = new LoginForm(); // create a new instance of LoginForm model
        if ($model->load(Yii::$app->request->post()) && $model->login()) { //If we can load the post data, which will validate according to the model and if it can utilize the model’s login method, it will return the user to whatever page they were on using:
            return $this->goBack(); // Only now they will be in a logged in state.
        } else { // Otherwise, if something fails or we have not yet posted the form, we will display the form
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail(Yii::$app->params['adminEmail'])) {
                Yii::$app->session->setFlash('success', 'Thank you for contacting us. We will respond to you as soon as possible.');
            } else {
                Yii::$app->session->setFlash('error', 'There was an error sending email.');
            }

            return $this->refresh();
        } else {
            return $this->render('contact', [
                'model' => $model,
            ]);
        }
    }

    public function actionAbout()
    {
        return $this->render('about');
    }

    public function actionSignup()
    {
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) { //call the signup method of SignupForm
                if (Yii::$app->getUser()->login($user)) {
                    return $this->goHome();
                }
            }
        }
        // otherwise show signup form
        return $this->render('signup', [
            'model' => $model,
        ]);
    }

    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->getSession()->setFlash('success', 'Check your email for further instructions.');

                return $this->goHome();
            } else {
                Yii::$app->getSession()->setFlash('error', 'Sorry, we are unable to reset password for email provided.');
            }
        }

        return $this->render('requestPasswordResetToken', [
            'model' => $model,
        ]);
    }

    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidParamException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->getSession()->setFlash('success', 'New password was saved.');

            return $this->goHome();
        }

        return $this->render('resetPassword', [
            'model' => $model,
        ]);
    }
}
