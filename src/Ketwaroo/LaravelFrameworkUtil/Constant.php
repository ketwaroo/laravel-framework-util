<?php

/**
 * @copyright (c) 2014, 3C Institute
 */

namespace Ketwaroo\LaravelFrameworkUtil;

/**
 * Description of Constant
 *
 * @author Yaasir Ketwaroo <ketwaroo@3cisd.com>
 */
class Constant
{

    /**
     * Specify the prefix for the package tables.
     */
    const CONFIGKEY_TABLE_PREFIX = 'stringTablePrefix';

    /**
     * 
     */
    const CONFIGKEY_AUTOLOAD_PACKAGES = 'boolAutoloadPackage';

    /**
     * 
     */
    const CONFIGKEY_AUTOLOAD_PACKAGES_LIST = 'listAutoloadPackage';

    /**
     * 
     */
    const CONFIGKEY_DEV_ASSET_ROUTE = 'devAssetRoute';

    /**
     * 
     */
    const CONFIGKEY_PACKAGE_AUTOPUBLISH = 'boolPackageAutopublishAssets';

    /**
     * 
     */
    const CONFIGKEY_RESOURCEURI_DEFAULTSCHEMA = 'strResourceUriDefaultSchemaHandler';

    /**
     * 
     */
    const RESOURCEURI_SCHEMA_ASSET = 'asset';

    /**
     * 
     */
    const RESOURCEURI_DEFAULTSCHEMA = 'asset';

    /**
     * filter is fired just before action is called in a cccisd/framework controller class.
     * filters the request segments. filter callback takes payload->subject = \Request::getSegments(),
     *  second param = current controller segment,
     *  third param  = current action segment
     */
    const FILTERCONTENT_CONTROLLER_REQUESTSEGMENT = 'cccisd.controller.requestsegment';

    /**
     * 
     */
    const EVENT_CONTROLLER_INIT = 'cccisd.controller.init';

}
