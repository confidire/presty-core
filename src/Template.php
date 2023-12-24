<?php
/*
 * +----------------------------------------------------------------------
 * | Presty Framework
 * +----------------------------------------------------------------------
 * | Copyright (c) 20021~2022 Confidire All rights reserved.
 * +----------------------------------------------------------------------
 * | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
 * +----------------------------------------------------------------------
 * | Email: 790455692@qq.com
 * +----------------------------------------------------------------------
 */

namespace presty;

class Template
{
    public function getTemplateContent ($fileName)
    {
        $path = TEMPLATES . $fileName . \presty\Container::getInstance ()->makeAndSave("config")->get('view.template_suffix');
        return file_get_contents ($path);
    }

    public function getTemplatePath ($fileName)
    {
        return TEMPLATES . $fileName . \presty\Container::getInstance ()->makeAndSave("config")->get('view.template_suffix');
    }
}