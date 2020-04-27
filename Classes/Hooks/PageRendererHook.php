<?php
declare(strict_types=1);
namespace MK\MkScss\Hooks;
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

use MK\MkScss\Compiler\ScssCompiler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Hook to compile scss to css
 *
 * @author Michell Kalb <m.kalb@resch-media.de>
 */
class PageRendererHook
{
    /**
     * @var ScssCompiler
     */
    protected $scssCompiler = null;

    /**
     * Checks for scss files and replaces them with the compiled css file
     * 
     * @param array &$params
     * @param PageRenderer &$pagerenderer
     * @return void
     */
    public function RenderPreProcess(array &$params, PageRenderer &$pagerenderer): void
    {
        if (!empty($params['cssFiles'])) {
            $cssFiles = [];

            if ($this->scssCompiler === null) {
                $this->scssCompiler = GeneralUtility::makeInstance(ScssCompiler::class);
            }

            foreach ($params['cssFiles'] as $key => $fileConf) {
                if (!empty($fileConf['file']) && strtolower(substr($fileConf['file'], -4)) === 'scss') {
                    $fileConf['file'] = $this->scssCompiler->getCompiledFilename($fileConf['file']);
                    $cssFiles[$fileConf['file']] = $fileConf;
                } else {
                    $cssFiles[$key] = $fileConf;
                }
            }

            $params['cssFiles'] = $cssFiles;
        }
    }
}
