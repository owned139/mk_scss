<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = 'MK\MkScss\Hooks\PageRendererHook->RenderPreProcess';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'MK\MkScss\Hooks\CacheHook->clearCachePostProc';
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['additionalBackendItems']['cacheActions'][] = \MK\MkScss\Hooks\Backend\ToolbarItems\ClearScssCacheToolbarItem::class;

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
    $iconRegistry->registerIcon(
        'mkscss-ext-icon', 
        \TYPO3\CMS\Core\Imaging\IconProvider\BitmapIconProvider::class, 
        ['source' => 'EXT:mk_scss/Resources/Public/Icons/Extension.png']
    );
});
