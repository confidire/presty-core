<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Tomanday All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace ModuleGuides;

class Database
{
    public function register (\presty\Core $app)
    {
        if(get_config ("env.database_auto_load")) {
            $app->database = new \presty\Database(
                env ('database.type', "mysql"),
                env ('database.host', "127.0.0.1"),
                env ('database.name', ""),
                env ('database.user', "rott"),
                env ('database.pass', ""),
                env ('database.port', "3306"),
                env ('database.prefix', ""),
                env ('database.file', ""),
                env ('database.table', "")
            );
        }
    }
}