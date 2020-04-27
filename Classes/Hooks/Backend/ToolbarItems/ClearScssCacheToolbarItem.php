<?php
declare(strict_types=1);
namespace MK\MkScss\Hooks\Backend\ToolbarItems;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2020 Michell Kalb
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Backend\Toolbar\ClearCacheActionsHookInterface;
use TYPO3\CMS\Backend\Routing\UriBuilder;

/**
 * @author Michell Kalb <m.kalb@resch-media.de>
 */
class ClearScssCacheToolbarItem implements ClearCacheActionsHookInterface
{
    /**
     * @var string
     */
    protected $key = 'mkscss';

    /**
     * Modifies CacheMenuItems array
     *
     * @param array $cacheActions
     * @param array $optionValues
     * @return void
     */
    public function manipulateCacheActions(&$cacheActions, &$optionValues): void
    {
        $uriBuilder = GeneralUtility::makeInstance(UriBuilder::class);

        $optionValues[] = $this->key;
        $cacheActions[] = [
            'id' => $this->key,
            'title' => 'LLL:EXT:mk_scss/Resources/Private/Language/locallang.xlf:backend.clear_cache.title',
            'description' => 'LLL:EXT:mk_scss/Resources/Private/Language/locallang.xlf:backend.clear_cache.description',
            'href' => (string)$uriBuilder->buildUriFromRoute(
                'tce_db', 
                [
                    'cacheCmd' => $this->key, 
                    'ajaxCall' => 1
                ]
            ),
            'iconIdentifier' => 'mkscss-ext-icon'
        ];
    }
}
