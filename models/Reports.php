<?

namespace app\models;

use yii\base\Object;
use yii\db\Query;

class Reports extends Object
{
    /**
     * Получение результатов отчета
     * @return array
     */
    public function getList()
    {
        $arResult = (new Query)
            ->select('SONET_GROUP.ID as GROUP_ID,
                SONET_GROUP.NAME as GROUP_NAME,
                TASK.ID as TASK_ID,
			    TASK.TITLE as TASK_NAME,

                TIME.ID as TIME_ID,
                TIME.SECONDS as TIME_SECONDS,
                TIME.CREATED_DATE as CREATED_DATE,

                USER.NAME as USER_NAME,
                USER.LAST_NAME as USER_LAST_NAME,
                USER.SECOND_NAME as USER_SECOND_NAME'
            )
            ->from('b24_sonet_group SONET_GROUP')
            ->join('LEFT JOIN', 'b24_task TASK', 'SONET_GROUP.ID=TASK.GROUP_ID')
            ->join('LEFT JOIN', 'b24_time TIME', 'TASK.ID=TIME.TASK_ID')
            ->join('LEFT JOIN', 'b24_user USER', 'TIME.USER_ID=USER.ID')
            ->where('TIME.SECONDS IS NOT NULL')
            ->all();

        return $arResult;
    }
}
