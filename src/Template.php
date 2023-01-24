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

namespace presty;

class Template
{
    public function getTemplateContent ($fileName, $returnContent = true)
    {
        $path = TEMPLATES . $fileName . config('template_suffix');
        if ($returnContent) return file_get_contents ($path);
        else {
            return file_get_contents ($path);
        }
    }
}