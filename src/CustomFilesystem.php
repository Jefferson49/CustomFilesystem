<?php

/**
 * webtrees: online genealogy
 * Copyright (C) 2024 webtrees development team
 *                    <http://webtrees.net>
 *
 * CustomFileSystem (webtrees custom module):
 * Copyright (C) 2025 Markus Hemprich
 *                    <http://www.familienforschung-hemprich.de>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <https://www.gnu.org/licenses/>.
 *
 * 
 * CustomFilesystem
 *
 * A webtrees(https://webtrees.net) 2.1 custom module for advanced GEDCOM import, 
 * export and filter operations. The module also supports remote downloads/uploads via URL requests.
 * 
 */

declare(strict_types=1);

namespace Jefferson49\Webtrees\Module\CustomFilesystem;

use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;
use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Fisharebest\Webtrees\FlashMessages;
use Fisharebest\Webtrees\I18N;
use Fisharebest\Webtrees\Module\AbstractModule;
use Fisharebest\Webtrees\Module\ModuleCustomTrait;
use Fisharebest\Webtrees\Module\ModuleCustomInterface;
use Fisharebest\Webtrees\Module\ModuleGlobalInterface;
use Fisharebest\Webtrees\Module\ModuleGlobalTrait;
use Fisharebest\Webtrees\Registry;
use Fisharebest\Webtrees\Webtrees;

use ReflectionMethod;

use function file_exists;
use function parse_ini_file;


class CustomFilesystem extends AbstractModule implements ModuleCustomInterface, ModuleGlobalInterface
{
    use ModuleCustomTrait;
    use ModuleGlobalTrait;

    //All configured options from the webtrees config.ini.php file
    private static $webtrees_config = [];

    
    //Custom module version
    public const CUSTOM_VERSION = '1.0.0-alpha';
	//GitHub repository
	public const GITHUB_REPO = 'Jefferson49/CustomFilesystem';
	//Author of the custom module
    public const CUSTOM_AUTHOR = 'Markus Hemprich';


    /**
     * Bootstrap the module
     */
    public function boot(): void
    {
        //Create the custom file system
        $custom_filesystem_factory = $this->getFilesystemFactory();

        if ($custom_filesystem_factory !== null) {
            //Register the custom filesystem
            Registry::filesystem($custom_filesystem_factory);
            return;
        }

        //Register the default filesystem
        Registry::filesystem(new FilesystemFactory());
    }

    /**
     * How should this module be identified in the control panel, etc.?
     *
     * @return string
     */
    public function title(): string
    {
        return I18N::translate('Custom Filesystem');
    }

    /**
     * A sentence describing what this module does.
     *
     * @return string
     */
    public function description(): string
    {
        return I18N::translate('Create a custom filesystem');
    }

    /**
     * {@inheritDoc}
     * @see \Fisharebest\Webtrees\Module\ModuleCustomInterface::customModuleAuthorName()
     */
    public function customModuleAuthorName(): string
    {
        return self::CUSTOM_AUTHOR;
    }

    /**
     * {@inheritDoc}
     * @see \Fisharebest\Webtrees\Module\ModuleCustomInterface::customModuleVersion()
     */
    public function customModuleVersion(): string
    {
        return self::CUSTOM_VERSION;
    }

    /**
     * {@inheritDoc}
     * @see \Fisharebest\Webtrees\Module\ModuleCustomInterface::customModuleSupportUrl()
     */
    public function customModuleSupportUrl(): string
    {
        return 'https://github.com/' . self::GITHUB_REPO;
    }

    /**
     * Where does this module store its resources
     *
     * @return string
     */
    public function resourcesFolder(): string
    {
        return __DIR__ . '/resources/';
    }

    /**
     * Get the namespace for the views
     *
     * @return string
     */
    public static function viewsNamespace(): string
    {
        return self::activeModuleName();
    }    

    /**
     * Get the active module name, e.g. the name of the currently running module
     *
     * @return string
     */
    public static function activeModuleName(): string
    {
        return '_' . basename(dirname(__DIR__, 1)) . '_';
    }

    /**
     * Get the custom filesystem factory
     * 
     * @return FilesystemFactoryInterface   A configured filesystem factory. Null, if error.
     */
    public static function getFilesystemFactory() : ?FilesystemFactoryInterface
    {
        $filesystem_factory_names = self::getFilesystemFactoryNames();

        if (sizeof($filesystem_factory_names) !== 1) {
            return null;
        }
        $name = array_values($filesystem_factory_names)[0];

        $name_space = str_replace('\\\\', '\\',__NAMESPACE__ );
        $name_space .= '\\FilesystemFactories\\';
        $options = self::getProviderOptions($name);

        //If no options found
        if (sizeof($options) === 0) {
            return null;
        }

        foreach($filesystem_factory_names as $class_name => $filesystem_factory_name) {
            if ($filesystem_factory_name === $name) {
                $class_name = $name_space . $class_name;
                return new $class_name($options);
            }
        }

        //If no provider found
        return null;
    }

	/**
     * Return the names of all available filesystem factories
     *
     * @return array array<class_name => provider_name>
     */ 

    public static function getFilesystemFactoryNames(): array {

        $filesystem_factory_names = [];
        $name_space = str_replace('\\\\', '\\',__NAMESPACE__ );
        $name_space_factories = $name_space . '\\FilesystemFactories\\';
        $name_space_contracts = $name_space .'\\Contracts\\';

        foreach (get_declared_classes() as $class_name) { 
            if (strpos($class_name, $name_space_factories) !==  false) {
                if (in_array($name_space_contracts . 'CustomFilesystemFactoryInterface', class_implements($class_name))) {
                    $reflectionMethod = new ReflectionMethod($class_name, 'getName');
                    $class_name = str_replace($name_space_factories, '', $class_name);
                    $filesystem_factory_names[$class_name] = $reflectionMethod->invoke(null);    
                }
            }
        }

        return $filesystem_factory_names;
    }

	/**
     * Get the options of a provider
     * 
     * @param string $name  Authorization provider name
     * 
     * @return array        An array with the options. Empty if options could not be read completely.
     */ 

    public static function getProviderOptions(string $name): array {

        $options = [];
        $name_space = str_replace('\\\\', '\\',__NAMESPACE__ );
        $name_space_factories = $name_space . '\\FilesystemFactories\\';
        $filesystem_factory_names = self::getFilesystemFactoryNames();

        foreach ($filesystem_factory_names as $class_name => $factory_name) {
            if ($factory_name === $name) {
                $reflectionMethod = new ReflectionMethod($name_space_factories . $class_name, 'getRequiredOptions');
                $option_names = $reflectionMethod->invoke(null);
                break;
            }
        }

        // Get the configuration settings from the webtrees configutration
        $config = self::getWebtreesConfig();
        foreach ($config as $key => $value) {
            if (strpos($key, $name . '_') === 0) {
                $key = str_replace($name . '_', '', $key);
                $options[$key] = $value;
            }
        }

        //Return if no options found, i.e. the authorization provider is not configured
        if (sizeof($options) === 0) {
            return [];
        }

        //Check if configuration is complete, i.e. contains all required options
        foreach ($option_names as $option_name) {
            if (!key_exists($option_name, $options)) {
                FlashMessages::addMessage(I18N::translate('The configuration for the authorization provider "%s" does not include data for the option "%s". Please check the configuration in the following file: data/config.ini.php', $factory_name, $option_name), 'danger');
                return [];
            }
        }

        return $options;
    }

	/**
     * Get all options from the webtrees config.ini.php file
     * 
     * @param string $name  Filesytem name
     * 
     * @return array        An array with the options. Empty if options could not be read.
     */ 

    public static function getWebtreesConfig(): array {

        // If not already available, read the configuration settings from the webtrees config file
        if (self::$webtrees_config === [] && file_exists(Webtrees::CONFIG_FILE)) {
            self::$webtrees_config  = parse_ini_file(Webtrees::CONFIG_FILE);
        }

        return self::$webtrees_config;
    }

	/**
     * Get the value for a certain key in the webtrees configuration (from config.ini.php file)
     * 
     * @param string $key
     * 
     * @return string
     */ 

    public static function getConfigValue(string $key): string {

        if (isset(self::getWebtreesConfig()[$key])) {
            return self::getWebtreesConfig()[$key];
        } else {
            return '';
        }
    }
};
