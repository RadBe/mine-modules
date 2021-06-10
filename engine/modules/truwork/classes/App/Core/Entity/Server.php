<?php


namespace App\Core\Entity;


use App\Core\Models\ServersModel;

/**
 * @property int $id
 * @property string $name
 * @property bool $enabled
 * @property string $ip
 * @property int $query_port
 * @property string $version
 * @property string|null $plugin_permissions
 * @property string|null $plugin_g_money
 */
class Server extends DatabaseEntity
{
    /**
     * @inheritdoc
     */
    protected $fillable = [
        'name', 'enabled', 'ip', 'query_port', 'version', 'plugin_permissions', 'plugin_g_money'
    ];

    /**
     * @inheritdoc
     */
    protected $casts = [
        'enabled' => 'bool'
    ];

    /**
     * @inheritdoc
     */
    protected $hidden = [
        'ip', 'query_port', 'plugin_permissions', 'plugin_g_money'
    ];

    /**
     * @param Server|int $server
     * @return string
     */
    public static function getIcon($server): string
    {
        if ($server instanceof Server) {
            $server = $server->id;
        }

        $dir = ServersModel::ICON_DIR;
        $file = "$dir/{$server}.png";
        if (is_file(ROOT_DIR . '/' . $file)) {
            return $file;
        }

        return "$dir/default.png";
    }
}
