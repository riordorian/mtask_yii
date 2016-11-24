<?

namespace app\models;

use yii\base\Object;
use yii\db\Query;

class Loader extends Object
{
    public $obIntegration;
    public $groupsTable = 'b24_sonet_group';
    public $tasksTable = 'b24_task';
    public $timeTable = 'b24_time';
    public $usersTable = 'b24_user';


    /**
     * Loader constructor.
     */
    public function __construct()
    {
        $this->obIntegration = new Integration();
    }


    /**
     * Получение группы портала
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getGroups()
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        $arGroups = $this->obIntegration->call('sonet_group.get',
            array(
                'auth' => $this->obIntegration->arSettings['access_token'],
            )
        );

        /*
         * Записываем в базу полученныую информацию по группам
         * */
        $arGroups = $this->save($this->groupsTable, array('ID', 'SITE_ID', 'NAME', 'ACTIVE'), $arGroups);

        return $arGroups;
    }


    /**
     * Получет задачи по группам
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getTasks()
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        /*
         * Собираем информацию о задачах
         * */
        //	TODO: Не работает ограничение выборки. Нужно разобраться
        $arTasks = $this->obIntegration->call('task.item.list',
            array(
                'ORDER' => array('TITLE' => 'asc'),
                'FILTER' => array(0 => ''),
                'PARAMS' => array(0 => ''),
                'SELECT' => array('ID', 'TITLE', 'GROUP_ID', 'RESPONSIBLE_ID', 'DURATION_FACT'),
                'auth' => $this->obIntegration->arSettings['access_token']
            )
        );

        /*
         * Записываем в базу полученныую информацию по группам
         * */
        $arTasks = $this->save($this->tasksTable, array('ID', 'TITLE', 'GROUP_ID', 'RESPONSIBLE_ID', 'DURATION_FACT'), $arTasks);

        return $arTasks;
    }


    /**
     * Получает списание времени по задаче
     *
     * @param $taskId - ID задачи
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getTime($taskId)
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }
        if( empty($taskId) ){
            throw new \Exception('Empty tasks ids array');
        }

        /*
         * Собираем информацию о списанном времени
         * */
        $arTimes = $this->obIntegration->call('task.elapseditem.getlist',
            array(
                'auth' => $this->obIntegration->arSettings['access_token'],
                'TASK_ID' => $taskId
            )
        );

        /*
         * Записываем в базу полученныую информацию по группам
         * */
        $arTimes = $this->save($this->timeTable, array('ID', 'TASK_ID', 'SECONDS', 'USER_ID', 'CREATED_DATE'), $arTimes);

        return $arTimes;
    }


    /**
     * Получение пользователей портала
     *
     * @param array $arUserIds - ID пользователей
     *
     * @return array|mixed
     * @throws \Exception
     */
    public function getUsers($arUserIds = [])
    {
        if( empty($this->obIntegration->arSettings['access_token']) ){
            throw new \Exception('Incorrect access token');
        }

        /*
         * Собираем информацию о пользователях
         * */
        $arUsers = $this->obIntegration->call('user.get.json',
            array(
                'auth' => $this->obIntegration->arSettings['access_token'],
			    'ID' => $arUserIds
            )
        );

        /*
         * Записываем в базу полученныую информацию по группам
         * */
        $arUsers = $this->save($this->usersTable, array('ID', 'EMAIL', 'NAME', 'LAST_NAME', 'SECOND_NAME'), $arUsers);

        return $arUsers;
    }


    /**
     * Метод добавляет новую строку с данными в таблицу БД или обновляет имеющуюся
     *
     * @param $entity - Название таблицы
     * @param $arAvailableFields - Массив допустимых полей для записи в БД
     * @param $arElements - Массив записываемых элементов
     *
     * @return array
     */
    protected function save($entity, $arAvailableFields, $arElements)
    {
        $arResult = $arExElems = [];

        $command = \Yii::$app->db->createCommand();

        /*
         * Получаем уже имеющиеся записи
         * */
        $arExistenceElems = (new Query())->select('ID')->from($entity)->all();
        foreach($arExistenceElems as $k => &$arExistenceElem){
            $arExElems[$arExistenceElem['ID']] = [];
        }

        foreach($arElements['result'] as $arElement){
            try{
                $arRow = [];
                foreach($arAvailableFields as $fieldName){
                    if( is_null($arElement[$fieldName]) ){
                        $arElement[$fieldName] = 0;
                    }
                    $arRow[$fieldName] = $arElement[$fieldName];
                }

                if( array_key_exists($arRow['ID'], $arExElems) ){
                    $elId = $arElement['ID'];
                    unset($arRow['ID']);
                    if( $command->update($entity, $arRow, 'ID=:id', array(':id' => $elId))->execute() ){
                        $arResult['UPDATED'][$arElement['ID']] = true;
                    }
                }
                elseif( $command->insert($entity, $arRow)->execute() ){
                    $arResult['INSERTED'][$arElement['ID']] = true;
                }
            }
            catch(\Exception $e){
                ?><pre><?print_r($e->getMessage())?></pre><?
            }

            $arResult['ITEMS'][$arElement['ID']] = array();
        }

        return $arResult;
    }
}
