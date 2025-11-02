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

use Composer\Autoload\ClassLoader;


//Autoload this webtrees custom module
$loader = new ClassLoader(__DIR__);
$loader->addPsr4('Jefferson49\\Webtrees\\Module\\CustomFilesystem\\', __DIR__ . '/src');
$loader->register();

//Autoload vendor libraries
require_once __DIR__ . '/vendor/autoload.php';

return true;
