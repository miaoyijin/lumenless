<?php


namespace App\Models;

use Laravel\Lumen\Application;
use Illuminate\Database\Eloquent\Model;

/**
 * Class BaseModel
 * @package infra\models
 *
 * @property string $create_time
 * @property string $update_time
 * @property string $is_delete
 *
 * @method void inject()
 */
class BaseModel extends Model
{
    public const CREATED_AT = 'create_time';
    public const UPDATED_AT = 'update_time';

    /**
     * BaseModel constructor.
     * @param array $attributes attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (method_exists($this, 'inject')) {
            Application::getInstance()->call([$this, 'inject']);
        }
    }

    /**
     * 根据日期获取分表名
     * @param null $date date
     * @return string
     */
    public function getTableNameByDate($date = null)
    {
        $date = $date ? $date : date("Y-m-d");
        return $this->table . '_' . date("Ymd", strtotime($date));
    }
}
