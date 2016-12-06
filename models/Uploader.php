<?

namespace app\models;

use yii\base\Object;
use yii\db\Query;

class Uploader extends Object
{
    public $obIntegration;

    /**
     * Uploader constructor.
     */
    public function __construct()
    {
        $this->obIntegration = new Integration();
    }


    /**
     * Add 10 test groups to portal
     *
     * @return array
     * @throws \Exception
     */
    public function addGroups()
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        $arResult = [];
        for($i = 0; $i < 10; $i++){
            $arResult[] = $this->obIntegration->call('sonet_group.create',
                [
                    'NAME' => 'Test sonet group ' . $i,
                    'VISIBLE' => 'Y',
                    'OPENED' => 'Y',
                    'INITIATE_PERMS' => 'K',
                    'auth' => $this->obIntegration->arSettings['access_token'],
                ]
            );
        }

        return $arResult;
    }

    public function addTasks()
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        $arResult = [];

        $arGroups = $this->obIntegration->call('sonet_group.get',
            array(
                'auth' => $this->obIntegration->arSettings['access_token'],
            )
        );
        
        foreach($arGroups['result'] as $arGroup){
            if( strpos($arGroup['NAME'], 'Test') === false ){
                continue;
            }

            for($i = 0; $i < 1000; $i++ ){
                sleep(1);
                $arTask = $arResult[] = $this->obIntegration->call('task.item.add',
                    [
                        'TASKDATA' => [
                            'TITLE' => 'Test task ' . $i . ' in group ' . $arGroup['NAME'],
                            'DESCRIPTION' => 'Lorem ipsum',
                            'RESPONSIBLE_ID' => 18,
                            'GROUP_ID' => $arGroup['ID']
                        ],
                        'auth' => $this->obIntegration->arSettings['access_token']
                    ]
                );
                
                ?><pre><?print_r($arTask)?></pre><?
            }
        }

        return $arResult;
    }


    public function addTime()
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        $arResult = [];

        $arTasks = [];
        for($i = 1; $i < 5; $i++){
            $arCurTasks = $this->obIntegration->call('task.item.list',
                array(
                    'ORDER' => ['TITLE' => 'asc'],
                    'FILTER' => [0 => ''],
                    'PARAMS' => [
                        'NAV_PARAMS' => [
                            "nPageSize" => 50,
                            'iNumPage' => $i
                        ]
                    ],
                    'SELECT' => ['ID', 'TITLE', 'GROUP_ID', 'RESPONSIBLE_ID', 'DURATION_FACT'],
                    'auth' => $this->obIntegration->arSettings['access_token']
                )
            );
            $arTasks = array_merge($arTasks, $arCurTasks['result']);
        }


        foreach($arTasks as $arTask){
            if( strpos($arTask['TITLE'], 'Test') !== false ){
                for($i = 0; $i < 10; $i++){
                    sleep(1);
                    $arTime = $arResult[] = $this->obIntegration->call('task.elapseditem.add', [
                        'TASKID' => $arTask['ID'],
                        'ARFIELDS' => [
                            'SECONDS' => $i,
                            'COMMENT_TEXT' => 'текст комментария',
                            'CREATED_DATE' => '2016-01-20 17:26:37',
                        ],
                        'auth' => $this->obIntegration->arSettings['access_token']
                    ]);
                    ?><pre><?print_r($i)?></pre><?
                }
            }
        }
    }

}
