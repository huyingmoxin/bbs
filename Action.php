<?php
namespace app\admin\controller;

class Action extends Common
{
    /**
     * 辅助方法，过滤后台前台行为
     * @return [type] [description]
     */
    public function _filter(&$condition)
    {
        $type = input('type');

        $condition['type'] = empty($type) ? 0 : $type;
dvdfvdfvkf
    }
---------------------
    /**
     * 得到添加编辑的弹出框
     */
    public function getModel()
    {
        $id = input('id', '');
        $actions = input('actions');
        $model =model('Action');
        $data = $model->getBox($actions, $id);
        if ($data) {
            result(1, $data);
        } else {
            result(2,'','未能连接成功,请稍后再试');
        }
    }
}
