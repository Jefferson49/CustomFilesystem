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


namespace Jefferson49\Webtrees\Module\CustomFilesystem\FilesystemFactories;

use Fisharebest\Webtrees\Factories\FilesystemFactory;
use Jefferson49\Webtrees\Module\CustomFilesystem\Contracts\CustomFilesystemFactoryInterface;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use Marktaborosi\FlysystemNextcloud\NextCloudAdapter;


class Nextcloud extends FilesystemFactory implements CustomFilesystemFactoryInterface {

    private string $user_name;
    private string $password;
    private string $nextcloud_base_url;
    private string $folder_name;
    private bool   $configured = false;


    /**
     * Constructor
     *
     * @param array $options
     */
    public function __construct(array $options) {

        foreach (self::getRequiredOptions() as $option_key) {
            if (!isset($options[$option_key])) {
                $this->configured = false;
                return;
            }
        }

        $this->nextcloud_base_url = $options['nextcloudUrl'];
        $this->folder_name        = $options['folderName'];
        $this->user_name          = $options['userName'];
        $this->password           = $options['password'];
        $this->configured         = true;
    }

    /**
     * Create a filesystem for a custom folder.
     *
     * @param string $path_prefix
     *
     * @return FilesystemOperator
     */
    public function data(string $path_prefix = ''): FilesystemOperator
    {

        $adapter = new NextCloudAdapter([
            'baseUri'  => $this->nextcloud_base_url . '/remote.php/dav/files/' . $this->user_name . '/' . $this->folder_name . '/',
            'userName' => $this->user_name,
            'password' => $this->password,
        ]);

        if ($path_prefix !== '') {
            $adapter = new PathPrefixedAdapter($adapter, $path_prefix);
        }

        return new Filesystem($adapter);
    }

    /**
     * Get the name of the filesystem factory
     *
     * @return string   The name of the filesystem factory.
     */
    public static function getName() : string
    {
        return 'Nextcloud';
    }

    /**
     * Describe a filesystem for a custom folder.
     *
     * @return string
     */
    public function dataName(): string
    {
        return $this->nextcloud_base_url . '/remote.php/dav/files/' . $this->user_name . '/' . $this->folder_name . '/';
    }

    /**
     * Returns a list with options that need to be passed to this filesystem factory
     *
     * @return array   An array of option names, which can be set for this filesystem factory.
     */
    public static function getRequiredOptions() : array
    {
        return [
            'userName',
            'password',
            'nextcloudUrl',
            'folderName',
        ];
    }

    /**
     * Whether this filesystem factory is configured properly
     *
     * @return array   An array of option names, which can be set for this filesystem factory.
     */
    public function isConfigured() : bool {

        return $this->configured;
    }    
}