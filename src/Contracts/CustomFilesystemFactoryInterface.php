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


namespace Jefferson49\Webtrees\Module\CustomFilesystem\Contracts;

use Fisharebest\Webtrees\Contracts\FilesystemFactoryInterface;


interface CustomFilesystemFactoryInterface extends FilesystemFactoryInterface {

    /**
     * Get the name of the filesystem factory
     *
     * @return string   The name of the filesystem factory.
     */
    public static function getName() : string;

    /**
     * Returns a list with options that can be passed to the filesystem factory
     *
     * @return array   An array of option names, which can be set for this filesystem factory.
     */
    public static function getRequiredOptions() : array;
}