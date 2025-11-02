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

use Fisharebest\Webtrees\Factories\FilesystemFactory;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemOperator;
use League\Flysystem\PathPrefixing\PathPrefixedAdapter;
use Marktaborosi\FlysystemNextcloud\NextCloudAdapter;


class CustomFilesystemFactory extends FilesystemFactory {

    const USERNAME           = 'USERNAME';
    const PASSWORD           = 'PASSWORD';
    const NEXTCLOUD_BASE_URL = 'https://nextcloud.DOMAIN';
    const FOLDER_NAME        = 'webtrees';


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
            'baseUri'  => self::NEXTCLOUD_BASE_URL . '/remote.php/dav/files/' . self::USERNAME . '/' . self::FOLDER_NAME . '/',
            'userName' => self::USERNAME,
            'password' => self::PASSWORD,
        ]);

        if ($path_prefix !== '') {
            $adapter = new PathPrefixedAdapter($adapter, $path_prefix);
        }

        return new Filesystem($adapter);
    }

    /**
     * Describe a filesystem for a custom folder.
     *
     * @return string
     */
    public function dataName(): string
    {
        return self::NEXTCLOUD_BASE_URL . '/remote.php/dav/files/' . self::USERNAME . '/' . self::FOLDER_NAME . '/';
    }
}