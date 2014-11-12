<?php

use \yii\bootstrap\Modal;
use kartik\social\FacebookPlugin; 
use \yii\bootstrap\Collapse;
use \yii\bootstrap\Alert;
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = 'Yii 2 Template';
?>
<div class="site-index">

    <div class="jumbotron">
        

        
        <h1>Yii 2 Template <i class="fa fa-plug"></i></h1>

        <p class="lead">Use this Yii 2 Template to start Projects.</p>
        <?php echo FacebookPlugin::widget(['type'=>FacebookPlugin::LIKE,'settings' => []]); ?>
        <br />
         <?php if (Yii::$app->user->isGuest) {
            echo Html::a('Signup form', ['site/signup'],
            ['class' => 'btn btn-lg btn-success']);}; 
        ?>
    </div>
    
    <?php
        // Collapse widget
        echo Collapse::widget([
        'items' => 
        [ 
            // first group
            [
                'label' => 'Top Features' ,
                'content' => FacebookPlugin::widget([
                    'type'=>FacebookPlugin::SHARE,
                    'settings' => ['href'=>'http://yii2app','width'=>'350']
                ]),
                // open its content by default
                //'contentOptions' => ['class' => 'in']
            ],
            
            // another group
            [
                'label' => 'Top Resources',
                'content' => FacebookPlugin::widget([
                    'type'=>FacebookPlugin::SHARE,
                    'settings' => ['href'=>'http://yii2app','width'=>'350']
                ]),
                 // 'contentOptions' => [],
                 // 'options' => [],
            ], 
        ] 
        ]);
        
        // Modal widget
        Modal::begin([
               'header' => '<h2>Latest Comments</h2>',
               'toggleButton' => ['label' => 'comments'],
        ]);
            echo FacebookPlugin::widget([ 
                'type'=>FacebookPlugin::COMMENT, 
                'settings' => ['href'=>'http://www.yii2build.com','width'=>'350']
            ]);
        Modal::end(); ?>
        <br /><br />
        <?php
        // Alert widget
        echo Alert::widget([
            'options' => [
                'class' => 'alert-info',
            ],
            'body' => 'Launch your project like a rocket...',
        ]);
        ?>
        <div class="body-content">
            <div class="row">
                <div class="col-lg-4">
                    <h2>Free</h2>
                    <p>
                        <?php
                            if (!Yii::$app->user->isGuest) {
                                echo Yii::$app->user->identity->username . ' is doing cool stuff. ';
                            } 
                        ?>
                        Starting with this free, open source Yii 2 template and it will save you
                        a lot of time.  You can deliver projects to the customer quickly, with
                        a  lot of boilerplate already taken care of for you, so you can concentrate
                        on the complicated stuff.
                    </p>
                    <p>
                        <a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a>
                    </p>
                    
                    <?php
                        echo FacebookPlugin::widget([ 
                            'type'=>FacebookPlugin::LIKE,
                            'settings' => [] ]);
                    ?>
                </div>
                <div class="col-lg-4">
                    <h2>Advantages</h2>
                    <p> 
                        Ease of use is a huge advantage.  
                        We've simplifiled RBAC and given you Free/Paid user type out of the box.  
                        The Social plugins are so quick and easy to install, you will love it! 
                    </p>
                    <p>
                        <a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a>
                    </p>
                    <?php
                        echo FacebookPlugin::widget([ 
                            'type'=>FacebookPlugin::COMMENT, 
                            'settings' => ['href'=>'http://www.yii2build.com','width'=>'350']
                        ]);
                    ?>
                </div>
                <div class="col-lg-4">
                    <h2>Code Quick, Code Right!</h2>
                    <p>
                        Leverage the power of the awesome Yii 2 framework with this enhanced template.
                        Based Yii 2's advanced template, you get a full frontend and backend
                        implementation that features rich UI for backend management.
                    </p>
                    <p>
                        <a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a>
                    </p>
                </div>
            </div>
        </div>
</div>
