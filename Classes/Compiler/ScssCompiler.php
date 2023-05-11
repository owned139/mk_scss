<?php
declare(strict_types=1);
namespace MK\MkScss\Compiler;
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

use TYPO3\CMS\Core\Utility\{GeneralUtility,PathUtility};
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\SingletonInterface;
use ScssPhp\ScssPhp\{Compiler,OutputStyle};

/**
 * Compiles scss files to css
 *
 * @author Michell Kalb <m.kalb@resch-media.de>
 */
class ScssCompiler implements SingletonInterface
{
    /**
     * @var Compiler
     */
    protected $compiler = null;

    /**
     * @var string
     */
    protected $sitePath = '';

    /**
     * @var string
     */
    protected $subPath = '';

    /**
     * @var string
     */
    protected $cacheDir = 'typo3temp/mk_scss/';

    /**
     * @var array
     */
    protected $hashCache = [];

    /**
     * @var array
     */
    protected $settings = [];

    /**
     * @var array
     */
    protected $importFilePaths = [];

    /**
     * Initialize
     */
    public function __construct()
    {
        $this->sitePath = Environment::getPublicPath() . '/';
        $this->subPath = rtrim(
            str_replace(
                GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT'), 
                '', 
                $this->sitePath
            ), 
            '/'
        );

        if (!empty($GLOBALS['TSFE']) && !empty($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_mkscss.']['settings.'])) {
            $this->settings = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_mkscss.']['settings.'];
            $this->settings['sourcemapType'] = $this->settings['sourcemapType'] ?? 'file';
            $this->settings['sourcemaps'] = $this->settings['sourcemaps'] ?? '0';
            $this->settings['cssFormatter'] = $this->settings['cssFormatter'] ?? 'Expanded';
        }

        $this->compiler = GeneralUtility::makeInstance(Compiler::class);
        $cssFormatter = strtolower((string)$this->settings['cssFormatter']);

        if ($cssFormatter === 'compressed') {
            $this->compiler->setOutputStyle(OutputStyle::COMPRESSED);
        } else {
            $this->compiler->setOutputStyle(OutputStyle::EXPANDED);
        }

        if ($this->settings['sourcemapType'] === 'file' && $this->settings['sourcemaps'] === '1') {
            $this->compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);
        } elseif ($this->settings['sourcemaps'] === '1') {
            $this->compiler->setSourceMap(Compiler::SOURCE_MAP_INLINE);
        }

        $tempPath = $this->sitePath . $this->cacheDir;

        if (!file_exists($tempPath)) {
            GeneralUtility::mkdir_deep(rtrim($tempPath, '/'));
        }
    }

    /**
     * Compiles the file and returns the new relative filepath
     * 
     * @param string $relFilePath
     * @return string
     */
    public function getCompiledFilename(string $relFilePath): string
    {
        if (PathUtility::isExtensionPath($relFilePath)) {
            // $absFilePath = GeneralUtility::getFileAbsFileName($relFilePath);
            $relFilePath = trim(PathUtility::getPublicResourceWebPath($relFilePath), '/');
        }

        $fileNameHashed = $this->getFilenameHashed($relFilePath) . '.css';
        $outFilePath = $this->cacheDir . $fileNameHashed;

        if ($this->compiledFileExpired($relFilePath, $outFilePath)) {
            $this->compile($relFilePath, $outFilePath);
        } elseif (!file_exists($this->sitePath . $outFilePath)) {
            throw new \Exception('SCSS file not compiled');
        }

        return '/' . $outFilePath;
    }

    /**
     * Returns the hashed filepath (crc32b)
     * 
     * @param string $path
     * @return string
     */
    protected function getFilenameHashed(string $path): string
    {
        if(empty($this->hashCache[$path])) {
            $this->hashCache[$path] = PathUtility::basename($path) . '-' . hash('crc32b', $path);
        }

        return $this->hashCache[$path];
    }

    /**
     * Compiles the given SCSS file to CSS
     * 
     * @param string $inFilePath
     * @param string $outFilePath
     * @return void
     */
    protected function compile(string $inFilePath, string $outFilePath): void
    {
        $inFileInfo = PathUtility::pathinfo($inFilePath);
        $absOutFilePath = $this->sitePath . $outFilePath;

        if (!in_array($inFileInfo['dirname'] . '/', $this->importFilePaths, true)) {
            $this->compiler->addImportPath($inFileInfo['dirname'] . '/');
        }

        if ($this->settings['sourcemapType'] === 'file' && $this->settings['sourcemaps'] === '1') {
            $this->compiler->setSourceMap(Compiler::SOURCE_MAP_FILE);
            $this->compiler->setSourceMapOptions([
                'sourceMapWriteTo'  => $absOutFilePath . '.map',
                'sourceMapURL'      => $this->subPath . '/' . $outFilePath . '.map',
                'sourceMapFilename' => $this->subPath . '/' . $outFilePath,
                'sourceMapBasepath' => $this->sitePath,
                'sourceRoot'        => $this->subPath . '/',
            ]);
        } elseif ($this->settings['sourcemaps'] === '1') {
            $this->compiler->setSourceMap(Compiler::SOURCE_MAP_INLINE);
        }

        $compiledResult = $this->compiler->compileString('@import "' . $inFileInfo['basename'] . '";');

        if ($this->settings['sourcemapType'] === 'file' && $this->settings['sourcemaps'] === '1') {
            GeneralUtility::writeFileToTypo3tempDir($absOutFilePath . '.map', $compiledResult->getSourceMap());
        }

        GeneralUtility::writeFileToTypo3tempDir(
            $absOutFilePath, 
            $this->fixCssPaths($compiledResult->getCss(), $inFilePath)
        );
    }

    /**
     * Fixes the url() paths
     *
     * @return string $content
     * @return string $relInFilePath
     * @return string
     */
    public function fixCssPaths(string $content, string $relInFilePath) : string
    {
        if (stripos($content, 'url') !== false) {
            $search = [];
            $replace = [];
            $inFilePath = '../../' . PathUtility::dirname($relInFilePath) . '/';
            preg_match_all('/url\\(\\s*["\']?(?!\\/)([^"\']+)["\']?\\s*\\)/iU', $content, $matches);

            foreach ($matches[1] as $key => $match) {
                $match = trim($match, '\'" ');
                if (strpos($match, ':') === false) {
                    $replace[] = GeneralUtility::resolveBackPath($inFilePath . $match);
                    $search[] = $match;
                }
            }

            if (!empty($replace)) {
                $content = str_replace($search, $replace, $content);
            }
        }

        return $content;
    }

    /**
     * Returns true if the compiled file doesent exists or the sourcefile changed
     *
     * @param string $inFilePath
     * @param string $outFilePath
     * @return bool
     */
    protected function compiledFileExpired(string $inFilePath, string $outFilePath): bool
    {
        $absInFilePath = $this->sitePath . $inFilePath;
        $absOutFilePath = $this->sitePath . $outFilePath;

        return (!file_exists($absOutFilePath) || filemtime($absInFilePath) > filemtime($absOutFilePath));
    }
}
