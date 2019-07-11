<?php
namespace app\admin\controller;

class ActionLog extends Common
{
    /**
     * 辅助方法，过滤后台前台日志
     * @return [type] [description]
     */
    public function _filter(&$condition)
    {
        $type = input('type');

        $condition['type'] = empty($type) ? 0 : $type;
    }
}
