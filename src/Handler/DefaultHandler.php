<?php namespace Anomaly\AddonFieldType\Handler;

use Anomaly\AddonFieldType\AddonFieldType;
use Anomaly\Streams\Platform\Addon\AddonCollection;
use Anomaly\Streams\Platform\Addon\Extension\ExtensionCollection;

/**
 * Class DefaultHandler
 *
 * @link          http://pyrocms.com/
 * @author        PyroCMS, Inc. <support@pyrocms.com>
 * @author        Ryan Thompson <ryan@pyrocms.com>
 * @package       Anomaly\AddonFieldType\Handler
 */
class DefaultHandler
{

    /**
     * Handle the options.
     *
     * @param AddonFieldType $fieldType
     */
    public function handle(AddonFieldType $fieldType, AddonCollection $addons)
    {
        // Restrict to type if desired.
        if ($type = $fieldType->config('type')) {
            $addons = $addons->{$type = snake_case(str_plural($type))}();
        }

        // Search extensions if desired.
        if ($addons instanceof ExtensionCollection && $search = $fieldType->config('search')) {
            $addons = $addons->search($search);
        }

        // Enabled only if extension or module.
        if (in_array($type, ['modules', 'extensions']) && $fieldType->config('enabled', true) == true) {
            $addons = $addons->enabled();
        }

        // Installed only if extension or module.
        if (in_array($type, ['modules', 'extensions']) && $fieldType->config('installed', true) == true) {
            $addons = $addons->installed();
        }

        // Limit to theme type if desired.
        if (in_array($type, ['themes']) && $type = $fieldType->config('theme_type')) {
            $addons = $addons->{$type}();
        }

        $fieldType->setOptions($addons->lists('name', 'namespace')->all());
    }
}