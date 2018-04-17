<?php

use yii\helpers\Inflector;
use yii\helpers\StringHelper;


/**
 * @var yii\web\View $this
 * @var yii\gii\generators\crud\Generator $generator
 */

$urlParams = $generator->generateUrlParams();
$nameAttribute = $generator->getNameAttribute();

echo "<?php\n";


?>

use yii\helpers\Html;
use <?= $generator->indexWidgetType === 'grid' ? "kartik\\grid\\GridView" : "yii\\widgets\\ListView" ?>;
use yii\widgets\Pjax;

/**
 * @var yii\web\View $this
 * @var yii\data\ActiveDataProvider $dataProvider
<?= !empty($generator->searchModelClass) ? " * @var " . ltrim($generator->searchModelClass, '\\') . " \$searchModel\n" : '' ?>
 */

$this->title = <?= $generator->generateString(Inflector::pluralize(Inflector::camel2words(StringHelper::basename($generator->modelClass)))) ?>;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="<?= Inflector::camel2id(StringHelper::basename($generator->modelClass)) ?>-index">
    <div class="m-portlet m-portlet--responsive-mobile m-portlet--brand m-portlet--head-solid-bg m-portlet--bordered">
        <div class="m-portlet__head">
            <div class="m-portlet__head-caption">
                <div class="m-portlet__head-title">
                    <h3 class="m-portlet__head-text">
                        <i class="flaticon-users"></i> <?= "<?= " ?>Html::encode($this->title) ?>
                    </h3>
                </div>
            </div>
            <!--<div class="m-portlet__head-tools">
                <ul class="m-portlet__nav">
                    <li class="m-portlet__nav-item">
                        <div class="m-dropdown m-dropdown--inline m-dropdown--arrow m-dropdown--align-right m-dropdown--align-push" data-dropdown-toggle="hover" aria-expanded="true">
                            <a href="#" class="m-portlet__nav-link m-portlet__nav-link--icon m-portlet__nav-link--icon-xl m-dropdown__toggle">
                                <i class="la la-plus m--hide"></i>
                                <i class="la la-ellipsis-h m--font-brand"></i>
                            </a>
                            <div class="m-dropdown__wrapper">
                                <span class="m-dropdown__arrow m-dropdown__arrow--right m-dropdown__arrow--adjust"></span>
                                <div class="m-dropdown__inner">
                                    <div class="m-dropdown__body">
                                        <div class="m-dropdown__content">
                                            <ul class="m-nav">
                                                <li class="m-nav__section m-nav__section--first">
                                                        <span class="m-nav__section-text">
                                                            Azioni
                                                        </span>
                                                </li>
                                                <li class="m-nav__item">
                                                    <a href="#" class="m-nav__link export-xls"  data-toggle="modal" data-target="#data-import">
                                                        <i class="m-nav__link-icon fa fa-file-excel-o"></i>
                                                        <span class="m-nav__link-text">
                                                            Importa nuovo file
                                                        </span>
                                                    </a>
                                                </li>

                                                <li class="m-nav__separator m-nav__separator--fit m--hide"></li>
                                                <li class="m-nav__item m--hide">
                                                    <a href="" class="btn btn-outline-danger m-btn m-btn--pill m-btn--wide btn-sm">
                                                        Submit
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>-->
        </div>
        <div class="m-portlet__body">
            <?= "<? echo " ?>GridView::widget([
                'id' => 'grid<?= ucFirst(Inflector::camel2id(StringHelper::basename($generator->modelClass))) ?>',
                'pjax'=>true,
                'pjaxSettings' =>[
                    'neverTimeout'=>true,
                    'options'=>[
                        'id'=>'grid<?= ucFirst(Inflector::camel2id(StringHelper::basename($generator->modelClass))) ?>-pjax',
                    ]
                ],
                'dataProvider' => $dataProvider,
                <?= !empty($generator->searchModelClass) ? "'filterModel' => \$searchModel,\n                'columns' => [\n" : "'columns' => [\n"; ?>
                     ['class' => 'yii\grid\SerialColumn'],
        <?php
        $count = 0;
        if (($tableSchema = $generator->getTableSchema()) === false) {
            foreach ($generator->getColumnNames() as $name) {
                if (++$count < 6) {
                    echo "          '" . $name . "',\n";
                } else {
                    echo "          // '" . $name . "',\n";
                }
            }
        } else {
            foreach ($tableSchema->columns as $column) {
                $format = $generator->generateColumnFormat($column);
                if ($column->type === 'date') {
                    $columnDisplay = "                     ['attribute' => '$column->name','options'=>['class'=>'m-input'],'format' => ['date',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['date'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['date'] : 'd-m-Y']],";
                } elseif ($column->type === 'time') {
                    $columnDisplay = "                     ['attribute' => '$column->name','options'=>['class'=>'m-input'],'format' => ['time',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['time'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['time'] : 'H:i:s A']],";
                } elseif ($column->type === 'datetime' || $column->type === 'timestamp') {
                    $columnDisplay = "                     ['attribute' => '$column->name','options'=>['class'=>'m-input'],'format' => ['datetime',(isset(Yii::\$app->modules['datecontrol']['displaySettings']['datetime'])) ? Yii::\$app->modules['datecontrol']['displaySettings']['datetime'] : 'd-m-Y H:i:s A']],";
                } else {
                    $columnDisplay = "                     ['attribute' => '$column->name','options'=>['class'=>'m-input']".  ($format === 'text' ? "" : "'format'=>'" . $format."'") . "],";
                }
                if (++$count < 6) {
                    echo $columnDisplay ."\n";
                } else {
                    echo "//" . $columnDisplay . " \n";
                }
            }
        }
        ?>
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{view} {delete}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<i class="fa fa-eye"></i>',
                                    Yii::$app->urlManager->createUrl(['<?= (empty($generator->moduleID) ? '' : $generator->moduleID . '/') . $generator->controllerID?>/view', <?= $urlParams ?>, 'edit' => 't']),
                                    ['title' => Yii::t('yii', 'Modifica'),'data-toggle'=>'m-tooltip']
                                );
                            },
                            'delete' => function($url, $model){
                                return Html::a('<i class="fa fa-trash-o"></i>', '#', [
                                    'class'=>['delete-button'],
                                    'data' => [
                                        'id' => $model->id,
                                        'toggle'=>'m-tooltip',
                                        'title' => Yii::t('yii', 'Elimina')
                                    ],
                                ]);
                            }
                        ],
                    ],
                ],
                'responsive' => true,
                'perfectScrollbar' => false,
                'hover' => true,
                'condensed' => false,
                'floatHeader' => false,
                'bordered' => true,
                'striped'=>false,
                'persistResize'=>false,
                'tableOptions' => ['class'=>'m-datatable__table'],
                'headerRowOptions' => ['class'=>'m-datatable__row'],
                'rowOptions' => ['class'=>'m-datatable__row'],
                'panel' => [
                    'heading' => '',
                    'type' => 'info',
                    'before' =>'<a href="/' . Yii::$app->request->getPathInfo() .'" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--air">
                                <span>
                                    <i class="la la-times-circle-o"></i>
                                    <span><?= '\' . Yii::t(\'yii\', \'Rimuovi filtri\') . \''?></span>
                                </span>
                            </a>',
                    'after' => '<a href="/' . Yii::$app->request->getPathInfo() .'" class="btn btn-brand m-btn m-btn--custom m-btn--icon m-btn--air">
                                <span>
                                    <i class="la la-times-circle-o"></i>
                                    <span><?= '\' . Yii::t(\'yii\', \'Rimuovi filtri\') . \''?></span>
                                </span>
                            </a>',
                    'showFooter' => false
                ],
                'toolbar'=>[],
                'panelTemplate'=>'<div class="panel {type}">
                    {panelBefore}
                    {panelHeading}
                    {items}
                    {panelFooter}
                    {panelAfter}
                </div>',
                'panelHeadingTemplate' => '
                        {heading}
                        {pager}
                    <div class="m-datatable__pager-info">
                        {summary}
                    </div>
                    <div class="clearfix"></div>',
                'panelFooterTemplate' => '
                        {pager}
                    <div class="m-datatable__pager-info">
                        {summary}
                    </div>
                    <div class="clearfix"></div>'
            ]); ?>
        </div>
    </div>
</div>
<?= '<?php
$this->registerJs(
        <<<JS
        function initPage(){
        
            $(\'.delete-button\').on(\'click\',function(e){
                e.preventDefault();
                var del=$(this).data(\'id\');
                swal({
                      title: "Siete sicuri di cancellare la riga selezionata?",
                      text: "",
                      type: "warning",
                      allowOutsideClick: false,
                      showConfirmButton: true,
                      showCancelButton: true,
                      confirmButtonText: "Si, sono sicuro",
                      cancelButtonText: "No",
                      animation: false, 
                      customClass: \'animated tada\'
                }).then(function(result){
                    mApp.blockPage({
                        overlayColor: \'#000000\',
                        type: \'loader\',
                        state: \'success\',
                        message: \'Attendere...\'
                    });
                    jQuery.ajax({
                        type: \'POST\',
                        url: \'/' .   $generator->controllerID .'/delete\',
                        data: {
                            id: del,
                        },
                        success: function() {
                            mApp.unblockPage();
                            swal("Completato!", "Riga cancellata correttamente!", "success");
                            jQuery.pjax.reload({container:\'#grid' .ucFirst(Inflector::camel2id(StringHelper::basename($generator->modelClass))) . '-pjax\'});
                        },
                        error: function() {
                            mApp.unblockPage();
                            swal("Si è verificato un errore", "Non è stato possibile cancellare la riga!", "error");
                            
                        }
                    });
                        
              })
               
            });
            
            $(\'#grid'.ucFirst(Inflector::camel2id(StringHelper::basename($generator->modelClass))) .'-pjax\').on(\'pjax:end\', function(){ initPage();  });
        }
        
        initPage();
JS

);';